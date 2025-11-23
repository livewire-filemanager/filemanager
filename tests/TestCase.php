<?php

namespace LivewireFilemanager\Filemanager\Tests;

use Exception;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\LivewireServiceProvider;
use LivewireFilemanager\Filemanager\FilemanagerServiceProvider;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    private static array $migrations = [];

    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);

        $this->withoutExceptionHandling();
    }

    protected function setUpDatabase($app)
    {
        $schema = $app['db']->connection()->getSchemaBuilder();

        $schema->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->timestamps();
        });
        $schema->create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->uuid()->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->nullableTimestamps();
        });

        $this->runMigration(__DIR__.'/../database/migrations/create_folders_table.stub');
        $this->runMigration(__DIR__.'/../database/migrations/include_user_id_column_in_folders_table.stub');

        $this->testUser = TestUserModel::create(['email' => 'user@example.com']);
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilemanagerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:9WLU/L6N5GhQRuJPlw6u6G9RILCO5F6FTeA3UxAl9sg=');

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('livewire-filemanager.api.enabled', true);
        $app['config']->set('livewire-filemanager.api.prefix', 'filemanager/v1');
        $app['config']->set('livewire-filemanager.api.middleware', ['api', 'auth']);
        $app['config']->set('livewire-filemanager.acl_enabled', false);
        $app['config']->set('livewire-filemanager.folders.max_depth', null);

        $app['config']->set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => storage_path('app'),
        ]);

        $app['config']->set('media-library.disk_name', 'local');
        $app['config']->set('media-library.file_namer', \Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer::class);
        $app['config']->set('media-library.path_generator', \Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class);
        $app['config']->set('media-library.url_generator', \Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class);
        $app['config']->set('media-library.max_file_size', 1024 * 1024 * 10);
    }

    protected function getTestFilesDirectory(): string
    {
        return __DIR__.'/TestSupport/testfiles';
    }

    protected function getTestJpg(): string
    {
        return $this->getTestFilesDirectory().'/test.jpg';
    }

    protected function getTestTxt(): string
    {
        return $this->getTestFilesDirectory().'/test.txt';
    }

    protected function getTempDirectory(): string
    {
        return __DIR__.'/TestSupport/temp';
    }

    protected function setUpTempTestFiles(): void
    {
        $this->initializeDirectory($this->getTempDirectory());

        foreach (glob($this->getTestFilesDirectory().'/*') as $file) {
            copy($file, $this->getTempDirectory().'/'.basename($file));
        }
    }

    protected function initializeDirectory(string $directory): void
    {
        if (file_exists($directory)) {
            $this->deleteDirectory($directory);
        }

        mkdir($directory, 0755, true);
    }

    protected function deleteDirectory(string $directory): void
    {
        $iterator = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($directory);
    }

    private function runMigration(string $path, string $className = ''): void
    {
        if (! isset(self::$migrations[$path])) {
            self::$migrations[$path] = require_once $path;
        }

        if (self::$migrations[$path] instanceof Migration) {
            self::$migrations[$path]->up();

            return;
        }

        if ($className) {
            (new $className)->up();

            return;
        }

        throw new Exception("Couldn't run migration {$path}");
    }
}
