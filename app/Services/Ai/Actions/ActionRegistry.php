<?php

namespace App\Services\Ai\Actions;

use App\Services\Ai\AiGateway;

/**
 * Daftar semua aksi tulis yang tersedia untuk bot. Tambah aksi baru cukup
 * daftarkan di sini.
 */
class ActionRegistry
{
    /** @var array<string, WriteAction> */
    private array $actions = [];

    public function __construct(AiGateway $ai)
    {
        foreach ([
            new CatatPengeluaranAction(),
            new CatatPendapatanAction(),
            new CatatBayarHutangAction(),
            new UpdateStatusProyekAction(),
            new CatatTransferBankAction(),
            new CatatSierraBerakAction(),
            new CatatStrukAction($ai),
            new CatatTransferBuktiAction(),
        ] as $action) {
            $this->actions[$action->name()] = $action;
        }
    }

    public function find(string $name): ?WriteAction
    {
        return $this->actions[$name] ?? null;
    }

    /**
     * Definisi tool untuk AI. Aksi tersembunyi (mis. catat struk dari foto)
     * tidak ikut karena tidak bisa dipicu dari teks.
     *
     * @return array<int, array>
     */
    public function toolDefinitions(): array
    {
        return array_values(array_map(
            fn (WriteAction $a) => $a->toolDefinition(),
            array_filter($this->actions, fn (WriteAction $a) => ! $a->hidden())
        ));
    }
}
