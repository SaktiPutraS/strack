<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectTypeController extends Controller
{
    public function index(): View
    {
        $projectTypes = ProjectType::orderBy('sort_order')->orderBy('name')->get();
        return view('project-types.index', compact('projectTypes'));
    }

    public function create(): View
    {
        return view('project-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_types,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        if (empty($validated['sort_order'])) {
            $maxOrder = ProjectType::max('sort_order') ?: 0;
            $validated['sort_order'] = $maxOrder + 10;
        }

        ProjectType::create($validated);

        return redirect()->route('project-types.index')
            ->with('success', 'Tipe proyek berhasil ditambahkan!');
    }

    public function show(ProjectType $projectType): View
    {
        $projectType->load('projects');
        $projectCount = $projectType->projects()->count();
        return view('project-types.show', compact('projectType', 'projectCount'));
    }

    public function edit(ProjectType $projectType): View
    {
        return view('project-types.edit', compact('projectType'));
    }

    public function update(Request $request, ProjectType $projectType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_types,name,' . $projectType->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $projectType->update($validated);

        return redirect()->route('project-types.index')
            ->with('success', 'Tipe proyek berhasil diperbarui!');
    }

    public function destroy(ProjectType $projectType): RedirectResponse
    {
        if ($projectType->projects()->exists()) {
            return redirect()->route('project-types.index')
                ->with('error', 'Tidak dapat menghapus tipe proyek yang masih digunakan!');
        }

        $projectType->delete();

        return redirect()->route('project-types.index')
            ->with('success', 'Tipe proyek berhasil dihapus!');
    }

    public function toggle(ProjectType $projectType): RedirectResponse
    {
        $projectType->update([
            'is_active' => !$projectType->is_active
        ]);

        $status = $projectType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('project-types.index')
            ->with('success', "Tipe proyek berhasil {$status}!");
    }
}
