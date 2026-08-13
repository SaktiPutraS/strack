<?php

namespace App\Services\Ai\Actions;

/**
 * Daftar semua aksi tulis yang tersedia untuk bot. Tambah aksi baru cukup
 * daftarkan di sini.
 */
class ActionRegistry
{
    /** @var array<string, WriteAction> */
    private array $actions = [];

    public function __construct()
    {
        foreach ([
            new CatatPengeluaranAction(),
            new CatatPendapatanAction(),
            new CatatBayarHutangAction(),
            new UpdateStatusProyekAction(),
            new CatatTransferBankAction(),
            new CatatSierraBerakAction(),
        ] as $action) {
            $this->actions[$action->name()] = $action;
        }
    }

    public function find(string $name): ?WriteAction
    {
        return $this->actions[$name] ?? null;
    }

    /** @return array<int, array> definisi tool untuk Anthropic API */
    public function toolDefinitions(): array
    {
        return array_values(array_map(
            fn (WriteAction $a) => $a->toolDefinition(),
            $this->actions
        ));
    }
}
