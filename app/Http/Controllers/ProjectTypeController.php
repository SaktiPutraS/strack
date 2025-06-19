<?php
// app/Http/Controllers/ProjectTypeController.php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProjectTypeController extends Controller
{
    /**
     * Display a listing of project types
     */
    public function index(): View
    {
        $projectTypes = ProjectType::ordered()->paginate(15);
        return view('project-types.index', compact('projectTypes'));
    }

    /**
     * Store a newly created project type
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:project_types,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // Auto-generate name if display_name provided
        if (empty($validated['name']) && !empty($validated['display_name'])) {
            $validated['name'] = Str::upper(Str::slug($validated['display_name'], '_'));
        }

        // Auto-generate display_name if not provided
        if (empty($validated['display_name'])) {
            $validated['display_name'] = ucwords(strtolower(str_replace(['_', '-'], ' ', $validated['name'])));
        }

        $projectType = ProjectType::create($validated);

        // Handle AJAX/JSON requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipe proyek berhasil ditambahkan!',
                'project_type' => [
                    'id' => $projectType->id,
                    'name' => $projectType->name,
                    'display_name' => $projectType->display_name,
                    'formatted_name' => $projectType->formatted_name,
                    'created_at' => $projectType->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);
        }

        return redirect()->back()
            ->with('success', 'Tipe proyek berhasil ditambahkan!');
    }

    /**
     * Get all active project types for API
     */
    public function getActive(): JsonResponse
    {
        $projectTypes = ProjectType::active()
            ->ordered()
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'display_name' => $type->display_name,
                    'formatted_name' => $type->formatted_name,
                ];
            });

        return response()->json($projectTypes);
    }

    /**
     * Search project types
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        $projectTypes = ProjectType::active()
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            })
            ->ordered()
            ->limit(10)
            ->get()
            ->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'display_name' => $type->display_name,
                    'formatted_name' => $type->formatted_name,
                ];
            });

        return response()->json($projectTypes);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(ProjectType $projectType): JsonResponse
    {
        $projectType->update([
            'is_active' => !$projectType->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => $projectType->is_active ? 'Tipe proyek diaktifkan!' : 'Tipe proyek dinonaktifkan!',
            'is_active' => $projectType->is_active
        ]);
    }
}
