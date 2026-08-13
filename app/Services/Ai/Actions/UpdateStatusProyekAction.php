<?php

namespace App\Services\Ai\Actions;

use App\Models\Project;
use RuntimeException;

/**
 * Ubah status pengerjaan proyek (LEAD/WAITING/PROGRESS/FINISHED/CANCELLED).
 */
class UpdateStatusProyekAction extends WriteAction
{
    use ResolvesProject;

    private const STATUSES = [
        'LEAD' => 'Penawaran',
        'WAITING' => 'Menunggu',
        'PROGRESS' => 'Dikerjakan',
        'FINISHED' => 'Selesai',
        'CANCELLED' => 'Dibatalkan',
    ];

    public function name(): string
    {
        return 'update_status_proyek';
    }

    public function toolDefinition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Ubah STATUS pengerjaan sebuah proyek. Mis. jadikan Deal (WAITING), mulai '
                . 'dikerjakan (PROGRESS), tandai selesai (FINISHED), atau batalkan (CANCELLED).',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'proyek' => ['type' => 'string', 'description' => 'Nama proyek atau nama klien.'],
                    'status' => ['type' => 'string', 'enum' => array_keys(self::STATUSES), 'description' => 'Status baru.'],
                ],
                'required' => ['proyek', 'status'],
            ],
        ];
    }

    public function prepare(array $input): array
    {
        $project = $this->resolveProject((string) ($input['proyek'] ?? ''));

        $status = strtoupper((string) ($input['status'] ?? ''));
        if (! array_key_exists($status, self::STATUSES)) {
            throw new RuntimeException('Status tidak dikenali.');
        }

        if ($project->status === $status) {
            throw new RuntimeException("Proyek \"{$project->title}\" sudah berstatus " . self::STATUSES[$status] . '.');
        }

        return [
            'project_id' => $project->id,
            'project_title' => $project->title,
            'status_lama' => $project->status,
            'status' => $status,
        ];
    }

    public function preview(array $p): string
    {
        return "Ubah status proyek:\n"
            . "- Proyek: {$p['project_title']}\n"
            . "- Dari: " . (self::STATUSES[$p['status_lama']] ?? $p['status_lama']) . "\n"
            . "- Menjadi: " . self::STATUSES[$p['status']] . "\n\n"
            . "Lanjut? Balas *ya* atau *tidak*.";
    }

    public function execute(array $p): string
    {
        $project = Project::findOrFail($p['project_id']);
        $project->status = $p['status'];
        $project->save();

        return "Status \"{$p['project_title']}\" diubah menjadi " . self::STATUSES[$p['status']] . '.';
    }
}
