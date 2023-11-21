<?php

namespace LivewireFilemanager\Filemanager\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use LivewireFilemanager\Filemanager\FilemanagerServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilemanagerServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:9WLU/L6N5GhQRuJPlw6u6G9RILCO5F6FTeA3UxAl9sg=');
    }
}
