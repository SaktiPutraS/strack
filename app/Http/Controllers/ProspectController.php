<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function index(Request $request)
    {
        $query = Prospect::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        // Validasi arah sortir agar aman dari input yang tidak valid
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc'; // Setel ke default jika input tidak valid
        }

        $prospects = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Prospect::count(),
            'belum_dihubungi' => Prospect::where('status', 'BELUM_DIHUBUNGI')->count(),
            'pengecekan' => Prospect::where('status', 'PENGECEKAN_KEAKTIFAN')->count(),
            'penawaran' => Prospect::where('status', 'PENAWARAN')->count(),
            'follow_up' => Prospect::where('status', 'FOLLOW_UP')->count(),
            'tolak' => Prospect::where('status', 'TOLAK')->count(),
        ];

        return view('prospects.index', compact('prospects', 'stats'));
    }

    public function create()
    {
        return view('prospects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'address' => 'nullable|string',
            'social_media' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:BELUM_DIHUBUNGI,PENGECEKAN_KEAKTIFAN,PENAWARAN,FOLLOW_UP,TOLAK'
        ]);

        Prospect::create($validated);

        return redirect()->route('prospects.index')->with('success', 'Prospek berhasil ditambahkan!');
    }

    public function show(Prospect $prospect)
    {
        return view('prospects.show', compact('prospect'));
    }

    public function edit(Prospect $prospect)
    {
        return view('prospects.edit', compact('prospect'));
    }

    public function update(Request $request, Prospect $prospect)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'address' => 'nullable|string',
            'social_media' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:BELUM_DIHUBUNGI,PENGECEKAN_KEAKTIFAN,PENAWARAN,FOLLOW_UP,TOLAK'
        ]);

        $prospect->update($validated);

        return redirect()->route('prospects.index')->with('success', 'Prospek berhasil diperbarui!');
    }

    public function destroy(Prospect $prospect)
    {
        $prospect->delete();

        return redirect()->route('prospects.index')->with('success', 'Prospek berhasil dihapus!');
    }
}
