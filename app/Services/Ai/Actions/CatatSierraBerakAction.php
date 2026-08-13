<?php

namespace App\Services\Ai\Actions;

use App\Models\SierraBerak;
use RuntimeException;

/**
 * Catat entri Sierra Berak (tanggal, waktu, keterangan).
 */
class CatatSierraBerakAction extends WriteAction
{
    public function name(): string
    {
        return 'catat_sierra_berak';
    }

    public function toolDefinition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Catat entri Sierra Berak (log harian Sierra). Simpan tanggal, waktu, dan keterangan.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'keterangan' => ['type' => 'string', 'description' => 'Isi catatan.'],
                    'tanggal' => ['type' => 'string', 'description' => 'Tanggal Y-m-d, default hari ini.'],
                    'waktu' => ['type' => 'string', 'description' => 'Waktu format HH:MM (24 jam), default jam sekarang.'],
                ],
                'required' => ['keterangan'],
            ],
        ];
    }

    public function prepare(array $input): array
    {
        $keterangan = trim((string) ($input['keterangan'] ?? ''));
        if ($keterangan === '') {
            throw new RuntimeException('Keterangan wajib diisi.');
        }

        $waktu = trim((string) ($input['waktu'] ?? ''));
        if ($waktu === '' || ! preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $waktu)) {
            $waktu = now()->format('H:i');
        }

        return [
            'keterangan' => $keterangan,
            'tanggal' => $this->parseDate($input['tanggal'] ?? null),
            'waktu' => $waktu,
        ];
    }

    public function preview(array $p): string
    {
        return "Catat Sierra Berak:\n"
            . "- Tanggal: {$p['tanggal']} {$p['waktu']}\n"
            . "- Keterangan: {$p['keterangan']}\n\n"
            . "Simpan? Balas *ya* atau *tidak*.";
    }

    public function execute(array $p): string
    {
        SierraBerak::create([
            'tanggal' => $p['tanggal'],
            'waktu' => $p['waktu'],
            'keterangan' => $p['keterangan'],
        ]);

        return "Catatan Sierra Berak {$p['tanggal']} {$p['waktu']} tersimpan.";
    }
}
