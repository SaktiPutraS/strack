<?php

namespace App\Services\Payment;

use App\Models\PaymentRequest;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Integrasi Midtrans Snap API.
 * Pakai HTTP client bawaan Laravel (tanpa SDK eksternal) supaya tidak menambah
 * dependency. Mendukung mode sandbox & production lewat config services.midtrans.
 */
class MidtransService
{
    private string $serverKey;
    private bool $isProduction;
    private int $expiryHours;

    public function __construct()
    {
        $this->serverKey = (string) config('services.midtrans.server_key');
        $this->isProduction = (bool) config('services.midtrans.is_production', false);
        $this->expiryHours = (int) config('services.midtrans.expiry_hours', 24);
    }

    private function snapBaseUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Buat transaksi Snap untuk sebuah project dan simpan sebagai PaymentRequest.
     * Mengembalikan PaymentRequest yang berisi payment_url (redirect_url Snap).
     */
    public function createCharge(Project $project, float $amount): PaymentRequest
    {
        if (empty($this->serverKey)) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum diatur di .env');
        }

        $orderId = $this->buildOrderId($project);
        $grossAmount = (int) round($amount); // Midtrans hanya menerima bilangan bulat (IDR)
        $expiredAt = Carbon::now()->addHours($this->expiryHours);

        $project->loadMissing('client');

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => [[
                'id' => 'PRJ-' . $project->id,
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => mb_substr('Pembayaran: ' . $project->title, 0, 50),
            ]],
            'customer_details' => [
                'first_name' => $project->client->name ?? 'Klien',
                'email' => $project->client->email ?? null,
                'phone' => $project->client->phone ?? null,
            ],
            'expiry' => [
                'unit' => 'hour',
                'duration' => $this->expiryHours,
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($this->snapBaseUrl(), $payload);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Gagal membuat transaksi Midtrans: ' . $response->status() . ' ' . $response->body()
            );
        }

        $data = $response->json();

        return PaymentRequest::create([
            'project_id' => $project->id,
            'order_id' => $orderId,
            'gateway' => 'midtrans',
            'amount' => $amount,
            'status' => 'PENDING',
            'payment_url' => $data['redirect_url'] ?? null,
            'snap_token' => $data['token'] ?? null,
            'expired_at' => $expiredAt,
            'raw_response' => $data,
        ]);
    }

    /**
     * order_id unik & mudah dilacak: STRACK-{projectId}-{timestamp}
     */
    private function buildOrderId(Project $project): string
    {
        return 'STRACK-' . $project->id . '-' . now()->format('YmdHis');
    }

    /**
     * Verifikasi signature webhook Midtrans:
     * SHA512(order_id + status_code + gross_amount + serverKey)
     */
    public function verifySignature(array $payload): bool
    {
        $expected = hash('sha512',
            ($payload['order_id'] ?? '')
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . $this->serverKey
        );

        return hash_equals($expected, $payload['signature_key'] ?? '');
    }

    /**
     * Petakan transaction_status Midtrans ke status PaymentRequest internal.
     */
    public function mapStatus(array $payload): string
    {
        $trx = $payload['transaction_status'] ?? '';
        $fraud = $payload['fraud_status'] ?? 'accept';

        return match ($trx) {
            'capture' => $fraud === 'accept' ? 'PAID' : 'PENDING',
            'settlement' => 'PAID',
            'pending' => 'PENDING',
            'deny', 'failure' => 'FAILED',
            'cancel' => 'CANCELLED',
            'expire' => 'EXPIRED',
            default => 'PENDING',
        };
    }
}
