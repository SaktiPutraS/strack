<?php

namespace App\Services\Ai\Actions;

use App\Models\Project;
use RuntimeException;

/**
 * Cari 1 proyek dari sebutan bebas (judul / nama klien). Melempar pesan jelas
 * bila tidak ketemu atau ambigu (lebih dari satu), agar user mempersempit.
 */
trait ResolvesProject
{
    protected function resolveProject(string $query, ?array $onlyStatuses = null): Project
    {
        $query = trim($query);
        if ($query === '') {
            throw new RuntimeException('Sebutkan nama proyek atau kliennya.');
        }

        $builder = Project::with('client')->search($query);

        if ($onlyStatuses) {
            $builder->whereIn('status', $onlyStatuses);
        }

        $matches = $builder->limit(6)->get();

        if ($matches->isEmpty()) {
            throw new RuntimeException("Proyek \"{$query}\" tidak ditemukan.");
        }

        if ($matches->count() > 1) {
            $daftar = $matches
                ->map(fn (Project $p) => '- ' . $p->title . ' (' . ($p->client->name ?? '-') . ', ' . $p->status . ')')
                ->implode("\n");

            throw new RuntimeException("Ada beberapa proyek cocok, sebutkan lebih spesifik:\n{$daftar}");
        }

        return $matches->first();
    }
}
