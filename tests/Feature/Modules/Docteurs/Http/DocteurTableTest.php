<?php

declare(strict_types=1);

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Docteurs\Http\Livewire\DocteurTable;
use Modules\Docteurs\Models\DocteurModel;

uses(RefreshDatabase::class);

/**
 * Tests du composant Livewire DocteurTable.
 *
 * Fixture : le composant liste les médecins depuis la table docteurs
 * en utilisant DocteurModel::paginate() (lecture CQRS, sans passer
 * par le Domain). Ces tests vérifient le rendu et la recherche.
 *
 * Règle métier protégée : seul le module Docteurs accède à ses données ;
 * la vue clinique délègue entièrement au composant.
 */

// ─── Rendu initial ────────────────────────────────────────────────────────────

it('rend le composant DocteurTable sans erreur', function (): void {
    Livewire::test(DocteurTable::class)
        ->assertStatus(200);
});

it('affiche les médecins inscrits dans le tableau', function (): void {
    DocteurModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'Coulibaly',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'ORD-001',
    ]);

    Livewire::test(DocteurTable::class)
        ->assertSee('Ibrahim')
        ->assertSee('Coulibaly')
        ->assertSee('Cardiologie');
});

it('affiche un message quand il n\'y a aucun médecin', function (): void {
    Livewire::test(DocteurTable::class)
        ->assertSee('Aucun médecin');
});

// ─── Recherche en temps réel ──────────────────────────────────────────────────

it('filtre les médecins par prénom', function (): void {
    DocteurModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000001',
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'Coulibaly',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'ORD-001',
    ]);
    DocteurModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000002',
        'prenom' => 'Fatoumata',
        'nom_de_famille' => 'Koné',
        'specialite' => 'Pédiatrie',
        'numero_ordre' => 'ORD-002',
    ]);

    Livewire::test(DocteurTable::class)
        ->set('search', 'Ibrahim')
        ->assertSee('Ibrahim')
        ->assertDontSee('Fatoumata');
});

it('filtre les médecins par spécialité', function (): void {
    DocteurModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000001',
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'Coulibaly',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'ORD-001',
    ]);
    DocteurModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000002',
        'prenom' => 'Fatoumata',
        'nom_de_famille' => 'Koné',
        'specialite' => 'Pédiatrie',
        'numero_ordre' => 'ORD-002',
    ]);

    Livewire::test(DocteurTable::class)
        ->set('search', 'Cardiologie')
        ->assertSee('Ibrahim')
        ->assertDontSee('Fatoumata');
});

it('filtre les médecins par nom de famille', function (): void {
    DocteurModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000001',
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'Coulibaly',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'ORD-001',
    ]);
    DocteurModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000002',
        'prenom' => 'Fatoumata',
        'nom_de_famille' => 'Koné',
        'specialite' => 'Pédiatrie',
        'numero_ordre' => 'ORD-002',
    ]);

    Livewire::test(DocteurTable::class)
        ->set('search', 'Koné')
        ->assertSee('Fatoumata')
        ->assertDontSee('Ibrahim');
});

// ─── Pagination ───────────────────────────────────────────────────────────────

it('expose une propriété docteurs de type LengthAwarePaginator', function (): void {
    $component = Livewire::test(DocteurTable::class);

    $docteurs = $component->get('docteurs');

    expect($docteurs)->toBeInstanceOf(LengthAwarePaginator::class);
});
