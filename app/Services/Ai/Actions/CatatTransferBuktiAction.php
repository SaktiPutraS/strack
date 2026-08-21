<?php

namespace App\Services\Ai\Actions;

use App\Models\BankBalance;
use App\Models\BankTransfer;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Cocokkan BUKTI TRANSFER (foto) dengan pembayaran yang belum ditransfer ke
 * Bank Octo. Bila nominal di bukti SAMA PERSIS dengan total semua pembayaran
 * yang belum ditransfer, seluruhnya ditandai sudah transfer setelah user
 * membalas ya. Bila tidak sama, aksi ditolak dan selisihnya dilaporkan.
 *
 * Dipicu dari gambar, bukan dari teks, karena itu disembunyikan dari daftar
 * tool AI (lihat hidden()). Nominal HANYA dibaca AI; perbandingannya di sini.
 */
class CatatTransferBuktiAction extends WriteAction
{
    /** Membaca gambar makan waktu, sama seperti struk. */
    private const TTL_MINUTES = 20;

    /** Batas baris rincian yang ditampilkan di pesan. */
    private const MAX_ROWS = 12;

    public function name(): string
    {
        return 'catat_transfer_bukti';
    }

    public function hidden(): bool
    {
        return true;
    }

    public function pendingTtlMinutes(): int
    {
        return self::TTL_MINUTES;
    }

    /** Tidak ditawarkan ke AI sebagai tool (lihat hidden()). */
    public function toolDefinition(): array
    {
        return [];
    }

    /**
     * @param  array  $input  hasil TransferProofParser::parse()
     */
    public function prepare(array $input): array
    {
        $nominal = (int) ($input['nominal'] ?? 0);

        if ($nominal <= 0) {
            throw new RuntimeException(
                'Nominal di bukti transfer itu tidak terbaca. Coba kirim ulang gambarnya lebih jelas.'
            );
        }

        $payments = Payment::with('project.client')
            ->where('is_transferred', false)
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        if ($payments->isEmpty()) {
            throw new RuntimeException(
                'Semua pembayaran sudah ditandai transfer, jadi bukti sebesar ' . $this->rp($nominal)
                . ' ini tidak ada pasangannya.'
            );
        }

        $total = (int) $payments->sum('amount');
        $rincian = $payments->map(fn (Payment $p) => [
            'label'  => $this->labelOf($p),
            'amount' => (int) $p->amount,
        ])->all();

        if ($total !== $nominal) {
            throw new RuntimeException($this->mismatchMessage($input, $nominal, $total, $rincian));
        }

        return [
            'nominal'           => $nominal,
            'tanggal'           => $this->parseDate($input['tanggal'] ?? null),
            'ref'               => $input['ref'] ?? null,
            'bank'              => $input['bank'] ?? null,
            'keterangan'        => $input['keterangan'] ?? null,
            'payment_ids'       => $payments->pluck('id')->all(),
            'jumlah_pembayaran' => $payments->count(),
            'rincian'           => $rincian,
        ];
    }

    public function preview(array $p): string
    {
        $lines = [];
        $lines[] = '💸 Bukti transfer' . ($p['bank'] ? " {$p['bank']}" : '')
            . ' - ' . $this->tanggalIndonesia($p['tanggal']);
        $lines[] = 'Nominal: ' . $this->rp($p['nominal']);

        if ($p['ref']) {
            $lines[] = "Referensi: {$p['ref']}";
        }

        if ($p['keterangan']) {
            $lines[] = "Keterangan: {$p['keterangan']}";
        }

        $lines[] = '';
        $lines[] = "✅ Nominalnya PAS dengan seluruh pembayaran yang belum ditransfer ({$p['jumlah_pembayaran']} pembayaran):";
        $lines = array_merge($lines, $this->rowLines($p['rincian']));
        $lines[] = '';
        $lines[] = 'Total: ' . $this->rp($p['nominal']);

        $lines[] = '';
        $lines[] = 'Tandai semuanya sudah masuk Bank Octo? Balas *ya* atau *tidak*.';

        return implode("\n", $lines);
    }

    public function execute(array $p): string
    {
        $payments = Payment::whereIn('id', $p['payment_ids'])
            ->where('is_transferred', false)
            ->get();

        if ($payments->isEmpty()) {
            throw new RuntimeException('Pembayaran itu sudah ditandai transfer sebelumnya.');
        }

        // Data bisa berubah selagi menunggu konfirmasi. Nominal harus tetap pas.
        $total = (int) $payments->sum('amount');
        if ($total !== (int) $p['nominal'] || $payments->count() !== (int) $p['jumlah_pembayaran']) {
            throw new RuntimeException(
                'Daftar pembayaran yang belum ditransfer sudah berubah sejak rekap tadi (sekarang '
                . $this->rp($total) . '). Kirim ulang bukti transfernya.'
            );
        }

        DB::transaction(function () use ($payments, $p) {
            foreach ($payments as $payment) {
                BankTransfer::create([
                    'payment_id'       => $payment->id,
                    'transfer_date'    => $p['tanggal'],
                    'transfer_amount'  => $payment->amount,
                    'reference_number' => $p['ref'],
                    'notes'            => 'Via bukti transfer di bot Telegram',
                ]);
            }
        });

        BankBalance::updateBalance();

        return "{$payments->count()} pembayaran ({$this->rp($total)}) ditandai sudah transfer. "
            . 'Saldo Bank Octo: ' . $this->rp((int) BankBalance::getCurrentBalance()) . '.';
    }

    /**
     * Pesan penolakan saat nominal tidak sama. Sengaja memuat rinciannya supaya
     * user langsung tahu bagian mana yang perlu dicatat manual.
     */
    private function mismatchMessage(array $input, int $nominal, int $total, array $rincian): string
    {
        $selisih = abs($total - $nominal);
        $arah = $nominal > $total ? 'lebih besar' : 'lebih kecil';

        $lines = [];
        $lines[] = '⚠️ Nominal bukti transfer tidak cocok, jadi belum saya catat.';
        $lines[] = '';
        $lines[] = 'Bukti transfer: ' . $this->rp($nominal)
            . (! empty($input['tanggal']) ? ' (' . $this->tanggalIndonesia($this->parseDate($input['tanggal'])) . ')' : '');
        $lines[] = 'Belum ditransfer: ' . $this->rp($total) . ' dari ' . count($rincian) . ' pembayaran';
        $lines[] = "Selisih: {$this->rp($selisih)} ({$arah} dari yang tercatat)";
        $lines[] = '';
        $lines[] = 'Rinciannya:';
        $lines = array_merge($lines, $this->rowLines($rincian));
        $lines[] = '';
        $lines[] = 'Kalau transfernya memang sebagian, catat lewat menu Transfer Bank di aplikasi.';

        return implode("\n", $lines);
    }

    /** @param  array<int, array{label: string, amount: int}>  $rincian */
    private function rowLines(array $rincian): array
    {
        $lines = [];
        $shown = array_slice($rincian, 0, self::MAX_ROWS);

        foreach ($shown as $i => $row) {
            $lines[] = ($i + 1) . ". {$row['label']}: {$this->rp($row['amount'])}";
        }

        $sisa = count($rincian) - count($shown);
        if ($sisa > 0) {
            $lines[] = "... dan {$sisa} pembayaran lain.";
        }

        return $lines;
    }

    private function labelOf(Payment $payment): string
    {
        $klien = $payment->project?->client?->name;
        $proyek = $payment->project?->title;

        $label = trim(implode(' - ', array_filter([$klien, $proyek])));

        return $label !== '' ? mb_substr($label, 0, 60) : "Pembayaran #{$payment->id}";
    }

    /** "21 Agustus 2026" (APP_LOCALE masih en, jadi nama bulan ditulis sendiri). */
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
