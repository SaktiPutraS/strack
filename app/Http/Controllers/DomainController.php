<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Domain;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function index(Request $request): View
    {
        $query = Domain::with(['client', 'project']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter status dihitung di PHP (accessor), jadi ambil semua lalu saring.
        $domains = $query
            ->orderByRaw('expires_at IS NULL, expires_at ASC')
            ->get();

        if ($request->filled('status')) {
            $domains = $domains->filter(fn (Domain $d) => $d->status === $request->status)->values();
        }

        // Ringkasan
        $all = Domain::all();
        $summary = [
            'total' => $all->count(),
            'hosted' => $all->where('is_hosted', true)->count(),
            'expiring' => $all->filter(fn (Domain $d) => $d->status === 'EXPIRING_SOON')->count(),
            'expired' => $all->filter(fn (Domain $d) => $d->status === 'EXPIRED')->count(),
        ];

        return view('domains.index', compact('domains', 'summary'));
    }

    public function create(): View
    {
        return view('domains.form', [
            'formTitle' => 'Tambah Domain',
            'action' => route('domains.store'),
            'method' => 'POST',
            'domain' => new Domain(),
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        Domain::create($data);

        return redirect()->route('domains.index')->with('success', 'Domain berhasil ditambahkan!');
    }

    public function edit(Domain $domain): View
    {
        return view('domains.form', [
            'formTitle' => 'Edit Domain',
            'action' => route('domains.update', $domain),
            'method' => 'PUT',
            'domain' => $domain,
            'clients' => Client::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Domain $domain): RedirectResponse
    {
        $domain->update($this->validateData($request));

        return redirect()->route('domains.index')->with('success', 'Domain berhasil diperbarui!');
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domain berhasil dihapus!');
    }

    /**
     * Sinkronisasi daftar domain dari folder hosting (~/domains). Membaca nama
     * folder domain via fungsi file PHP (bukan exec) dan menambah yang belum ada.
     */
    public function sync(): RedirectResponse
    {
        $root = config('services.hosting.domains_path') ?: dirname(base_path(), 2);

        if (! is_dir($root) || ! is_readable($root)) {
            return back()->withErrors(['sync' => "Folder domain hosting tidak terbaca: {$root}"]);
        }

        $existing = Domain::pluck('name')->map(fn ($n) => strtolower($n))->all();
        $added = 0;

        foreach (scandir($root) ?: [] as $entry) {
            if ($entry === '' || $entry[0] === '.' || ! str_contains($entry, '.')) {
                continue;
            }
            if (! is_dir($root . DIRECTORY_SEPARATOR . $entry)) {
                continue;
            }
            if (in_array(strtolower($entry), $existing, true)) {
                Domain::where('name', $entry)->update(['is_hosted' => true]);
                continue;
            }

            Domain::create(['name' => $entry, 'is_hosted' => true]);
            $existing[] = strtolower($entry);
            $added++;
        }

        return redirect()->route('domains.index')
            ->with('success', "Sync selesai. {$added} domain baru ditambahkan dari hosting.");
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:191',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'provider' => 'nullable|string|max:100',
            'registered_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'renewal_cost' => 'nullable|numeric|min:0',
            'is_hosted' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);
    }
}
