<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

/**
 * Teste la route POST /docteurs de bout en bout.
 * Valide que la Presentation (FormRequest + Controller) communique
 * correctement avec l'Application et l'Infrastructure.
 *
 * Fixture : un admin est connecté — Gate::before() lui accorde toutes
 * les permissions sans assignation explicite.
 *
 * On ne teste pas les règles métier ici — elles sont couvertes
 * par les tests unitaires du Domain.
 */
beforeEach(function (): void {
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole($role);
    $this->actingAs($this->admin);
});

// ─── Cas nominal ──────────────────────────────────────────────────────────────

it('inscrit un docteur et redirige', function (): void {
    $response = $this->post('/docteurs', [
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'Coulibaly',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'BF-12345',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('docteurs', [
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'COULIBALY',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'BF-12345',
    ]);
});

it('la redirection contient l\'UUID du docteur créé', function (): void {
    $response = $this->post('/docteurs', [
        'prenom' => 'Ibrahim',
        'nom_de_famille' => 'Coulibaly',
        'specialite' => 'Cardiologie',
        'numero_ordre' => 'BF-12345',
    ]);

    // La redirection pointe vers /doclinic/medecins/{uuid} (paramètre de route).
    $location = $response->headers->get('Location');
    expect($location)->toContain('/medecins/');
});

// ─── Validation FormRequest ───────────────────────────────────────────────────

it('rejette une requête sans prénom', function (): void {
    $this->post('/docteurs', ['nom_de_famille' => 'Coulibaly', 'specialite' => 'Cardiologie', 'numero_ordre' => 'BF-12345'])
        ->assertSessionHasErrors('prenom');
});

it('rejette une requête sans nom de famille', function (): void {
    $this->post('/docteurs', ['prenom' => 'Ibrahim', 'specialite' => 'Cardiologie', 'numero_ordre' => 'BF-12345'])
        ->assertSessionHasErrors('nom_de_famille');
});

it('rejette une requête sans spécialité', function (): void {
    $this->post('/docteurs', ['prenom' => 'Ibrahim', 'nom_de_famille' => 'Coulibaly', 'numero_ordre' => 'BF-12345'])
        ->assertSessionHasErrors('specialite');
});

it('rejette une requête sans numéro d\'ordre', function (): void {
    $this->post('/docteurs', ['prenom' => 'Ibrahim', 'nom_de_famille' => 'Coulibaly', 'specialite' => 'Cardiologie'])
        ->assertSessionHasErrors('numero_ordre');
});
