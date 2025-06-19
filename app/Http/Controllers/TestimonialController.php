<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::with(['client', 'testimonial']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($request) {
                        $clientQuery->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        // Filter by testimonial status
        if ($request->filled('has_testimonial')) {
            $query->where('has_testimonial', $request->has_testimonial);
        }

        // Filter by project status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();

        // Statistics
        $projectsWithTestimonial = Project::where('has_testimonial', true)->count();
        $projectsWithoutTestimonial = Project::where('has_testimonial', false)->count();
        $finishedProjectsReady = Project::where('status', 'FINISHED')
            ->where('has_testimonial', false)
            ->count();

        // Get clients for filter
        $clients = \App\Models\Client::orderBy('name')->get();

        return view('testimonials.index', compact(
            'projects',
            'projectsWithTestimonial',
            'projectsWithoutTestimonial',
            'finishedProjectsReady',
            'clients'
        ));
    }

    public function create(Request $request): View
    {
        $finishedProjects = Project::with('client')
            ->where('status', 'FINISHED')
            ->whereDoesntHave('testimonial')
            ->orderBy('updated_at', 'desc')
            ->get();

        $selectedProject = null;
        if ($request->filled('project')) {
            $selectedProject = Project::find($request->project);
        }

        return view('testimonials.create', compact('finishedProjects', 'selectedProject'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id|unique:testimonials,project_id',
            'content' => 'nullable|string|max:500',
            'rating' => 'required|integer|min:1|max:5',
            'is_published' => 'boolean',
        ]);

        // Set defaults for simplified version
        $validated['content'] = $validated['content'] ?: 'Testimoni sudah diterima untuk proyek ini.';
        $validated['rating'] = 5; // Default rating
        $validated['is_published'] = false; // Always false since it's just a flag

        $project = Project::findOrFail($validated['project_id']);
        if ($project->status !== 'FINISHED') {
            return back()->withErrors([
                'project_id' => 'Hanya proyek yang sudah selesai yang bisa ditandai memiliki testimoni.'
            ])->withInput();
        }

        $testimonial = Testimonial::create($validated);

        return redirect()->route('testimonials.index')
            ->with('success', 'Proyek berhasil ditandai sudah memiliki testimoni!');
    }

    public function edit(Testimonial $testimonial): View
    {
        $finishedProjects = Project::with('client')
            ->where('status', 'FINISHED')
            ->where(function ($query) use ($testimonial) {
                $query->whereDoesntHave('testimonial')
                    ->orWhere('id', $testimonial->project_id);
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('testimonials.edit', compact('testimonial', 'finishedProjects'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'nullable|string|max:500',
        ]);

        // Keep the same project_id, rating, and published status
        $validated['content'] = $validated['content'] ?: 'Testimoni sudah diterima untuk proyek ini.';

        $testimonial->update($validated);

        return redirect()->route('testimonials.index')
            ->with('success', 'Catatan testimoni berhasil diperbarui!');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse|JsonResponse
    {
        $testimonial->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penanda testimoni berhasil dihapus!'
            ]);
        }

        return redirect()->route('testimonials.index')
            ->with('success', 'Penanda testimoni berhasil dihapus! Proyek akan ditandai belum memiliki testimoni.');
    }

    // Simple API for getting projects without testimonials
    public function getProjectsNeedingTestimonials(): JsonResponse
    {
        $projects = Project::with('client')
            ->where('status', 'FINISHED')
            ->where('has_testimonial', false)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'client_name' => $project->client->name,
                    'type' => $project->type,
                    'finished_date' => $project->updated_at->format('d M Y'),
                    'total_value' => $project->formatted_total_value,
                ];
            });

        return response()->json($projects);
    }
}
