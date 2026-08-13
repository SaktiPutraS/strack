<?php

namespace App\Services\Ai\Actions;

use App\Models\BankBalance;
use App\Models\BankTransfer;
use App\Models\Payment;
use RuntimeException;

/**
 * Tandai pembayaran (yang belum ditransfer) sebagai sudah masuk Bank Octo.
 * Bisa untuk SATU proyek tertentu, atau SEMUA pembayaran yang belum ditransfer
 * bila proyek tidak disebut. Membuat BankTransfer per pembayaran lalu memperbarui saldo.
 */
class CatatTransferBankAction extends WriteAction
{
    use ResolvesProject;

    public function name(): string
    {
        return 'catat_transfer_bank';
    }

    public function toolDefinition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Catat bahwa pembayaran sudah DITRANSFER masuk ke rekening Bank Octo. '
                . 'Jika user menyebut proyek tertentu, isi "proyek". Jika user ingin mentransfer SEMUA '
                . 'pembayaran yang belum ditransfer (tanpa menyebut proyek tertentu), KOSONGKAN "proyek". '
                . 'Tanggal opsional (default hari ini) - jangan tanya tanggal kecuali user menyebutkannya.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'proyek' => ['type' => 'string', 'description' => 'Nama proyek/klien. Kosongkan untuk SEMUA pembayaran yang belum ditransfer.'],
                    'tanggal' => ['type' => 'string', 'description' => 'Tanggal transfer Y-m-d, default hari ini.'],
                    'referensi' => ['type' => 'string', 'description' => 'Nomor referensi transfer (opsional).'],
                ],
                'required' => [],
            ],
        ];
    }

    public function prepare(array $input): array
    {
        $proyek = trim((string) ($input['proyek'] ?? ''));

        if ($proyek !== '') {
            // Mode satu proyek.
            $project = $this->resolveProject($proyek);
            $payments = $project->payments()->where('is_transferred', false)->get();
            $scope = "proyek \"{$project->title}\"";

            if ($payments->isEmpty()) {
                throw new RuntimeException("Tidak ada pembayaran {$scope} yang belum ditransfer.");
            }
        } else {
            // Mode semua pembayaran belum ditransfer.
            $payments = Payment::where('is_transferred', false)->get();
            $scope = 'semua pembayaran yang belum ditransfer';

            if ($payments->isEmpty()) {
                throw new RuntimeException('Tidak ada pembayaran yang belum ditransfer.');
            }
        }

        return [
            'scope' => $scope,
            'payment_ids' => $payments->pluck('id')->all(),
            'total' => (int) $payments->sum('amount'),
            'jumlah_pembayaran' => $payments->count(),
            'tanggal' => $this->parseDate($input['tanggal'] ?? null),
            'referensi' => trim((string) ($input['referensi'] ?? '')) ?: null,
        ];
    }

    public function preview(array $p): string
    {
        return "Catat transfer ke Bank Octo:\n"
            . "- Untuk: {$p['scope']}\n"
            . "- {$p['jumlah_pembayaran']} pembayaran, total {$this->rp($p['total'])}\n"
            . ($p['referensi'] ? "- Referensi: {$p['referensi']}\n" : '')
            . "- Tanggal: {$p['tanggal']}\n\n"
            . "Tandai sudah ditransfer? Balas *ya* atau *tidak*.";
    }

    public function execute(array $p): string
    {
        $payments = Payment::whereIn('id', $p['payment_ids'])
            ->where('is_transferred', false)
            ->get();

        if ($payments->isEmpty()) {
            throw new RuntimeException('Pembayaran sudah ditransfer sebelumnya.');
        }

        foreach ($payments as $payment) {
            BankTransfer::create([
                'payment_id' => $payment->id,
                'transfer_date' => $p['tanggal'],
                'transfer_amount' => $payment->amount,
                'reference_number' => $p['referensi'],
                'notes' => 'Via bot Telegram',
            ]);
        }

        BankBalance::updateBalance();

        $saldo = BankBalance::getCurrentBalance();

        return "{$payments->count()} pembayaran ({$this->rp((int) $payments->sum('amount'))}) ditandai sudah transfer. "
            . "Saldo Bank Octo: {$this->rp($saldo)}.";
    }
}
