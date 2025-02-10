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

            $table->uuid()->nullable()->unique();
            $table->string('name');
            $table->json('custom_properties');

            $table->nullableTimestamps();
        });

        $this->runMigration(__DIR__ . '/../database/migrations/create_folders_table.stub');
        $this->runMigration(__DIR__ . '/../database/migrations/include_user_id_column_in_folders_table.stub');

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
