<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Représente un élément de navigation dans la sidebar (avec support de sous-menus).
 *
 * Un item sans children est un lien direct. Un item avec children devient un
 * groupe treeview : son route est ignoré (mettre ''), le clic ouvre le sous-menu.
 *
 * Exemple simple :
 *   new SidebarItem(label: 'Patients', route: 'doclinic.patients', icon: 'icon-Compiling', order: 10)
 *
 * Exemple avec sous-menu :
 *   new SidebarItem(
 *       label: 'Clinique', route: '', icon: 'icon-Hospital', order: 10,
 *       children: [
 *           new SidebarItem(label: 'Patients', route: 'doclinic.patients', icon: 'icon-Compiling'),
 *           new SidebarItem(label: 'Médecins', route: 'doclinic.doctor_list', icon: 'icon-Diagnostics'),
 *       ]
 *   )
 */
final readonly class SidebarItem
{
    /** @param SidebarItem[] $children */
    public function __construct(
        public string $label,
        public string $route,
        public string $icon,
        public int $order = 0,
        public ?string $homeComponent = null,
        public array $children = [],
    ) {}

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }
}
