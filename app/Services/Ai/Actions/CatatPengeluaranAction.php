<?php

namespace App\Services\Ai\Actions;

use App\Models\BankBalance;
use App\Models\CashBalance;
use App\Models\Expense;
use RuntimeException;

/**
 * Catat pengeluaran baru (Expense). Validasi saldo & sinkronisasi saldo
 * Bank/Cash ditangani otomatis oleh model Expense (boot).
 */
class CatatPengeluaranAction extends WriteAction
{
    public function name(): string
    {
        return 'catat_pengeluaran';
    }

    public function toolDefinition(): array
    {
        $kategori = collect(Expense::CATEGORIES)
            ->map(fn ($label, $key) => "{$key} ({$label})")
            ->implode(', ');

        return [
            'name' => $this->name(),
            'description' => 'Catat PENGELUARAN uang baru. Pakai bila user ingin mencatat uang keluar '
                . '(beli, bayar, jajan, isi bensin, dll).',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'jumlah' => ['type' => 'integer', 'description' => 'Nominal rupiah, angka bulat tanpa titik.'],
                    'sumber' => ['type' => 'string', 'enum' => ['BANK', 'CASH'], 'description' => 'BANK = Bank Octo, CASH = tunai.'],
                    'kategori' => ['type' => 'string', 'description' => "Pilih KODE kategori paling cocok. Pilihan: {$kategori}."],
                    'deskripsi' => ['type' => 'string', 'description' => 'Keterangan singkat pengeluaran.'],
                    'tanggal' => ['type' => 'string', 'description' => 'Tanggal Y-m-d. Kosongkan untuk hari ini.'],
                ],
                'required' => ['jumlah', 'sumber', 'kategori', 'deskripsi'],
            ],
        ];
    }

    public function prepare(array $input): array
    {
        $jumlah = $this->parseAmount($input['jumlah'] ?? null);

        $sumber = strtoupper((string) ($input['sumber'] ?? ''));
        if (! array_key_exists($sumber, Expense::SOURCES)) {
            throw new RuntimeException('Sumber dana harus BANK atau CASH.');
        }

        $kategori = strtoupper((string) ($input['kategori'] ?? ''));
        if (! array_key_exists($kategori, Expense::CATEGORIES)) {
            throw new RuntimeException('Kategori tidak dikenali. Sebutkan jenis pengeluarannya.');
        }

        $deskripsi = trim((string) ($input['deskripsi'] ?? ''));
        if ($deskripsi === '') {
            throw new RuntimeException('Deskripsi pengeluaran wajib diisi.');
        }

        $tanggal = $this->parseDate($input['tanggal'] ?? null);

        // Cek saldo (validasi awal; dicek ulang saat execute).
        $this->assertBalance($sumber, $jumlah);

        return compact('jumlah', 'sumber', 'kategori', 'deskripsi', 'tanggal');
    }

    public function preview(array $p): string
    {
        return "Catat pengeluaran:\n"
            . "- Jumlah: {$this->rp($p['jumlah'])}\n"
            . "- Sumber: " . Expense::SOURCES[$p['sumber']] . "\n"
            . "- Kategori: " . Expense::CATEGORIES[$p['kategori']] . "\n"
            . "- Deskripsi: {$p['deskripsi']}\n"
            . "- Tanggal: {$p['tanggal']}\n\n"
            . "Simpan? Balas *ya* atau *tidak*.";
    }

    public function execute(array $p): string
    {
        $this->assertBalance($p['sumber'], $p['jumlah']);

        Expense::create([
            'expense_date' => $p['tanggal'],
            'amount' => $p['jumlah'],
            'category' => $p['kategori'],
            'source' => $p['sumber'],
            'description' => $p['deskripsi'],
        ]);

        $sisa = $p['sumber'] === Expense::SOURCE_BANK
            ? BankBalance::getCurrentBalance()
            : CashBalance::getCurrentBalance();

        return "Pengeluaran {$this->rp($p['jumlah'])} tercatat. Sisa saldo "
            . Expense::SOURCES[$p['sumber']] . ": {$this->rp($sisa)}.";
    }

    private function assertBalance(string $sumber, int $jumlah): void
    {
        $saldo = $sumber === Expense::SOURCE_BANK
            ? BankBalance::getCurrentBalance()
            : CashBalance::getCurrentBalance();

        if ($jumlah > $saldo) {
            throw new RuntimeException(
                'Saldo ' . Expense::SOURCES[$sumber] . ' tidak cukup (tersedia ' . $this->rp($saldo) . ').'
            );
        }
    }
}
