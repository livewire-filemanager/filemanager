<?php

namespace LivewireFilemanager\Filemanager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MigrateConfigCommand extends Command
{
    protected $signature = 'filemanager:migrate-config';

    protected $description = 'Migrate legacy fileuploader naming to filemanager naming for v1.0.0';

    public function __construct(private Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $changes = [];

        $this->line('');
        $this->line('Migrating livewire-fileuploader to livewire-filemanager...');
        $this->line('');

        if ($this->migrateConfigFile($changes)) {
            $this->line('<fg=green>✓</> Config file migrated');
        }

        if ($this->migratePublishedViews($changes)) {
            $this->line('<fg=green>✓</> Published views migrated');
        }

        if ($this->migratePublishedTranslations($changes)) {
            $this->line('<fg=green>✓</> Published translations migrated');
        }

        if (empty($changes)) {
            $this->line('<fg=yellow>⚠</> No legacy files found to migrate');
            $this->line('');

            return self::SUCCESS;
        }

        $this->line('');
        $this->line('<fg=green>Migration completed successfully!</>');
        $this->line('');
        $this->line('Summary of changes:');
        foreach ($changes as $change) {
            $this->line("  • {$change}");
        }
        $this->line('');

        return self::SUCCESS;
    }

    private function migrateConfigFile(array &$changes): bool
    {
        $oldPath = config_path('livewire-fileuploader.php');
        $newPath = config_path('livewire-filemanager.php');

        if (! $this->files->exists($oldPath)) {
            return false;
        }

        if ($this->files->exists($newPath)) {
            $this->line('<fg=yellow>⚠</> New config file already exists, skipping config migration');

            return false;
        }

        $this->files->move($oldPath, $newPath);
        $changes[] = "Moved {$oldPath} → {$newPath}";

        return true;
    }

    private function migratePublishedViews(array &$changes): bool
    {
        $oldPath = resource_path('views/vendor/livewire-fileuploader');
        $newPath = resource_path('views/vendor/livewire-filemanager');

        if (! $this->files->isDirectory($oldPath)) {
            return false;
        }

        if ($this->files->isDirectory($newPath)) {
            $this->line('<fg=yellow>⚠</> New views directory already exists, skipping views migration');

            return false;
        }

        $this->files->moveDirectory($oldPath, $newPath);
        $changes[] = "Moved {$oldPath} → {$newPath}";

        return true;
    }

    private function migratePublishedTranslations(array &$changes): bool
    {
        $oldPath = resource_path('lang/vendor/livewire-fileuploader');
        $newPath = resource_path('lang/vendor/livewire-filemanager');

        if (! $this->files->isDirectory($oldPath)) {
            return false;
        }

        if ($this->files->isDirectory($newPath)) {
            $this->line('<fg=yellow>⚠</> New translations directory already exists, skipping translations migration');

            return false;
        }

        $this->files->moveDirectory($oldPath, $newPath);
        $changes[] = "Moved {$oldPath} → {$newPath}";

        return true;
    }
}
