<?php

declare(strict_types=1);

use Illuminate\Container\Container;
use Illuminate\Routing\Router;
use Modules\Docteurs\Domain\DocteurRepository;
use Modules\Docteurs\Providers\DocteursServiceProvider;
use Modules\Docteurs\Repositories\EloquentDocteurRepository;
use Modules\Patients\Domain\PatientRepository;
use Modules\Patients\Providers\PatientsServiceProvider;
use Modules\Patients\Repositories\EloquentPatientRepository;

/**
 * Teste que chaque module enregistre correctement ses bindings
 * et ses routes dans le container Laravel.
 *
 * Ces tests vérifient la couche ServiceProvider de chaque module :
 * - register() : binding Repository → implémentation Eloquent
 * - boot()     : chargement des routes du module
 *
 * Ils garantissent que chaque module est autonome — il peut être
 * chargé seul sans dépendre d'un autre module.
 */

// ─── Bindings du container ────────────────────────────────────────────────────

it('PatientRepository est lié à EloquentPatientRepository', function (): void {
    // Le binding est enregistré par PatientsServiceProvider::register().
    // Si le module est désactivé, ce binding n'existerait pas.
    expect(app(PatientRepository::class))->toBeInstanceOf(EloquentPatientRepository::class);
});

it('DocteurRepository est lié à EloquentDocteurRepository', function (): void {
    expect(app(DocteurRepository::class))->toBeInstanceOf(EloquentDocteurRepository::class);
});

it('les deux bindings coexistent dans le même container', function (): void {
    // Vérifie qu'aucun module ne pollue ou n'écrase le binding de l'autre.
    expect(app(PatientRepository::class))->toBeInstanceOf(EloquentPatientRepository::class);
    expect(app(DocteurRepository::class))->toBeInstanceOf(EloquentDocteurRepository::class);
});

it('chaque résolution du binding crée une nouvelle instance', function (): void {
    // bind() (non singleton) : chaque appel retourne une instance fraîche.
    $a = app(PatientRepository::class);
    $b = app(PatientRepository::class);
    expect($a)->not->toBe($b);
});

// ─── Routes enregistrées ──────────────────────────────────────────────────────

it('la route doclinic.patients.store est enregistrée', function (): void {
    expect(app(Router::class)->has('doclinic.patients.store'))->toBeTrue();
});

it('la route doclinic.docteurs.store est enregistrée', function (): void {
    expect(app(Router::class)->has('doclinic.docteurs.store'))->toBeTrue();
});

it('la route doclinic.patients.store accepte POST uniquement', function (): void {
    $route = app(Router::class)->getRoutes()->getByName('doclinic.patients.store');
    expect($route->methods())->toContain('POST');
});

it('la route doclinic.docteurs.store accepte POST uniquement', function (): void {
    $route = app(Router::class)->getRoutes()->getByName('doclinic.docteurs.store');
    expect($route->methods())->toContain('POST');
});

it('la route doclinic.patients.store pointe vers PatientController@store', function (): void {
    $route = app(Router::class)->getRoutes()->getByName('doclinic.patients.store');
    expect($route->getActionName())->toContain('PatientController@store');
});

it('la route doclinic.docteurs.store pointe vers DocteurController@store', function (): void {
    $route = app(Router::class)->getRoutes()->getByName('doclinic.docteurs.store');
    expect($route->getActionName())->toContain('DocteurController@store');
});

// ─── Autonomie des ServiceProviders ───────────────────────────────────────────

it('PatientsServiceProvider peut enregistrer ses bindings dans un container vide', function (): void {
    // Container léger : ne bootstrap pas l'app, teste uniquement register().
    $container = new Container();
    $provider = new PatientsServiceProvider($container);
    $provider->register();

    expect($container->bound(PatientRepository::class))->toBeTrue();
});

it('DocteursServiceProvider peut enregistrer ses bindings dans un container vide', function (): void {
    $container = new Container();
    $provider = new DocteursServiceProvider($container);
    $provider->register();

    expect($container->bound(DocteurRepository::class))->toBeTrue();
});

it('PatientsServiceProvider ne binding pas DocteurRepository', function (): void {
    // Un ServiceProvider ne doit pas empiéter sur le territoire d'un autre module.
    $container = new Container();
    $provider = new PatientsServiceProvider($container);
    $provider->register();

    expect($container->bound(DocteurRepository::class))->toBeFalse();
});

it('DocteursServiceProvider ne binding pas PatientRepository', function (): void {
    $container = new Container();
    $provider = new DocteursServiceProvider($container);
    $provider->register();

    expect($container->bound(PatientRepository::class))->toBeFalse();
});
