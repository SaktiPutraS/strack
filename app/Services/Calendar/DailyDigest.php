<?php

namespace App\Services\Calendar;

use App\Models\CalendarEvent;
use App\Models\DebtRecord;
use App\Models\Domain;
use App\Models\MaintenanceTask;
use App\Models\Project;
use Carbon\Carbon;

/**
 * Kumpulkan seluruh isi kalender untuk SATU tanggal dalam bentuk teks polos,
 * dipakai pengingat harian Telegram (command calendar:remind).
 *
 * TODO SENGAJA TIDAK IKUT: todo tempatnya di panel Todo halaman Kalender,
 * bukan di pengingat harian (permintaan user 2026-08-20).
 *
 * Aturan tanggal tiap sumber DISAMAKAN dengan CalendarController (feed
 * kalender): kalau salah satu diubah, ubah keduanya.
 */
class DailyDigest
{
    /** Nama hari & bulan Indonesia (locale aplikasi masih 'en'). */
    private const DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    private const MONTHS = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    /**
     * @return array<string, array{label: string, icon: string, items: string[]}>
     *         Hanya kelompok yang ADA ISINYA yang dikembalikan.
     */
    public function forDate(Carbon $date, string $userId = 'admin'): array
    {
        $groups = [
            'agenda' => ['label' => 'Agenda', 'icon' => '📌', 'items' => $this->agenda($date, $userId)],
            'projects' => ['label' => 'Deadline Proyek', 'icon' => '📋', 'items' => $this->projects($date)],
            'domains' => ['label' => 'Domain Kedaluwarsa', 'icon' => '🌐', 'items' => $this->domains($date)],
            'maintenance' => ['label' => 'Maintenance', 'icon' => '🔧', 'items' => $this->maintenance($date)],
            'debts' => ['label' => 'Jatuh Tempo', 'icon' => '💰', 'items' => $this->debts($date)],
        ];

        return array_filter($groups, fn (array $g) => ! empty($g['items']));
    }

    /** Tanggal dalam Bahasa Indonesia, mis. "Kamis, 20 Agustus 2026". */
    public function formatDate(Carbon $date): string
    {
        return self::DAYS[$date->dayOfWeek] . ', ' . $date->day . ' ' . self::MONTHS[$date->month] . ' ' . $date->year;
    }

    /** Susun pesan siap kirim. Null berarti tidak ada apa-apa hari itu. */
    public function buildMessage(Carbon $date, string $userId = 'admin'): ?string
    {
        $groups = $this->forDate($date, $userId);

        if (empty($groups)) {
            return null;
        }

        $lines = ['🗓️ Agenda Hari Ini', $this->formatDate($date)];

        foreach ($groups as $group) {
            $lines[] = '';
            $lines[] = $group['icon'] . ' ' . $group['label'];
            foreach ($group['items'] as $item) {
                $lines[] = '- ' . $item;
            }
        }

        return implode("\n", $lines);
    }

    /** Pesan untuk hari yang kosong (dipakai kalau dipaksa kirim). */
    public function emptyMessage(Carbon $date): string
    {
        return "🗓️ Agenda Hari Ini\n" . $this->formatDate($date) . "\n\nTidak ada agenda hari ini.";
    }

    // ── Per sumber ──────────────────────────────────────────────────────────
    /** Agenda pribadi (tipe EVENT saja), termasuk yang berulang & multi-hari. */
    private function agenda(Carbon $date, string $userId): array
    {
        $hari = $date->format('Y-m-d');

        return array_map(function (array $row) {
            /** @var CalendarEvent $event */
            $event = $row['event'];
            $baris = $event->time_label . ' ' . $event->title;

            return $event->description
                ? $baris . ' (' . str_replace("\n", ' ', $event->description) . ')'
                : $baris;
        }, CalendarEvent::expandRange($userId, $hari, $hari, CalendarEvent::TYPE_EVENT));
    }

    private function projects(Carbon $date): array
    {
        return Project::with('client')
            ->whereDate('deadline', $date)
            ->whereIn('status', ['WAITING', 'PROGRESS'])
            ->get()
            ->map(function (Project $p) {
                $sisa = (float) $p->remaining_amount;
                $klien = $p->client->name ?? 'Tanpa klien';
                $baris = $klien . ' - ' . $p->title;

                return $sisa > 0
                    ? $baris . ' (sisa tagihan Rp ' . number_format($sisa, 0, ',', '.') . ')'
                    : $baris;
            })
            ->all();
    }

    private function domains(Carbon $date): array
    {
        return Domain::whereDate('expires_at', $date)
            ->orderBy('name')
            ->get()
            ->map(fn (Domain $d) => $d->name . ' habis hari ini'
                . ($d->renewal_cost ? ' (Rp ' . number_format((float) $d->renewal_cost, 0, ',', '.') . ')' : ''))
            ->all();
    }

    /**
     * DATE = tanggal pasti; MONTH = tanggal 1 pada bulan yang dipilih;
     * YEAR = 1 Januari tahun itu. TEXT & ODOMETER tidak punya tanggal.
     */
    private function maintenance(Carbon $date): array
    {
        $items = [];

        foreach (MaintenanceTask::whereIn('schedule_type', ['DATE', 'MONTH', 'YEAR'])->get() as $task) {
            if (! $this->maintenanceJatuhPada($task, $date)) {
                continue;
            }

            $items[] = $task->name . ' (' . $task->schedule_label . ')';
        }

        return $items;
    }

    private function maintenanceJatuhPada(MaintenanceTask $task, Carbon $date): bool
    {
        try {
            switch ($task->schedule_type) {
                case 'DATE':
                    return $task->schedule_value
                        && Carbon::parse($task->schedule_value)->isSameDay($date);

                case 'YEAR':
                    return (int) $task->schedule_value === $date->year
                        && $date->month === 1 && $date->day === 1;

                case 'MONTH':
                    return $date->day === 1
                        && in_array($date->month, $task->monthNumbers(), true);

                default:
                    return false;
            }
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function debts(Carbon $date): array
    {
        return DebtRecord::whereDate('due_date', $date)
            ->where('status', '!=', 'PAID')
            ->get()
            ->map(fn (DebtRecord $d) => $d->type_label . ' - ' . $d->party_name
                . ' (sisa ' . $d->formatted_remaining . ')')
            ->all();
    }
}
