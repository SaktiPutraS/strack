<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

/**
 * Daftar proyek beserta kliennya. Dipakai skrip lokal
 * `scripts/sinkron-folder-proyek.ps1` untuk mencocokkan folder kerja di PC
 * dengan data strack:
 *   php artisan proyek:daftar --json
 *   php artisan proyek:daftar --status=PROGRESS,WAITING
 */
class ProyekDaftar extends Command
{
    protected $signature = 'proyek:daftar
                            {--status= : Saring status, pisahkan koma (mis. PROGRESS,WAITING,LEAD)}
                            {--json : Keluarkan sebagai JSON, bukan tabel}';

    protected $description = 'Tampilkan daftar proyek (id, klien, judul, status) untuk keperluan skrip';

    public function handle(): int
    {
        $query = Project::with('client:id,name')
            ->select('id', 'client_id', 'title', 'status', 'deadline')
            ->orderBy('id');

        $status = trim((string) $this->option('status'));
        if ($status !== '') {
            $daftar = array_values(array_filter(array_map(
                fn ($s) => strtoupper(trim($s)),
                explode(',', $status)
            )));

            $query->whereIn('status', $daftar);
        }

        $rows = $query->get()->map(fn (Project $p) => [
            'id'       => $p->id,
            'klien'    => (string) ($p->client?->name ?? ''),
            'judul'    => (string) $p->title,
            'status'   => (string) $p->status,
            'deadline' => optional($p->deadline)->toDateString(),
        ])->all();

        if ($this->option('json')) {
            // Tanpa hiasan apa pun supaya gampang dibaca skrip.
            $this->output->writeln(json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->table(['ID', 'Klien', 'Judul', 'Status', 'Deadline'], $rows);
        $this->line(count($rows) . ' proyek.');

        return self::SUCCESS;
    }
}
