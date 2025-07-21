<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectTypeController extends Controller
{
    /**
     * Display a listing of project types
     */
    public function index(): View
    {
        $projectTypes = ProjectType::orderBy('sort_order')->orderBy('name')->get();

        return view('project-types.index', compact('projectTypes'));
    }

    /**
     * Show the form for creating a new project type
     */
    public function create(): View
    {
        return view('project-types.create');
    }

    /**
     * Store a newly created project type
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_types,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Set default values
        $validated['is_active'] = $request->has('is_active');

        if (empty($validated['sort_order'])) {
            $maxOrder = ProjectType::max('sort_order') ?: 0;
            $validated['sort_order'] = $maxOrder + 10;
        }

        ProjectType::create($validated);

        return redirect()->route('project-types.index')
            ->with('success', 'Tipe proyek berhasil ditambahkan!');
    }

    /**
     * Display the specified project type
     */
    public function show(ProjectType $projectType): View
    {
        $projectType->load('projects');
        $projectCount = $projectType->projects()->count();

        return view('project-types.show', compact('projectType', 'projectCount'));
    }

    /**
     * Show the form for editing the specified project type
     */
    public function edit(ProjectType $projectType): View
    {
        return view('project-types.edit', compact('projectType'));
    }

    /**
     * Update the specified project type
     */
    public function update(Request $request, ProjectType $projectType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_types,name,' . $projectType->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $projectType->update($validated);

        return redirect()->route('project-types.index')
            ->with('success', 'Tipe proyek berhasil diperbarui!');
    }

    /**
     * Remove the specified project type
     */
    public function destroy(ProjectType $projectType): RedirectResponse
    {
        // Check if project type is being used
        if ($projectType->projects()->exists()) {
            return redirect()->route('project-types.index')
                ->with('error', 'Tidak dapat menghapus tipe proyek yang masih digunakan!');
        }

        $projectType->delete();

        return redirect()->route('project-types.index')
            ->with('success', 'Tipe proyek berhasil dihapus!');
    }

    // Ubah parameter menjadi snake_case
    public function toggle(ProjectType $project_type): RedirectResponse
    {
        $project_type->update([
            'is_active' => !$project_type->is_active
        ]);

        $status = $project_type->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('project-types.index')
            ->with('success', "Tipe proyek berhasil {$status}!");
    }
}
