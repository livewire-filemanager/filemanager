<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicFileRoutePrefixTest extends TestCase
{
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('livewire-filemanager.public_path.enabled', true);
    }

    private function addFile(Folder $folder, string $name): Media
    {
        return $folder->addMedia($this->getTestFilesDirectory().'/test.pdf')
            ->preservingOriginal()
            ->usingFileName($name)
            ->toMediaCollection('medialibrary');
    }

    public function test_it_registers_the_public_file_route()
    {
        $this->assertTrue(Route::has('filemanager.public.show'));
    }

    public function test_copied_urls_include_the_prefix_and_are_served()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $projects = Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);

        $media = $this->addFile($projects, 'invoice.pdf');

        $this->assertSame(config('app.url').'/files/projects/invoice.pdf', getMediaFullPath($media));

        $this->get(getMediaFullPath($media))->assertOk();
    }

    public function test_copied_urls_for_files_in_the_root_folder_are_served()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);

        $media = $this->addFile($root, 'test.pdf');

        $this->assertSame(config('app.url').'/files/root/test.pdf', getMediaFullPath($media));

        $this->get(getMediaFullPath($media))->assertOk();
    }

    public function test_it_uses_a_custom_prefix()
    {
        config()->set('livewire-filemanager.public_path.prefix', '/media/');

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $projects = Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);

        $media = $this->addFile($projects, 'invoice.pdf');

        $this->assertSame(config('app.url').'/media/projects/invoice.pdf', getMediaFullPath($media));
    }

    public function test_it_returns_404_without_the_prefix()
    {
        $this->withExceptionHandling();

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $projects = Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);

        $this->addFile($projects, 'invoice.pdf');

        $this->get('/projects/invoice.pdf')->assertNotFound();
    }
}
