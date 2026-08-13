<?php

namespace App\Services\Ai\Actions;

use App\Models\DebtRecord;
use RuntimeException;

/**
 * Catat pembayaran/cicilan untuk catatan hutang atau piutang yang sudah ada.
 * paid_amount + status LUNAS dihitung otomatis oleh model. Tolak kelebihan bayar.
 */
class CatatBayarHutangAction extends WriteAction
{
    public function name(): string
    {
        return 'catat_bayar_hutang_piutang';
    }

    public function toolDefinition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Catat pembayaran/cicilan untuk catatan HUTANG (uang yang saya pinjam) atau '
                . 'PIUTANG (uang yang dipinjam orang ke saya) yang sudah tercatat.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'catatan_hutang' => ['type' => 'string', 'description' => 'Nama pihak atau judul catatan hutang/piutang.'],
                    'jumlah' => ['type' => 'integer', 'description' => 'Nominal pembayaran, angka bulat.'],
                    'tanggal' => ['type' => 'string', 'description' => 'Tanggal Y-m-d, default hari ini.'],
                    'keterangan' => ['type' => 'string', 'description' => 'Catatan opsional.'],
                ],
                'required' => ['catatan_hutang', 'jumlah'],
            ],
        ];
    }

    public function prepare(array $input): array
    {
        $debt = $this->resolveDebt((string) ($input['catatan_hutang'] ?? ''));
        $jumlah = $this->parseAmount($input['jumlah'] ?? null);

        $sisa = (float) $debt->remaining_amount;
        if ($jumlah > $sisa) {
            throw new RuntimeException(
                "Jumlah melebihi sisa {$debt->type_label} \"{$debt->party_name}\" ({$debt->formatted_remaining})."
            );
        }

        return [
            'debt_id' => $debt->id,
            'label' => $debt->type_label . ' ' . $debt->party_name . ($debt->title ? " - {$debt->title}" : ''),
            'jumlah' => $jumlah,
            'tanggal' => $this->parseDate($input['tanggal'] ?? null),
            'keterangan' => trim((string) ($input['keterangan'] ?? '')) ?: null,
        ];
    }

    public function preview(array $p): string
    {
        return "Catat pembayaran:\n"
            . "- {$p['label']}\n"
            . "- Jumlah: {$this->rp($p['jumlah'])}\n"
            . "- Tanggal: {$p['tanggal']}\n\n"
            . "Simpan? Balas *ya* atau *tidak*.";
    }

    public function execute(array $p): string
    {
        $debt = DebtRecord::findOrFail($p['debt_id']);

        if ($p['jumlah'] > (float) $debt->remaining_amount) {
            throw new RuntimeException('Sisa sudah berubah, jumlah kini melebihi sisa.');
        }

        $debt->payments()->create([
            'amount' => $p['jumlah'],
            'payment_date' => $p['tanggal'],
            'notes' => $p['keterangan'],
        ]);

        $sisa = $debt->fresh()->formatted_remaining;

        return "Pembayaran {$this->rp($p['jumlah'])} untuk {$p['label']} tercatat. Sisa: {$sisa}.";
    }

    private function resolveDebt(string $query): DebtRecord
    {
        $query = trim($query);
        if ($query === '') {
            throw new RuntimeException('Sebutkan catatan hutang/piutang yang mana.');
        }

        $matches = DebtRecord::search($query)->orderByRaw("status = 'PAID'")->limit(6)->get();

        if ($matches->isEmpty()) {
            throw new RuntimeException("Catatan hutang/piutang \"{$query}\" tidak ditemukan.");
        }

        if ($matches->count() > 1) {
            $daftar = $matches
                ->map(fn (DebtRecord $d) => "- {$d->type_label} {$d->party_name}"
                    . ($d->title ? " ({$d->title})" : '') . " - sisa {$d->formatted_remaining}")
                ->implode("\n");

            throw new RuntimeException("Ada beberapa catatan cocok, sebutkan lebih spesifik:\n{$daftar}");
        }

        return $matches->first();
    }
}
