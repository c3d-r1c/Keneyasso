<?php

declare(strict_types=1);

/**
 * Tests d'indépendance structurelle des modules.
 *
 * Ces tests analysent statiquement le code source pour détecter
 * les violations d'architecture avant qu'elles n'atteignent la production.
 *
 * Règles vérifiées :
 * 1. Pas de couplage direct entre modules (import croisé interdit)
 * 2. Les couches Domain et Application ignorent Eloquent et les façades Laravel
 * 3. Chaque module déclare ses dépendances dans son propre composer.json
 * 4. Chaque module a un provider autonome qui ne charge que ses propres ressources
 *
 * Ces tests ne nécessitent ni base de données ni bootstrap Laravel complet.
 */

// ─── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Retourne tous les fichiers PHP d'un dossier (récursivement).
 *
 * @return array<string>
 */
function phpFiles(string $dir): array
{
    if (! is_dir($dir)) {
        return [];
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
    );

    $files = [];
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

/**
 * Retourne toutes les lignes `use Xxx\...` d'un fichier.
 *
 * @return array<string>
 */
function useStatements(string $path): array
{
    $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];

    return array_values(array_filter($lines, fn (string $l): bool => str_starts_with(trim($l), 'use ')));
}

// ─── Pas de couplage inter-modules : Patients ↔ Docteurs ─────────────────────

it('le Domain Patients n\'importe rien du module Docteurs', function (): void {
    // Le Domain ne communique avec les autres modules que via événements/contrats Core.
    // Un import direct crée un couplage fort qui brise l'indépendance des modules.
    $violations = [];

    foreach (phpFiles(base_path('Modules/Patients/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Modules\\Docteurs')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('l\'Application Patients n\'importe rien du module Docteurs', function (): void {
    $violations = [];

    foreach (phpFiles(base_path('Modules/Patients/app/Actions')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Modules\\Docteurs')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('le Domain Docteurs n\'importe rien du module Patients', function (): void {
    $violations = [];

    foreach (phpFiles(base_path('Modules/Docteurs/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Modules\\Patients')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('l\'Application Docteurs n\'importe rien du module Patients', function (): void {
    $violations = [];

    foreach (phpFiles(base_path('Modules/Docteurs/app/Actions')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Modules\\Patients')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

// ─── Pas d'Eloquent dans Domain ni Application ────────────────────────────────

it('le Domain Patients n\'utilise pas Eloquent', function (): void {
    // L'Eloquent appartient à l'Infrastructure (Repositories/, Models/).
    // Sa présence dans le Domain introduirait un couplage à Laravel
    // qui rendrait les tests unitaires impossibles sans RefreshDatabase.
    $violations = [];

    foreach (phpFiles(base_path('Modules/Patients/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Illuminate\\Database')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('l\'Application Patients n\'utilise pas Eloquent', function (): void {
    $violations = [];

    foreach (phpFiles(base_path('Modules/Patients/app/Actions')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Illuminate\\Database')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('le Domain Docteurs n\'utilise pas Eloquent', function (): void {
    $violations = [];

    foreach (phpFiles(base_path('Modules/Docteurs/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Illuminate\\Database')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('l\'Application Docteurs n\'utilise pas Eloquent', function (): void {
    $violations = [];

    foreach (phpFiles(base_path('Modules/Docteurs/app/Actions')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Illuminate\\Database')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

// ─── Pas de façades Laravel dans Domain ni Application ────────────────────────

it('le Domain Patients n\'utilise pas les façades Laravel', function (): void {
    // Les façades (DB::, Cache::, Event::…) couplent le Domain à l'IoC container.
    // On autorise uniquement illuminate/contracts (interfaces pures).
    $violations = [];

    foreach (phpFiles(base_path('Modules/Patients/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Illuminate\\Support\\Facades')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('le Domain Docteurs n\'utilise pas les façades Laravel', function (): void {
    $violations = [];

    foreach (phpFiles(base_path('Modules/Docteurs/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            if (str_contains($use, 'Illuminate\\Support\\Facades')) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

// ─── Autonomie des fichiers de configuration de chaque module ─────────────────

it('Patients a son propre composer.json avec la bonne entrée PSR-4', function (): void {
    $path = base_path('Modules/Patients/composer.json');
    expect(file_exists($path))->toBeTrue();

    $config = json_decode(file_get_contents($path), true);
    $psr4 = $config['autoload']['psr-4'] ?? [];

    expect($psr4)->toHaveKey('Modules\\Patients\\');
});

it('Docteurs a son propre composer.json avec la bonne entrée PSR-4', function (): void {
    $path = base_path('Modules/Docteurs/composer.json');
    expect(file_exists($path))->toBeTrue();

    $config = json_decode(file_get_contents($path), true);
    $psr4 = $config['autoload']['psr-4'] ?? [];

    expect($psr4)->toHaveKey('Modules\\Docteurs\\');
});

it('Patients a son propre module.json avec son provider déclaré', function (): void {
    $path = base_path('Modules/Patients/module.json');
    expect(file_exists($path))->toBeTrue();

    $config = json_decode(file_get_contents($path), true);

    expect($config['providers'])->toContain(\Modules\Patients\Providers\PatientsServiceProvider::class);
});

it('Docteurs a son propre module.json avec son provider déclaré', function (): void {
    $path = base_path('Modules/Docteurs/module.json');
    expect(file_exists($path))->toBeTrue();

    $config = json_decode(file_get_contents($path), true);

    expect($config['providers'])->toContain(\Modules\Docteurs\Providers\DocteursServiceProvider::class);
});

// ─── Seul Core est partagé entre les modules ─────────────────────────────────

it('le Domain Patients ne dépend que de App\\Core, PHP natif et illuminate/support', function (): void {
    // Les imports autorisés dans le Domain sont :
    // - App\Core\Domain\ (contrats partagés)
    // - PHP natif (InvalidArgumentException…)
    // - Carbon\ (bibliothèque de dates pure, zéro I/O)
    // - Illuminate\Support\ (Str, Collection — bibliothèques pures)
    // Interdit : Illuminate\Database, Illuminate\Support\Facades (autres tests).
    $violations = [];

    $allowed = ['App\\Core\\', 'InvalidArgumentException', 'Carbon\\', 'Illuminate\\Support\\'];

    foreach (phpFiles(base_path('Modules/Patients/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            $isAllowed = false;
            foreach ($allowed as $prefix) {
                if (str_contains($use, $prefix)) {
                    $isAllowed = true;
                    break;
                }
            }

            // Stringable est une interface PHP native — autorisée
            if (str_contains($use, 'Stringable')) {
                $isAllowed = true;
            }

            if (! $isAllowed) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});

it('le Domain Docteurs ne dépend que de App\\Core, PHP natif et illuminate/support', function (): void {
    $violations = [];

    $allowed = ['App\\Core\\', 'InvalidArgumentException', 'Carbon\\', 'Illuminate\\Support\\'];

    foreach (phpFiles(base_path('Modules/Docteurs/app/Domain')) as $file) {
        foreach (useStatements($file) as $use) {
            $isAllowed = false;
            foreach ($allowed as $prefix) {
                if (str_contains($use, $prefix)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (str_contains($use, 'Stringable')) {
                $isAllowed = true;
            }

            if (! $isAllowed) {
                $violations[] = basename($file).': '.$use;
            }
        }
    }

    expect($violations)->toBeEmpty();
});
