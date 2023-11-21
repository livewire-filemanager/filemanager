<?php

namespace LivewireFilemanager\Filemanager;

use Livewire\Livewire;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Http\Components\BladeFilemanagerComponent;

class FilemanagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'livewire-filemanager');

        $this
            ->registerBladeComponents()
            ->registerLivewireComponents();
    }

    public function register()
    {
        parent::register();
    }

    public function registerLivewireComponents(): self
    {
        Livewire::component('livewire-filemanager', LivewireFilemanagerComponent::class);

        return $this;
    }

    public function registerBladeComponents(): self
    {
        Blade::component('livewire-filemanager', BladeFilemanagerComponent::class);

        return $this;
    }
}
