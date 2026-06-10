<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Payment\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BillingController extends Controller
{
    public function __construct(private MidtransService $midtrans)
    {
    }

    /**
     * Generate tagihan (QRIS/payment link) untuk sebuah project.
     * Dipanggil via AJAX dari tombol "Tagih Klien". Mengembalikan JSON
     * berisi payment_url + link WhatsApp siap kirim.
     */
    public function charge(Request $request, Project $project): JsonResponse
    {
        $project->loadMissing('client');

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:1',
        ]);

        // Default: sisa tagihan project
        $amount = (float) ($validated['amount'] ?? $project->remaining_amount);

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Project ini sudah lunas, tidak ada sisa tagihan.',
            ], 422);
        }

        if ($amount > $project->remaining_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal melebihi sisa tagihan (' . $project->formatted_remaining_amount . ').',
            ], 422);
        }

        try {
            $paymentRequest = $this->midtrans->createCharge($project, $amount);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tagihan: ' . $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat.',
            'data' => [
                'order_id' => $paymentRequest->order_id,
                'amount' => $paymentRequest->amount,
                'formatted_amount' => $paymentRequest->formatted_amount,
                'payment_url' => $paymentRequest->payment_url,
                'whatsapp_url' => $this->buildWhatsappUrl($project, $paymentRequest->payment_url, $amount),
            ],
        ]);
    }

    /**
     * Susun link WhatsApp berisi pesan tagihan + link bayar.
     */
    private function buildWhatsappUrl(Project $project, ?string $paymentUrl, float $amount): ?string
    {
        if (!$paymentUrl || !$project->client || !$project->client->phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $project->client->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = "Halo {$project->client->name},\n\n"
            . "Berikut tagihan untuk project *{$project->title}*\n"
            . 'Nominal: Rp ' . number_format($amount, 0, ',', '.') . "\n\n"
            . "Silakan bayar melalui link/QRIS berikut:\n{$paymentUrl}\n\n"
            . 'Terima kasih.';

        return "https://api.whatsapp.com/send?phone={$phone}&text=" . rawurlencode($message);
    }
}
