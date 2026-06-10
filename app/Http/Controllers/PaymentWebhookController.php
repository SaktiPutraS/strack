<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Services\Payment\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(private MidtransService $midtrans)
    {
    }

    /**
     * Terima notifikasi pembayaran dari Midtrans.
     * Route ini di luar auth, jadi keamanan bergantung pada verifikasi signature.
     */
    public function midtrans(Request $request): JsonResponse
    {
        $payload = $request->all();

        // 1) Verifikasi signature
        if (!$this->midtrans->verifySignature($payload)) {
            Log::warning('Midtrans webhook: signature tidak valid', ['order_id' => $payload['order_id'] ?? null]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = $payload['order_id'] ?? null;
        $paymentRequest = PaymentRequest::where('order_id', $orderId)->first();

        if (!$paymentRequest) {
            Log::warning('Midtrans webhook: order_id tidak dikenal', ['order_id' => $orderId]);
            // 200 supaya Midtrans tidak retry terus untuk order yang memang bukan milik kita
            return response()->json(['message' => 'Order not found']);
        }

        $newStatus = $this->midtrans->mapStatus($payload);

        // 2) Idempotent: kalau sudah PAID, jangan proses ulang
        if ($paymentRequest->status === 'PAID') {
            return response()->json(['message' => 'Already processed']);
        }

        // 3) Proses dalam transaksi + lock baris untuk cegah race condition
        DB::transaction(function () use ($paymentRequest, $payload, $newStatus) {
            /** @var PaymentRequest $locked */
            $locked = PaymentRequest::whereKey($paymentRequest->id)->lockForUpdate()->first();

            if ($locked->status === 'PAID') {
                return; // sudah diproses thread lain
            }

            $locked->status = $newStatus;
            $locked->gateway_ref = $payload['transaction_id'] ?? $locked->gateway_ref;
            $locked->raw_response = $payload;

            if ($newStatus === 'PAID') {
                $locked->paid_at = now();
                $this->recordPayment($locked, $payload);
            }

            $locked->save();
        });

        return response()->json(['message' => 'OK']);
    }

    /**
     * Catat pembayaran (Payment) saat tagihan lunas.
     * Boot model Payment otomatis update paid_amount & payment_status project.
     */
    private function recordPayment(PaymentRequest $paymentRequest, array $payload): void
    {
        $project = $paymentRequest->project;
        $priorPaid = (float) $project->payments()->sum('amount');
        $amount = (float) $paymentRequest->amount;

        $type = match (true) {
            ($priorPaid + $amount) >= (float) $project->total_value => 'FINAL',
            $priorPaid <= 0 => 'DP',
            default => 'INSTALLMENT',
        };

        Payment::create([
            'project_id' => $project->id,
            'amount' => $amount,
            'payment_type' => $type,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'QRIS (Midtrans)',
            'notes' => 'Pembayaran otomatis via Midtrans. Order: ' . $paymentRequest->order_id
                . '. Channel: ' . ($payload['payment_type'] ?? '-'),
        ]);
    }
}
