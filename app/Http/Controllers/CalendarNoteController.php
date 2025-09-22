<?php

namespace App\Http\Controllers;

use App\Models\CalendarNote;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarNoteController extends Controller
{
    public function getMonthNotes($year, $month)
    {
        try {
            $userId = session('role'); // Assuming role stores user ID
            $notes = CalendarNote::getNotesForMonth($userId, $year, $month);

            return response()->json([
                'success' => true,
                'notes' => $notes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kalender'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'title' => 'required|string|max:500',
                'content' => 'nullable|string'
            ]);

            $userId = session('role'); // Assuming role stores user ID

            // Check if note already exists for this date
            $existingNote = CalendarNote::getNoteForDate($userId, $request->date);

            if ($existingNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan untuk tanggal ini sudah ada. Gunakan edit untuk mengubah.'
                ], 400);
            }

            $note = CalendarNote::create([
                'user_id' => $userId,
                'date' => $request->date,
                'title' => $request->title,
                'content' => $request->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil disimpan',
                'note' => $note
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan catatan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:500',
                'content' => 'nullable|string'
            ]);

            $userId = session('role');
            $note = CalendarNote::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan tidak ditemukan'
                ], 404);
            }

            $note->update([
                'title' => $request->title,
                'content' => $request->content
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil diperbarui',
                'note' => $note
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui catatan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $userId = session('role');
            $note = CalendarNote::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$note) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan tidak ditemukan'
                ], 404);
            }

            $note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus catatan: ' . $e->getMessage()
            ], 500);
        }
    }
}
