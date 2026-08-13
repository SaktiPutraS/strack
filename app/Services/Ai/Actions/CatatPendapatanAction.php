<?php

namespace App\Services\Ai\Actions;

use App\Models\Payment;
use App\Models\Project;
use RuntimeException;

/**
 * Catat pendapatan/pembayaran proyek (Payment). Sinkronisasi paid_amount proyek
 * ditangani model Payment (boot). Tolak bila melebihi sisa tagihan.
 */
class CatatPendapatanAction extends WriteAction
{
    use ResolvesProject;

    public function name(): string
    {
        return 'catat_pendapatan';
    }

    public function toolDefinition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Catat PEMBAYARAN/pendapatan yang diterima untuk sebuah proyek dari klien '
                . '(DP, cicilan, atau pelunasan).',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'proyek' => ['type' => 'string', 'description' => 'Nama proyek atau nama klien.'],
                    'jumlah' => ['type' => 'integer', 'description' => 'Nominal rupiah, angka bulat.'],
                    'tipe' => ['type' => 'string', 'enum' => ['DP', 'INSTALLMENT', 'FULL', 'FINAL'], 'description' => 'DP=uang muka, INSTALLMENT=cicilan, FULL=lunas sekaligus, FINAL=pelunasan.'],
                    'metode' => ['type' => 'string', 'description' => 'Metode bayar (mis. Transfer, Cash, QRIS). Opsional.'],
                    'catatan' => ['type' => 'string', 'description' => 'Catatan opsional.'],
                    'tanggal' => ['type' => 'string', 'description' => 'Tanggal Y-m-d, default hari ini.'],
                ],
                'required' => ['proyek', 'jumlah', 'tipe'],
            ],
        ];
    }

    public function prepare(array $input): array
    {
        $project = $this->resolveProject((string) ($input['proyek'] ?? ''));

        $jumlah = $this->parseAmount($input['jumlah'] ?? null);

        $tipe = strtoupper((string) ($input['tipe'] ?? ''));
        if (! in_array($tipe, ['DP', 'INSTALLMENT', 'FULL', 'FINAL'], true)) {
            throw new RuntimeException('Tipe pembayaran harus DP, INSTALLMENT, FULL, atau FINAL.');
        }

        $sisa = (float) $project->remaining_amount;
        if ($jumlah > $sisa) {
            throw new RuntimeException(
                "Jumlah melebihi sisa tagihan proyek \"{$project->title}\" ({$this->rp($sisa)})."
            );
        }

        return [
            'project_id' => $project->id,
            'project_title' => $project->title,
            'client_name' => $project->client->name ?? '-',
            'jumlah' => $jumlah,
            'tipe' => $tipe,
            'metode' => trim((string) ($input['metode'] ?? '')) ?: null,
            'catatan' => trim((string) ($input['catatan'] ?? '')) ?: null,
            'tanggal' => $this->parseDate($input['tanggal'] ?? null),
        ];
    }

    public function preview(array $p): string
    {
        return "Catat pendapatan proyek:\n"
            . "- Proyek: {$p['project_title']} ({$p['client_name']})\n"
            . "- Jumlah: {$this->rp($p['jumlah'])}\n"
            . "- Tipe: {$p['tipe']}\n"
            . ($p['metode'] ? "- Metode: {$p['metode']}\n" : '')
            . "- Tanggal: {$p['tanggal']}\n\n"
            . "Simpan? Balas *ya* atau *tidak*.";
    }

    public function execute(array $p): string
    {
        $project = Project::findOrFail($p['project_id']);

        if ($p['jumlah'] > (float) $project->remaining_amount) {
            throw new RuntimeException('Sisa tagihan sudah berubah, jumlah kini melebihi sisa.');
        }

        Payment::create([
            'project_id' => $project->id,
            'amount' => $p['jumlah'],
            'payment_type' => $p['tipe'],
            'payment_date' => $p['tanggal'],
            'payment_method' => $p['metode'],
            'notes' => $p['catatan'],
        ]);

        $sisa = $project->fresh()->remaining_amount;

        return "Pembayaran {$this->rp($p['jumlah'])} untuk \"{$p['project_title']}\" tercatat. "
            . "Sisa tagihan: {$this->rp($sisa)}.";
    }
}
