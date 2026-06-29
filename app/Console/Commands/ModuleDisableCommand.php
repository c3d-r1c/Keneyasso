<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;

/**
 * Surcharge de module:disable pour protéger les modules marqués "required": true
 * dans leur module.json. Un module requis ne peut jamais être désactivé.
 *
 * Usage : php artisan module:disable Patients   → OK
 *         php artisan module:disable Auth        → erreur
 */
class ModuleDisableCommand extends Command
{
    protected $signature = 'module:disable {module : Nom du module à désactiver}';

    protected $description = 'Désactive un module (bloqué pour les modules requis).';

    public function handle(): int
    {
        $name   = (string) $this->argument('module');
        $module = Module::find($name);

        if ($module === null) {
            $this->error("Le module [{$name}] n'existe pas.");

            return self::FAILURE;
        }

        $json     = json_decode(file_get_contents($module->getPath().'/module.json'), true);
        $required = (bool) ($json['required'] ?? false);

        if ($required) {
            $this->error("Le module [{$name}] est requis par l'application et ne peut pas être désactivé.");

            return self::FAILURE;
        }

        $module->disable();
        $this->info("Module [{$name}] désactivé.");

        return self::SUCCESS;
    }
}
