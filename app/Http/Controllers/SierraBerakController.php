<?php

namespace App\Http\Controllers;

use App\Models\SierraBerak;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SierraBerakController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter bulan dan tahun dari request, default ke bulan/tahun saat ini
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // Validasi input
        $month = max(1, min(12, (int)$month));
        $year = max(2020, min(2030, (int)$year));

        // Buat objek Carbon untuk bulan yang dipilih
        $currentDate = Carbon::create($year, $month, 1);

        // Ambil data catatan untuk bulan yang dipilih
        $records = SierraBerak::byMonth($year, $month)
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal->format('Y-m-d');
            });

        // Data untuk kalender
        $calendarData = $this->generateCalendarData($currentDate, $records);

        // Statistik
        $stats = [
            'total_catatan_bulan_ini' => SierraBerak::byMonth($year, $month)->count(),
            'total_catatan_hari_ini' => SierraBerak::today()->count(),
            'total_catatan_tahun_ini' => SierraBerak::byYear(now()->year)->count(),
            'rata_rata_per_hari' => $this->getAveragePerDay($year, $month)
        ];

        return view('sierra-berak.index', compact('calendarData', 'currentDate', 'records', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
            'keterangan' => 'required|string|max:1000'
        ]);

        SierraBerak::create([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil ditambahkan!'
        ]);
    }

    public function show($id)
    {
        $record = SierraBerak::findOrFail($id);
        return response()->json($record);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
            'keterangan' => 'required|string|max:1000'
        ]);

        $record = SierraBerak::findOrFail($id);
        $record->update([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $record = SierraBerak::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil dihapus!'
        ]);
    }

    public function getByDate($date)
    {
        $records = SierraBerak::byDate($date)
            ->orderBy('waktu')
            ->get();

        return response()->json($records);
    }

    private function generateCalendarData($currentDate, $records)
    {
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Mulai dari hari Senin pada minggu yang berisi tanggal 1
        $startDate = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);

        // Akhiri pada hari Minggu pada minggu yang berisi tanggal terakhir
        $endDate = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $calendar = [];
        $currentWeek = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $dayRecords = $records->get($dateString, collect());

            $currentWeek[] = [
                'date' => $date->copy(),
                'is_current_month' => $date->month === $currentDate->month,
                'is_today' => $date->isToday(),
                'is_weekend' => $date->isWeekend(),
                'records' => $dayRecords,
                'record_count' => $dayRecords->count()
            ];

            if ($date->dayOfWeek === Carbon::SUNDAY) {
                $calendar[] = $currentWeek;
                $currentWeek = [];
            }
        }

        return $calendar;
    }

    private function getAveragePerDay($year, $month)
    {
        $totalRecords = SierraBerak::byMonth($year, $month)->count();
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        return $totalRecords > 0 ? round($totalRecords / $daysInMonth, 1) : 0;
    }
}
