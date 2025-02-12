<?php

namespace LivewireFilemanager\Filemanager;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Http\Components\BladeFilemanagerComponent;
use LivewireFilemanager\Filemanager\Http\Components\BladeFilemanagerModalComponent;
use LivewireFilemanager\Filemanager\Livewire\DeleteItemsComponent;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerPanelComponent;
use LivewireFilemanager\Filemanager\Models\Media;
use LivewireFilemanager\Filemanager\Policies\MediaPolicy;

class FilemanagerServiceProvider extends ServiceProvider
{
    protected $policies = [
        Media::class => MediaPolicy::class,
    ];

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-filemanager');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'livewire-filemanager');

        $this
            ->registerPublishables()
            ->registerBladeComponents()
            ->registerBladeDirectives()
            ->registerLivewireComponents()
            ->registerPolicies();
    }

    public function register()
    {
        parent::register();
    }

    protected function registerPublishables(): self
    {
        if (! class_exists('CreateTemporaryUploadsTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_folders_table.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_folders_table.php'),
                __DIR__.'/../database/migrations/include_user_id_column_in_folders_table.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_include_user_id_column_in_folders_table.php'),
            ], 'livewire-fileuploader-migrations');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/livewire-fileuploader'),
            ], 'livewire-fileuploader-views');

            $this->publishes([
                __DIR__.'/../resources/lang' => "{$this->app['path.lang']}/vendor/livewire-fileuploader",
            ], 'livewire-fileuploader-lang');

            $this->publishes([
                __DIR__.'/../config/livewire-fileuploader.stub' => config_path('livewire-fileuploader.php'),
            ], 'livewire-fileuploader-config');
        }

        return $this;
    }

    public function registerLivewireComponents(): self
    {
        Livewire::component('livewire-filemanager', LivewireFilemanagerComponent::class);
        Livewire::component('livewire-filemanager.delete-items', DeleteItemsComponent::class);
        Livewire::component('livewire-filemanager.media-panel', LivewireFilemanagerPanelComponent::class);

        return $this;
    }

    public function registerBladeComponents(): self
    {
        Blade::component('livewire-filemanager', BladeFilemanagerComponent::class);
        Blade::component('livewire-filemanager-modal', BladeFilemanagerModalComponent::class);

        return $this;
    }

    public function registerBladeDirectives(): self
    {
        Blade::directive('filemanagerStyles', function () {
            $styles = '';

            $styles .= <<<'html'
                        <script src="https://cdn.tailwindcss.com"></script>
                    html;

            return $styles;
        });

        Blade::directive('filemanagerScripts', function () {
            $scripts = '';

            $scripts .= <<<'html'
                        <script defer src="https://unpkg.com/@alpinejs/ui@3.13.3-beta.1/dist/cdn.min.js"></script>
                    html;

            return $scripts;
        });

        return $this;
    }

    public function registerPolicies(): self
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }

        return $this;
    }
}
