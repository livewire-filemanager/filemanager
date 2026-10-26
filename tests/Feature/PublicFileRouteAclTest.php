<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use LivewireFilemanager\Filemanager\Http\Controllers\Files\FileController;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicFileRouteAclTest extends TestCase
{
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('livewire-filemanager.acl_enabled', true);
    }

    protected function defineRoutes($router)
    {
        Route::get('{path}', [FileController::class, 'show'])->where('path', '.*');
    }

    private function addOwnedFile(): Media
    {
        $this->actingAs($this->testUser);

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $projects = Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);

        return $projects->addMedia($this->getTestFilesDirectory().'/test.pdf')
            ->preservingOriginal()
            ->usingFileName('invoice.pdf')
            ->withCustomProperties(['user_id' => $this->testUser->id])
            ->toMediaCollection('medialibrary');
    }

    public function test_the_owner_can_access_the_file()
    {
        $media = $this->addOwnedFile();

        $this->get(getMediaFullPath($media))->assertOk();
    }

    public function test_a_guest_cannot_access_the_file()
    {
        $this->withExceptionHandling();

        $media = $this->addOwnedFile();
        $url = getMediaFullPath($media);

        auth()->logout();

        $this->get($url)->assertNotFound();
    }

    public function test_another_user_cannot_access_the_file()
    {
        $this->withExceptionHandling();

        $media = $this->addOwnedFile();
        $url = getMediaFullPath($media);

        $this->actingAs(TestUserModel::create(['email' => 'other@example.com']));

        $this->get($url)->assertNotFound();
    }

    public function test_a_guest_can_access_the_file_when_acl_is_disabled()
    {
        $media = $this->addOwnedFile();
        $url = getMediaFullPath($media);

        auth()->logout();
        config()->set('livewire-filemanager.acl_enabled', false);

        $this->get($url)->assertOk();
    }
}
