<?php

namespace App\Services\Ai\Actions;

use App\Models\BankBalance;
use App\Models\BankTransfer;
use App\Models\Payment;
use RuntimeException;

/**
 * Tandai pembayaran proyek (yang belum ditransfer) sebagai sudah masuk Bank Octo.
 * Membuat BankTransfer per pembayaran lalu memperbarui saldo bank.
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
            'description' => 'Catat bahwa pembayaran sebuah proyek sudah DITRANSFER masuk ke rekening Bank '
                . 'Octo. Menandai semua pembayaran proyek itu yang belum ditransfer.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'proyek' => ['type' => 'string', 'description' => 'Nama proyek atau nama klien.'],
                    'tanggal' => ['type' => 'string', 'description' => 'Tanggal transfer Y-m-d, default hari ini.'],
                    'referensi' => ['type' => 'string', 'description' => 'Nomor referensi transfer (opsional).'],
                ],
                'required' => ['proyek'],
            ],
        ];
    }

    public function prepare(array $input): array
    {
        $project = $this->resolveProject((string) ($input['proyek'] ?? ''));

        $payments = $project->payments()->where('is_transferred', false)->get();

        if ($payments->isEmpty()) {
            throw new RuntimeException("Tidak ada pembayaran proyek \"{$project->title}\" yang belum ditransfer.");
        }

        return [
            'project_title' => $project->title,
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
            . "- Proyek: {$p['project_title']}\n"
            . "- {$p['jumlah_pembayaran']} pembayaran, total {$this->rp($p['total'])}\n"
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

        return "{$payments->count()} pembayaran proyek \"{$p['project_title']}\" ditandai sudah transfer. "
            . "Saldo Bank Octo: {$this->rp($saldo)}.";
    }
}
