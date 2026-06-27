<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * PatientController expose les actions HTTP du module Patients.
 *
 * On teste ici le flux complet HTTP → Application → Infrastructure,
 * sans mocker le repository (on veut vérifier l'intégration réelle).
 */

// ─── POST /patients ────────────────────────────────────────────────────────────

it('POST /patients inscrit un patient et redirige', function (): void {
    // La règle métier clé : un POST valide crée un patient en base.
    $response = $this->post('/patients', [
        'prenom' => 'Moussa',
        'nom_de_famille' => 'Traoré',
        'date_de_naissance' => '1990-05-15',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('patients', [
        'prenom' => 'Moussa',
        'nom_de_famille' => 'TRAORÉ',
    ]);
});

it('POST /patients rejette une requête sans prénom', function (): void {
    $response = $this->post('/patients', [
        'nom_de_famille' => 'Traoré',
        'date_de_naissance' => '1990-05-15',
    ]);

    $response->assertSessionHasErrors('prenom');
});

it('POST /patients rejette une date de naissance invalide', function (): void {
    $response = $this->post('/patients', [
        'prenom' => 'Moussa',
        'nom_de_famille' => 'Traoré',
        'date_de_naissance' => 'pas-une-date',
    ]);

    $response->assertSessionHasErrors('date_de_naissance');
});
