<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $query = Testimonial::with(['project.client']);

        if ($request->filled('search')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($request) {
                        $clientQuery->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', $request->is_published);
        }

        if ($request->filled('project_type')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('type', $request->project_type);
            });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $testimonials = $query->paginate(12)->withQueryString();

        $totalTestimonials = Testimonial::count();
        $publishedTestimonials = Testimonial::where('is_published', true)->count();
        $draftTestimonials = Testimonial::where('is_published', false)->count();
        $averageRating = Testimonial::avg('rating');

        $projectTypes = Project::distinct()->pluck('type')->filter();

        $publishedTestimonialsPreview = Testimonial::with(['project.client'])
            ->where('is_published', true)
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('testimonials.index', compact(
            'testimonials',
            'totalTestimonials',
            'publishedTestimonials',
            'draftTestimonials',
            'averageRating',
            'projectTypes',
            'publishedTestimonialsPreview'
        ));
    }

    public function create(Request $request): View
    {
        $finishedProjects = Project::with('client')
            ->where('status', 'FINISHED')
            ->whereDoesntHave('testimonial')
            ->orderBy('deadline', 'desc')
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
            'content' => 'required|string|max:500',
            'rating' => 'required|integer|min:1|max:5',
            'is_published' => 'boolean',
            'client_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('client_photo')) {
            $path = $request->file('client_photo')->store('testimonials', 'public');
            $validated['client_photo'] = $path;
        }

        $project = Project::findOrFail($validated['project_id']);
        if ($project->status !== 'FINISHED') {
            return back()->withErrors([
                'project_id' => 'Hanya proyek yang sudah selesai yang bisa diberi testimoni.'
            ])->withInput();
        }

        $testimonial = Testimonial::create($validated);

        return redirect()->route('testimonials.index')
            ->with('success', 'Testimoni berhasil ditambahkan!');
    }

    public function show(Testimonial $testimonial): View
    {
        $testimonial->load(['project.client']);
        return view('testimonials.show', compact('testimonial'));
    }

    public function edit(Testimonial $testimonial): View
    {
        $finishedProjects = Project::with('client')
            ->where('status', 'FINISHED')
            ->where(function ($query) use ($testimonial) {
                $query->whereDoesntHave('testimonial')
                    ->orWhere('id', $testimonial->project_id);
            })
            ->orderBy('deadline', 'desc')
            ->get();

        return view('testimonials.edit', compact('testimonial', 'finishedProjects'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id|unique:testimonials,project_id,' . $testimonial->id,
            'content' => 'required|string|max:500',
            'rating' => 'required|integer|min:1|max:5',
            'is_published' => 'boolean',
            'client_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['is_published'] = $request->has('is_published');

        if ($request->hasFile('client_photo')) {
            if ($testimonial->client_photo) {
                Storage::disk('public')->delete($testimonial->client_photo);
            }
            $path = $request->file('client_photo')->store('testimonials', 'public');
            $validated['client_photo'] = $path;
        }

        $testimonial->update($validated);

        return redirect()->route('testimonials.show', $testimonial)
            ->with('success', 'Testimoni berhasil diperbarui!');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->client_photo) {
            Storage::disk('public')->delete($testimonial->client_photo);
        }

        $testimonial->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Testimoni berhasil dihapus!'
            ]);
        }

        return redirect()->route('testimonials.index')
            ->with('success', 'Testimoni berhasil dihapus!');
    }

    public function togglePublish(Testimonial $testimonial): JsonResponse
    {
        $testimonial->update([
            'is_published' => !$testimonial->is_published
        ]);

        $status = $testimonial->is_published ? 'dipublikasikan' : 'disembunyikan';

        return response()->json([
            'success' => true,
            'message' => "Testimoni berhasil {$status}!",
            'is_published' => $testimonial->is_published,
            'testimonial' => $testimonial->load(['project.client'])
        ]);
    }

    public function getPublished(): JsonResponse
    {
        $testimonials = Testimonial::with(['project.client'])
            ->where('is_published', true)
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($testimonial) {
                return [
                    'id' => $testimonial->id,
                    'content' => $testimonial->content,
                    'rating' => $testimonial->rating,
                    'client_name' => $testimonial->project->client->name,
                    'project_title' => $testimonial->project->title,
                    'project_type' => $testimonial->project->type,
                    'client_photo' => $testimonial->client_photo ? Storage::url($testimonial->client_photo) : null,
                    'created_date' => $testimonial->created_at->format('d M Y'),
                    'star_rating' => str_repeat('★', $testimonial->rating) . str_repeat('☆', 5 - $testimonial->rating)
                ];
            });

        return response()->json($testimonials);
    }


    public function getHighRating(): JsonResponse
    {
        $testimonials = Testimonial::with(['project.client'])
            ->where('rating', '>=', 4)
            ->where('is_published', true)
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($testimonial) {
                return [
                    'id' => $testimonial->id,
                    'content' => $testimonial->preview_content,
                    'rating' => $testimonial->rating,
                    'client_name' => $testimonial->project->client->name,
                    'project_type' => $testimonial->project->type,
                    'created_date' => $testimonial->created_at->format('M Y')
                ];
            });

        return response()->json($testimonials);
    }
}
