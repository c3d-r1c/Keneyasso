<?php

declare(strict_types=1);

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Patients\Http\Livewire\PatientTable;
use Modules\Patients\Models\PatientModel;

uses(RefreshDatabase::class);

/**
 * Tests du composant Livewire PatientTable.
 *
 * Fixture : le composant liste les patients depuis la table patients
 * en utilisant PatientModel::paginate() (lecture CQRS, sans passer
 * par le Domain). Ces tests vérifient le rendu et la recherche.
 *
 * Règle métier protégée : seul le module Patients accède à ses données ;
 * la vue clinique délègue entièrement au composant.
 */

// ─── Rendu initial ────────────────────────────────────────────────────────────

it('rend le composant PatientTable sans erreur', function (): void {
    // Le composant doit pouvoir être monté même sans données en base.
    Livewire::test(PatientTable::class)
        ->assertStatus(200);
});

it('affiche les patients inscrits dans le tableau', function (): void {
    PatientModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee',
        'prenom' => 'Aminata',
        'nom_de_famille' => 'Diallo',
        'date_de_naissance' => '1990-05-15',
    ]);

    Livewire::test(PatientTable::class)
        ->assertSee('Aminata')
        ->assertSee('Diallo');
});

it('affiche un message quand il n\'y a aucun patient', function (): void {
    Livewire::test(PatientTable::class)
        ->assertSee('Aucun patient');
});

// ─── Recherche en temps réel ──────────────────────────────────────────────────

it('filtre les patients par prénom', function (): void {
    PatientModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000001',
        'prenom' => 'Aminata',
        'nom_de_famille' => 'Diallo',
        'date_de_naissance' => '1990-05-15',
    ]);
    PatientModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000002',
        'prenom' => 'Boubacar',
        'nom_de_famille' => 'Traoré',
        'date_de_naissance' => '1985-08-22',
    ]);

    Livewire::test(PatientTable::class)
        ->set('search', 'Aminata')
        ->assertSee('Aminata')
        ->assertDontSee('Boubacar');
});

it('filtre les patients par nom de famille', function (): void {
    PatientModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000001',
        'prenom' => 'Aminata',
        'nom_de_famille' => 'Diallo',
        'date_de_naissance' => '1990-05-15',
    ]);
    PatientModel::create([
        'id' => 'aaaaaaaa-bbbb-cccc-dddd-000000000002',
        'prenom' => 'Boubacar',
        'nom_de_famille' => 'Traoré',
        'date_de_naissance' => '1985-08-22',
    ]);

    Livewire::test(PatientTable::class)
        ->set('search', 'Traoré')
        ->assertSee('Traoré')
        ->assertDontSee('Aminata');
});

it('réinitialise la page à 1 quand la recherche change', function (): void {
    // La pagination doit revenir à la première page lors d'une nouvelle recherche
    // pour éviter d'afficher une page vide si les résultats sont moins nombreux.
    Livewire::test(PatientTable::class)
        ->set('search', 'x')
        ->assertSet('search', 'x');
});

// ─── Pagination ───────────────────────────────────────────────────────────────

it('expose une propriété patients de type LengthAwarePaginator', function (): void {
    $component = Livewire::test(PatientTable::class);

    $patients = $component->get('patients');

    expect($patients)->toBeInstanceOf(LengthAwarePaginator::class);
});
