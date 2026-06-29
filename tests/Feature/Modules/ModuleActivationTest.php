<?php

declare(strict_types=1);

use Nwidart\Modules\Module;

/**
 * Teste l'activation et la désactivation des modules via l'API nwidart.
 *
 * L'état des modules est persisté dans modules_statuses.json à la racine.
 * Ces tests vérifient que enable()/disable() modifient correctement ce fichier
 * et que les modules peuvent être gérés de manière totalement indépendante.
 *
 * Précaution : afterEach restaure modules_statuses.json pour isoler les tests.
 */

// ─── Restauration de l'état entre chaque test ──────────────────────────────────

beforeEach(function (): void {
    $this->originalStatuses = file_get_contents(base_path('modules_statuses.json'));
});

afterEach(function (): void {
    file_put_contents(base_path('modules_statuses.json'), $this->originalStatuses);
});

// ─── État initial ─────────────────────────────────────────────────────────────

it('le module Patients est activé par défaut', function (): void {
    $module = app('modules')->find('Patients');

    expect($module)->toBeInstanceOf(Module::class)
        ->and($module->isEnabled())->toBeTrue()
        ->and($module->isDisabled())->toBeFalse();
});

it('le module Docteurs est activé par défaut', function (): void {
    $module = app('modules')->find('Docteurs');

    expect($module)->toBeInstanceOf(Module::class)
        ->and($module->isEnabled())->toBeTrue()
        ->and($module->isDisabled())->toBeFalse();
});

it('les deux modules apparaissent dans la liste des modules', function (): void {
    $noms = array_map('strtolower', array_keys(app('modules')->all()));

    expect($noms)->toContain('patients')
        ->and($noms)->toContain('docteurs');
});

// ─── Désactivation ────────────────────────────────────────────────────────────

it('désactiver Patients met isDisabled à true', function (): void {
    $module = app('modules')->find('Patients');
    $module->disable();

    expect($module->isDisabled())->toBeTrue()
        ->and($module->isEnabled())->toBeFalse();
});

it('désactiver Docteurs met isDisabled à true', function (): void {
    $module = app('modules')->find('Docteurs');
    $module->disable();

    expect($module->isDisabled())->toBeTrue()
        ->and($module->isEnabled())->toBeFalse();
});

it('désactiver Patients persiste false dans modules_statuses.json', function (): void {
    app('modules')->find('Patients')->disable();

    $statuses = json_decode(file_get_contents(base_path('modules_statuses.json')), true);

    expect($statuses['Patients'])->toBeFalse();
});

it('désactiver Docteurs persiste false dans modules_statuses.json', function (): void {
    app('modules')->find('Docteurs')->disable();

    $statuses = json_decode(file_get_contents(base_path('modules_statuses.json')), true);

    expect($statuses['Docteurs'])->toBeFalse();
});

// ─── Réactivation ─────────────────────────────────────────────────────────────

it('réactiver Patients après désactivation remet isEnabled à true', function (): void {
    $module = app('modules')->find('Patients');
    $module->disable();
    $module->enable();

    expect($module->isEnabled())->toBeTrue();
});

it('réactiver Docteurs après désactivation remet isEnabled à true', function (): void {
    $module = app('modules')->find('Docteurs');
    $module->disable();
    $module->enable();

    expect($module->isEnabled())->toBeTrue();
});

it('réactiver persiste true dans modules_statuses.json', function (): void {
    $module = app('modules')->find('Patients');
    $module->disable();
    $module->enable();

    $statuses = json_decode(file_get_contents(base_path('modules_statuses.json')), true);

    expect($statuses['Patients'])->toBeTrue();
});

// ─── Indépendance d'activation ────────────────────────────────────────────────

it('désactiver Patients ne désactive pas Docteurs', function (): void {
    app('modules')->find('Patients')->disable();

    expect(app('modules')->find('Docteurs')->isEnabled())->toBeTrue();
});

it('désactiver Docteurs ne désactive pas Patients', function (): void {
    app('modules')->find('Docteurs')->disable();

    expect(app('modules')->find('Patients')->isEnabled())->toBeTrue();
});

it('les deux modules peuvent être désactivés simultanément', function (): void {
    app('modules')->find('Patients')->disable();
    app('modules')->find('Docteurs')->disable();

    $statuses = json_decode(file_get_contents(base_path('modules_statuses.json')), true);

    expect($statuses['Patients'])->toBeFalse()
        ->and($statuses['Docteurs'])->toBeFalse();
});
