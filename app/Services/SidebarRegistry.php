<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Registre centralisé des éléments de sidebar, résolu en singleton.
 *
 * Chaque module y ajoute ses items dans son ServiceProvider::boot().
 * Quand nwidart désactive un module, son provider ne boot plus,
 * ses items disparaissent automatiquement sans aucune modification ici.
 */
final class SidebarRegistry
{
    /** @var SidebarItem[] */
    private array $items = [];

    public function register(SidebarItem $item): void
    {
        $this->items[] = $item;

        usort($this->items, static fn (SidebarItem $a, SidebarItem $b) => $a->order <=> $b->order);
    }

    /** @return SidebarItem[] */
    public function items(): array
    {
        return $this->items;
    }
}
