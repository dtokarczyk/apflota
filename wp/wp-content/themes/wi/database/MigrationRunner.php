<?php

declare(strict_types=1);

/**
 * Prisma-style migration runner: tracks executed migrations in DB, one file per migration.
 */

if (! defined('ABSPATH')) {
    return;
}

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class MigrationRunner
{
    public const MIGRATIONS_TABLE = 'wi_migrations';

    /** @var string */
    private $migrationsPath;

    public function __construct()
    {
        $this->migrationsPath = dirname(__DIR__) . '/database/migrations';
    }

    /**
     * Ensure the migrations tracking table exists.
     */
    public function ensureMigrationsTable(): void
    {
        if (Capsule::schema()->hasTable(self::MIGRATIONS_TABLE)) {
            return;
        }

        Capsule::schema()->create(self::MIGRATIONS_TABLE, function (Blueprint $table): void {
            $table->id();
            $table->string('migration', 255);
            $table->integer('batch');
            $table->dateTime('executed_at');
        });
    }

    /**
     * Get list of migration filenames (e.g. 2026_02_23_000001_create_calc_rates_table.php).
     *
     * @return array<int, string>
     */
    public function getMigrationFiles(): array
    {
        $path = $this->migrationsPath;
        if (! is_dir($path)) {
            return [];
        }

        $files = glob($path . '/*.php');
        $files = array_map('basename', $files);
        sort($files);

        return array_values($files);
    }

    /**
     * Get list of already executed migration names from DB.
     *
     * @return array<string>
     */
    public function getRanMigrations(): array
    {
        $this->ensureMigrationsTable();

        return Capsule::table(self::MIGRATIONS_TABLE)
            ->orderBy('id')
            ->pluck('migration')
            ->all();
    }

    /**
     * Get pending migration filenames (not yet run).
     *
     * @return array<int, string>
     */
    public function getPendingMigrations(): array
    {
        $files = $this->getMigrationFiles();
        $ran   = $this->getRanMigrations();

        return array_values(array_diff($files, $ran));
    }

    /**
     * Run all pending migrations.
     *
     * @return array{run: int, errors: array<int, string>}
     */
    public function runPending(): array
    {
        $this->ensureMigrationsTable();
        $pending = $this->getPendingMigrations();
        $errors  = [];
        $run     = 0;

        if (empty($pending)) {
            return ['run' => 0, 'errors' => []];
        }

        $batch = (int) Capsule::table(self::MIGRATIONS_TABLE)->max('batch') + 1;

        foreach ($pending as $file) {
            $fullPath = $this->migrationsPath . '/' . $file;
            if (! is_file($fullPath)) {
                continue;
            }

            try {
                $migration = require $fullPath;
                if (! is_object($migration) || ! method_exists($migration, 'up')) {
                    $errors[] = $file . ': invalid migration (must return object with up() method)';
                    continue;
                }
                $migration->up();
                Capsule::table(self::MIGRATIONS_TABLE)->insert([
                    'migration'   => $file,
                    'batch'       => $batch,
                    'executed_at' => date('Y-m-d H:i:s'),
                ]);
                $run++;
            } catch (Throwable $e) {
                $errors[] = $file . ': ' . $e->getMessage();
            }
        }

        return ['run' => $run, 'errors' => $errors];
    }

    /**
     * Rollback the last batch of migrations.
     *
     * @return array{rolled_back: int, errors: array<int, string>}
     */
    public function rollbackLast(): array
    {
        $this->ensureMigrationsTable();

        $lastBatch = Capsule::table(self::MIGRATIONS_TABLE)->max('batch');
        if ($lastBatch === null) {
            return ['rolled_back' => 0, 'errors' => []];
        }

        $ran = Capsule::table(self::MIGRATIONS_TABLE)
            ->where('batch', $lastBatch)
            ->orderByDesc('id')
            ->pluck('migration')
            ->all();

        $errors = [];
        $count  = 0;

        foreach ($ran as $file) {
            $fullPath = $this->migrationsPath . '/' . $file;
            if (! is_file($fullPath)) {
                $errors[] = $file . ': file not found';
                Capsule::table(self::MIGRATIONS_TABLE)->where('migration', $file)->delete();
                $count++;
                continue;
            }

            try {
                $migration = require $fullPath;
                if (is_object($migration) && method_exists($migration, 'down')) {
                    $migration->down();
                }
                Capsule::table(self::MIGRATIONS_TABLE)->where('migration', $file)->delete();
                $count++;
            } catch (Throwable $e) {
                $errors[] = $file . ': ' . $e->getMessage();
            }
        }

        return ['rolled_back' => $count, 'errors' => $errors];
    }

    /**
     * Return status: list of migrations with 'run' or 'pending'.
     *
     * @return array<int, array{migration: string, status: string}>
     */
    public function status(): array
    {
        $this->ensureMigrationsTable();
        $files = $this->getMigrationFiles();
        $ran   = $this->getRanMigrations();

        $result = [];
        foreach ($files as $file) {
            $result[] = [
                'migration' => $file,
                'status'    => in_array($file, $ran, true) ? 'run' : 'pending',
            ];
        }

        return $result;
    }

    /**
     * Rollback all migrations (until none left).
     *
     * @return array{rolled_back: int, errors: array<int, string>}
     */
    public function reset(): array
    {
        $totalRolled = 0;
        $allErrors   = [];

        while (true) {
            $result = $this->rollbackLast();
            $totalRolled += $result['rolled_back'];
            $allErrors = array_merge($allErrors, $result['errors']);
            if ($result['rolled_back'] === 0) {
                break;
            }
        }

        return ['rolled_back' => $totalRolled, 'errors' => $allErrors];
    }
}
