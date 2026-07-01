<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * Lance tous les outils de qualité du CI localement et affiche un rapport
 * synthétique : quel outil passe, lequel échoue, et pourquoi.
 *
 * Usage : php artisan check:all
 *         php artisan check:all --fix   ← applique Pint + Rector en plus du check
 */
final class CheckAllCommand extends Command
{
    protected $signature = 'check:all {--fix : Applique les corrections Pint et Rector au lieu de simplement les signaler}';

    protected $description = 'Lance Pint, PHPStan, Rector et Pest — même séquence que le CI.';

    /** @var array<string, bool> */
    private array $results = [];

    public function handle(): int
    {
        $fix = (bool) $this->option('fix');

        $this->newLine();
        $this->line('┌─────────────────────────────────────────┐');
        $this->line('│         <fg=cyan>check:all — Contrôle qualité</>          │');
        $this->line('└─────────────────────────────────────────┘');
        $this->newLine();

        $this->runPint($fix);
        $this->runPhpStan();
        $this->runRector($fix);
        $this->runPest();

        return $this->printSummary();
    }

    private function runPint(bool $fix): void
    {
        $args = $fix ? [] : ['--test'];
        $label = $fix ? 'Pint (correction)' : 'Pint (vérification)';

        $this->runTool($label, array_merge($this->bin('pint'), $args));
    }

    private function runPhpStan(): void
    {
        $this->runTool('PHPStan', array_merge($this->bin('phpstan'), ['analyse', '--memory-limit=512M', '--no-progress']));
    }

    private function runRector(bool $fix): void
    {
        $args = $fix ? [] : ['--dry-run'];
        $label = $fix ? 'Rector (correction)' : 'Rector (vérification)';

        $this->runTool($label, array_merge($this->bin('rector'), $args));
    }

    private function runPest(): void
    {
        // Forcer l'environnement de test — nécessaire quand la commande tourne
        // dans un processus artisan qui a chargé .env (APP_ENV=local, DB_DATABASE=fichier).
        // Sans ça, le sous-processus hérite des vars d'artisan et ignore phpunit.xml.
        $testEnv = array_merge(getenv(), [
            'APP_ENV' => 'testing',
            'APP_LOCALE' => 'fr',
            'APP_FALLBACK_LOCALE' => 'fr',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'DB_URL' => '',
            'CACHE_STORE' => 'array',
            'SESSION_DRIVER' => 'array',
            'MAIL_MAILER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
            'BCRYPT_ROUNDS' => '4',
        ]);

        $this->runTool('Pest', $this->bin('pest'), $testEnv);
    }

    private function stripAnsi(string $output): string
    {
        return (string) preg_replace('/\x1B\[[0-9;]*[mGKHF]/u', '', $output);
    }

    /** @return string[] */
    private function bin(string $binary): array
    {
        // Sur Windows les binaires vendor ne sont pas exécutables directement
        return PHP_OS_FAMILY === 'Windows'
            ? [PHP_BINARY, "vendor/bin/{$binary}"]
            : ["vendor/bin/{$binary}"];
    }

    /**
     * @param  string[]  $command
     * @param  array<string,string>  $env
     */
    private function runTool(string $label, array $command, array $env = []): void
    {
        $this->components->task($label, function () use ($label, $command, $env): bool {
            $process = new Process($command, base_path(), env: $env ?: null, timeout: 300);
            $process->run();

            $passed = $process->isSuccessful();
            $this->results[$label] = $passed;

            if (! $passed) {
                $this->newLine();
                $this->line($this->stripAnsi($process->getOutput()));
                $this->line($this->stripAnsi($process->getErrorOutput()));
            }

            return $passed;
        });
    }

    private function printSummary(): int
    {
        $this->newLine();
        $this->line('┌─────────────────────────────────────────┐');
        $this->line('│                  Résumé                  │');
        $this->line('└─────────────────────────────────────────┘');

        $failed = [];

        foreach ($this->results as $tool => $passed) {
            if ($passed) {
                $this->line("  <fg=green>✓</> {$tool}");
            } else {
                $this->line("  <fg=red>✗</> {$tool}");
                $failed[] = $tool;
            }
        }

        $this->newLine();

        if ($failed === []) {
            $this->info('Tous les contrôles sont passés — prêt à pousser.');

            return self::SUCCESS;
        }

        $this->error(count($failed).' contrôle(s) échoué(s) : '.implode(', ', $failed));

        if (! $this->option('fix')) {
            $this->line('  → Relancez avec <fg=yellow>php artisan check:all --fix</> pour corriger Pint et Rector automatiquement.');
        }

        return self::FAILURE;
    }
}
