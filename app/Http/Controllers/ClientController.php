<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Client::with(['projects']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $clients = $query->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:clients,phone',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'address' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Klien berhasil ditambahkan!',
                'client' => $client
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Klien berhasil ditambahkan!');
    }

    public function show(Client $client): View
    {
        $client->load(['projects.payments', 'projects.testimonial']);

        $activeProjects = $client->activeProjects()->count();
        $finishedProjects = $client->finishedProjects()->count();
        $totalProjects = $client->projects()->count();

        $recentProjects = $client->projects()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $projectsByStatus = [
            'waiting' => $client->projects()->where('status', 'WAITING')->count(),
            'progress' => $client->projects()->where('status', 'PROGRESS')->count(),
            'finished' => $client->projects()->where('status', 'FINISHED')->count(),
            'cancelled' => $client->projects()->where('status', 'CANCELLED')->count(),
        ];

        return view('clients.show', compact(
            'client',
            'activeProjects',
            'finishedProjects',
            'totalProjects',
            'recentProjects',
            'projectsByStatus'
        ));
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:clients,phone,' . $client->id,
            'email' => 'nullable|email|max:255|unique:clients,email,' . $client->id,
            'address' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Data klien berhasil diperbarui!');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->projects()->exists()) {
            return redirect()->route('clients.index')
                ->with('error', 'Tidak dapat menghapus klien yang memiliki proyek!');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Klien berhasil dihapus!');
    }

    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        $clients = Client::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'phone' => $client->phone,
                    'email' => $client->email,
                    'whatsapp_link' => $client->whatsapp_link,
                    'total_projects' => $client->projects->count(),
                    'total_value' => $client->total_project_value,
                    'formatted_total_value' => 'Rp ' . number_format($client->total_project_value, 0, ',', '.')
                ];
            });

        return response()->json($clients);
    }

    public function getClientProjects(Client $client): JsonResponse
    {
        $projects = $client->projects()
            ->with(['payments'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'type' => $project->type,
                    'status' => $project->status,
                    'total_value' => $project->total_value,
                    'paid_amount' => $project->paid_amount,
                    'remaining_amount' => $project->remaining_amount,
                    'progress_percentage' => $project->progress_percentage,
                    'deadline' => $project->deadline->format('Y-m-d'),
                    'deadline_formatted' => $project->deadline->format('d M Y'),
                    'is_overdue' => $project->is_overdue,
                    'is_deadline_near' => $project->is_deadline_near,
                    'has_testimonial' => $project->has_testimonial,
                    'formatted_total_value' => $project->formatted_total_value,
                    'formatted_paid_amount' => $project->formatted_paid_amount,
                    'formatted_remaining_amount' => $project->formatted_remaining_amount,
                ];
            });

        return response()->json([
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'email' => $client->email,
                'whatsapp_link' => $client->whatsapp_link,
                'total_project_value' => $client->total_project_value,
                'total_paid' => $client->total_paid,
                'total_remaining' => $client->total_remaining,
            ],
            'projects' => $projects
        ]);
    }
}
