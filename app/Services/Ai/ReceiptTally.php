<?php

namespace App\Services\Ai;

use App\Models\Expense;

/**
 * Mengubah daftar item hasil baca struk menjadi BARIS PENGELUARAN per kategori,
 * mengikuti kebiasaan pencatatan pemilik: satu struk dipecah jadi beberapa
 * pengeluaran, satu per kategori.
 *
 * Aturan voucher (keputusan pemilik): potongan seluruh belanja dibebankan ke
 * kategori ENTERTAIN lebih dulu. Kalau belanjaan itu tidak punya item Entertain
 * (atau nilainya tidak cukup), sisanya dibagi PROPORSIONAL ke kategori lain,
 * sehingga jumlah semua baris tetap sama persis dengan total yang dibayar.
 */
class ReceiptTally
{
    /** Kategori yang menanggung voucher lebih dulu. */
    public const VOUCHER_FIRST = 'ENTERTAIN';

    /** Kategori tempat biaya kirim dicatat bila ada. */
    public const SHIPPING_CATEGORY = 'LAINNYA';

    /**
     * @param  array  $parsed  hasil ReceiptParser::parse()
     * @return array<int, array{kategori: string, label: string, amount: int, items: array<int, string>, deskripsi: string, voucher_cut: int}>
     */
    public static function groups(array $parsed): array
    {
        $toko = (string) ($parsed['toko'] ?? 'Belanja');

        $amounts = [];
        $names = [];

        foreach ($parsed['items'] ?? [] as $item) {
            $kategori = $item['kategori'];
            $amounts[$kategori] = ($amounts[$kategori] ?? 0) + (int) $item['net'];
            $names[$kategori][] = self::itemLabel($item);
        }

        $cuts = self::allocateVoucher($amounts, (int) ($parsed['voucher'] ?? 0));

        $ongkir = (int) ($parsed['ongkir'] ?? 0);
        if ($ongkir > 0) {
            $amounts[self::SHIPPING_CATEGORY] = ($amounts[self::SHIPPING_CATEGORY] ?? 0) + $ongkir;
            $names[self::SHIPPING_CATEGORY][] = 'Ongkir';
        }

        $groups = [];
        foreach ($amounts as $kategori => $amount) {
            $final = $amount - ($cuts[$kategori] ?? 0);

            // Kategori yang habis dimakan voucher tidak perlu dicatat.
            if ($final <= 0) {
                continue;
            }

            $daftar = $names[$kategori] ?? [];

            $groups[] = [
                'kategori'    => $kategori,
                'label'       => Expense::CATEGORIES[$kategori] ?? $kategori,
                'amount'      => $final,
                'items'       => $daftar,
                'deskripsi'   => trim($toko) . ' - ' . implode(', ', $daftar),
                'voucher_cut' => $cuts[$kategori] ?? 0,
            ];
        }

        usort($groups, fn ($a, $b) => $b['amount'] <=> $a['amount']);

        return $groups;
    }

    /** Jumlah semua baris pengeluaran (harus sama dengan total struk). */
    public static function totalOf(array $groups): int
    {
        return array_sum(array_column($groups, 'amount'));
    }

    /**
     * Bagi voucher ke kategori. Kembalikan potongan per kategori.
     *
     * @param  array<string, int>  $amounts  nilai per kategori sebelum voucher
     * @return array<string, int>
     */
    private static function allocateVoucher(array $amounts, int $voucher): array
    {
        $cuts = [];

        if ($voucher <= 0 || $amounts === []) {
            return $cuts;
        }

        $sisa = $voucher;

        // 1. Bebankan ke Entertain dulu, sebatas nilainya.
        if (($amounts[self::VOUCHER_FIRST] ?? 0) > 0) {
            $cuts[self::VOUCHER_FIRST] = min($sisa, $amounts[self::VOUCHER_FIRST]);
            $sisa -= $cuts[self::VOUCHER_FIRST];
        }

        if ($sisa <= 0) {
            return $cuts;
        }

        // 2. Sisanya proporsional ke kategori yang masih punya nilai.
        $eligible = [];
        foreach ($amounts as $kategori => $amount) {
            $tersisa = $amount - ($cuts[$kategori] ?? 0);
            if ($tersisa > 0) {
                $eligible[$kategori] = $tersisa;
            }
        }

        $base = array_sum($eligible);
        if ($base <= 0) {
            return $cuts;
        }

        // Voucher lebih besar dari belanjaan: potong habis, jangan sampai minus.
        if ($sisa >= $base) {
            foreach ($eligible as $kategori => $tersisa) {
                $cuts[$kategori] = ($cuts[$kategori] ?? 0) + $tersisa;
            }

            return $cuts;
        }

        $dibagi = 0;
        foreach ($eligible as $kategori => $tersisa) {
            $bagian = (int) floor($sisa * $tersisa / $base);
            $cuts[$kategori] = ($cuts[$kategori] ?? 0) + $bagian;
            $dibagi += $bagian;
        }

        // Sisa pembulatan (beberapa rupiah) ditaruh di kategori terbesar dulu,
        // supaya jumlah akhir sama persis dengan total struk.
        $urut = $eligible;
        arsort($urut);
        $kunci = array_keys($urut);
        $i = 0;

        while ($dibagi < $sisa && $kunci !== []) {
            $kategori = $kunci[$i % count($kunci)];

            if ($cuts[$kategori] < $eligible[$kategori]) {
                $cuts[$kategori]++;
                $dibagi++;
            }

            $i++;

            // Pengaman: semua kategori sudah mentok, hentikan.
            if ($i > count($kunci) * 2 && $dibagi < $sisa) {
                break;
            }
        }

        return $cuts;
    }

    /** "Aqua Galon" atau "Aqua Galon (2)" bila lebih dari satu. */
    private static function itemLabel(array $item): string
    {
        $qty = (int) ($item['qty'] ?? 1);

        return $qty > 1 ? "{$item['nama']} ({$qty})" : (string) $item['nama'];
    }
}
