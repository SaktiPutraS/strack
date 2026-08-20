<?php

namespace App\Services\Ai\Actions;

use App\Models\BankBalance;
use App\Models\CashBalance;
use App\Models\Expense;
use App\Services\Ai\AiGateway;
use App\Services\Ai\ReceiptTally;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Catat SATU STRUK belanja menjadi BEBERAPA pengeluaran sekaligus, satu baris
 * per kategori. Dipicu dari foto struk yang dikirim ke bot, bukan dari teks,
 * karena itu aksinya disembunyikan dari daftar tool AI.
 *
 * Sebelum disimpan, user bisa mengoreksi pengelompokan lewat balasan biasa
 * (lihat refine), mis. "tango masukkan ke sierra".
 */
class CatatStrukAction extends WriteAction
{
    /** Struk perlu waktu baca lebih lama daripada konfirmasi biasa. */
    private const TTL_MINUTES = 20;

    public function __construct(private AiGateway $ai) {}

    public function name(): string
    {
        return 'catat_struk';
    }

    public function hidden(): bool
    {
        return true;
    }

    public function pendingTtlMinutes(): int
    {
        return self::TTL_MINUTES;
    }

    public function supportsRefine(): bool
    {
        return true;
    }

    /** Tidak ditawarkan ke AI sebagai tool (lihat hidden()). */
    public function toolDefinition(): array
    {
        return [];
    }

    /**
     * @param  array  $input  hasil ReceiptParser::parse(), boleh ditambah
     *                        'hint' (caption Telegram) dan 'sumber'
     */
    public function prepare(array $input): array
    {
        $items = $input['items'] ?? [];

        if (! is_array($items) || $items === []) {
            throw new RuntimeException('Tidak ada item belanja yang bisa dicatat dari struk itu.');
        }

        $prepared = [
            'toko'        => (string) ($input['toko'] ?? 'Belanja'),
            'ref'         => $input['ref'] ?? null,
            'tanggal'     => $this->parseDate($input['tanggal'] ?? null),
            'sumber'      => $this->resolveSource($input['sumber'] ?? null, (string) ($input['hint'] ?? '')),
            'items'       => array_values($items),
            'voucher'     => (int) ($input['voucher'] ?? 0),
            'ongkir'      => (int) ($input['ongkir'] ?? 0),
            'total_struk' => (int) ($input['total'] ?? 0),
        ];

        return $this->recalculate($prepared);
    }

    public function preview(array $p): string
    {
        $lines = [];
        $lines[] = "🧾 Struk {$p['toko']} - " . $this->tanggalIndonesia($p['tanggal']);
        $lines[] = 'Sumber: ' . Expense::SOURCES[$p['sumber']];
        $lines[] = '';

        $no = 1;
        foreach ($p['groups'] as $group) {
            $lines[] = "{$no}. {$group['label']}: {$this->rp($group['amount'])}";
            $lines[] = '   ' . implode(', ', $group['items']);

            if ($group['voucher_cut'] > 0) {
                $lines[] = "   (voucher {$this->rp($group['voucher_cut'])} sudah dipotong di sini)";
            }

            $no++;
        }

        $jumlah = count($p['groups']);
        $lines[] = '';
        $lines[] = "Jadi {$jumlah} pengeluaran, total {$this->rp($p['total'])}.";

        if ($p['total_struk'] > 0 && $p['total'] !== $p['total_struk']) {
            $selisih = abs($p['total_struk'] - $p['total']);
            $lines[] = "⚠️ Total di struk {$this->rp($p['total_struk'])}, selisih {$this->rp($selisih)}. "
                . 'Periksa dulu sebelum menyimpan.';
        }

        $lines[] = '';
        $lines[] = 'Balas ya untuk simpan, tidak untuk batal, atau sebutkan koreksinya '
            . '(mis. "tango masukkan ke sierra").';

        return implode("\n", $lines);
    }

    public function execute(array $p): string
    {
        $this->assertBalance($p['sumber'], $p['total']);

        DB::transaction(function () use ($p) {
            foreach ($p['groups'] as $group) {
                Expense::create([
                    'expense_date' => $p['tanggal'],
                    'amount'       => $group['amount'],
                    'category'     => $group['kategori'],
                    'source'       => $p['sumber'],
                    'description'  => $group['deskripsi'],
                ]);
            }
        });

        $sisa = $p['sumber'] === Expense::SOURCE_BANK
            ? BankBalance::getCurrentBalance()
            : CashBalance::getCurrentBalance();

        $jumlah = count($p['groups']);

        return "{$jumlah} pengeluaran dari struk {$p['toko']} tercatat ({$this->rp($p['total'])}). "
            . 'Sisa saldo ' . Expense::SOURCES[$p['sumber']] . ": {$this->rp($sisa)}.";
    }

    /**
     * Terapkan koreksi user pada hasil pengelompokan. AI hanya memindahkan item
     * antar kategori (dan boleh mengubah sumber/tanggal); semua perhitungan
     * uang tetap dikerjakan di sini.
     */
    public function refine(array $prepared, string $instruction): array
    {
        $data = $this->askRefinement($prepared, $instruction);

        if (! ($data['koreksi'] ?? false)) {
            throw new NotACorrectionException();
        }

        $berubah = false;

        foreach ($data['kategori'] ?? [] as $ubah) {
            if (! is_array($ubah)) {
                continue;
            }

            $index = (int) ($ubah['no'] ?? 0) - 1;
            $kategori = strtoupper(trim((string) ($ubah['kategori'] ?? '')));

            if (! isset($prepared['items'][$index]) || ! array_key_exists($kategori, Expense::CATEGORIES)) {
                continue;
            }

            if ($prepared['items'][$index]['kategori'] !== $kategori) {
                $prepared['items'][$index]['kategori'] = $kategori;
                $berubah = true;
            }
        }

        $sumber = strtoupper(trim((string) ($data['sumber'] ?? '')));
        if (array_key_exists($sumber, Expense::SOURCES) && $sumber !== $prepared['sumber']) {
            $prepared['sumber'] = $sumber;
            $berubah = true;
        }

        $tanggal = trim((string) ($data['tanggal'] ?? ''));
        if ($tanggal !== '') {
            $baru = $this->parseDate($tanggal);
            if ($baru !== $prepared['tanggal']) {
                $prepared['tanggal'] = $baru;
                $berubah = true;
            }
        }

        if (! $berubah) {
            throw new RuntimeException(
                'Saya belum menangkap koreksinya. Sebutkan barang dan kategori tujuannya, '
                . 'mis. "tango ke sierra". Balas ya kalau rekapnya sudah benar.'
            );
        }

        return $this->recalculate($prepared);
    }

    /** Hitung ulang pengelompokan + total setelah data berubah. */
    private function recalculate(array $p): array
    {
        $p['groups'] = ReceiptTally::groups($p);
        $p['total'] = ReceiptTally::totalOf($p['groups']);

        if ($p['groups'] === [] || $p['total'] <= 0) {
            throw new RuntimeException('Nilai belanja pada struk itu tidak terbaca dengan benar.');
        }

        $this->assertBalance($p['sumber'], $p['total']);

        return $p;
    }

    /** Tanya AI: apa maksud koreksinya (item mana pindah ke kategori mana). */
    private function askRefinement(array $p, string $instruction): array
    {
        $daftar = [];
        foreach ($p['items'] as $i => $item) {
            $daftar[] = ($i + 1) . ". {$item['nama']} ({$item['kategori']})";
        }

        $daftarBarang = implode("\n", $daftar);

        $kategori = collect(Expense::CATEGORIES)
            ->map(fn ($label, $key) => "{$key} = {$label}")
            ->implode("\n");

        $system = <<<PROMPT
Kamu membantu mengoreksi hasil pembacaan struk belanja SEBELUM disimpan ke aplikasi keuangan.
Barang pada struk ini (nomor, nama, kategori sekarang):
{$daftarBarang}

KODE KATEGORI yang boleh dipakai:
{$kategori}

Pesan user berikutnya bisa berupa KOREKSI atas rekap ini, atau permintaan lain yang tidak ada
hubungannya dengan struk. BALAS HANYA JSON, tanpa penjelasan:
- Bila koreksi: {"koreksi": true, "kategori": [{"no": 3, "kategori": "SIERRA"}], "sumber": null, "tanggal": null}
  Sertakan hanya barang yang BERUBAH kategorinya. "sumber" diisi BANK atau CASH hanya bila user
  meminta ganti sumber dana. "tanggal" diisi Y-m-d hanya bila user meminta ganti tanggal.
- Bila jelas BUKAN koreksi struk (mis. bertanya data lain, minta catat hal lain): {"koreksi": false}
PROMPT;

        $system = str_replace('{$daftar_placeholder}', implode("\n", $daftar), $system);

        $raw = $this->ai->text($system, [['role' => 'user', 'content' => $instruction]], 700);

        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');

        if ($start === false || $end === false || $end <= $start) {
            throw new NotACorrectionException();
        }

        $data = json_decode(substr($raw, $start, $end - $start + 1), true);

        return is_array($data) ? $data : ['koreksi' => false];
    }

    /** Sumber dana: default Bank Octo (kebiasaan belanja daring), CASH bila disebut. */
    private function resolveSource(?string $sumber, string $hint): string
    {
        $sumber = strtoupper(trim((string) $sumber));

        if (array_key_exists($sumber, Expense::SOURCES)) {
            return $sumber;
        }

        return preg_match('/\b(cash|tunai)\b/i', $hint) === 1
            ? Expense::SOURCE_CASH
            : Expense::SOURCE_BANK;
    }

    private function assertBalance(string $sumber, int $jumlah): void
    {
        $saldo = $sumber === Expense::SOURCE_BANK
            ? BankBalance::getCurrentBalance()
            : CashBalance::getCurrentBalance();

        if ($jumlah > $saldo) {
            throw new RuntimeException(
                'Saldo ' . Expense::SOURCES[$sumber] . ' tidak cukup untuk struk ini (tersedia '
                . $this->rp($saldo) . ', butuh ' . $this->rp($jumlah) . ').'
            );
        }
    }

    /** "20 Agustus 2026" (APP_LOCALE masih en, jadi nama bulan ditulis sendiri). */
    private function tanggalIndonesia(string $date): string
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $carbon = \Carbon\Carbon::parse($date);

        return $carbon->day . ' ' . $bulan[$carbon->month] . ' ' . $carbon->year;
    }
}
