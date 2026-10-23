<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use LivewireFilemanager\Filemanager\Http\Controllers\Files\FileController;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PublicFileRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function defineRoutes($router)
    {
        Route::get('{path}', [FileController::class, 'show'])->where('path', '.*')->name('assets.show');
    }

    private function addFile(Folder $folder, string $name): Media
    {
        return $folder->addMedia($this->getTestFilesDirectory().'/test.pdf')
            ->preservingOriginal()
            ->usingFileName($name)
            ->toMediaCollection('medialibrary');
    }

    public function test_it_serves_a_file_by_the_url_from_get_media_full_path()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $projects = Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);
        $invoices = Folder::create(['name' => 'Invoices', 'slug' => 'invoices', 'parent_id' => $projects->id]);

        $media = $this->addFile($invoices, 'invoice.pdf');

        $this->assertSame(config('app.url').'/projects/invoices/invoice.pdf', getMediaFullPath($media));

        $this->get(getMediaFullPath($media))->assertOk();
    }

    public function test_it_serves_a_file_in_the_root_folder_by_the_url_from_get_media_full_path()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);

        $media = $this->addFile($root, 'test.pdf');

        $this->assertSame(config('app.url').'/root/test.pdf', getMediaFullPath($media));

        $this->get(getMediaFullPath($media))->assertOk();
    }

    public function test_it_resolves_the_correct_folder_when_the_same_slug_exists_under_different_parents()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $projects = Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);
        $archived = Folder::create(['name' => 'Archived', 'slug' => 'archived', 'parent_id' => $root->id]);

        Folder::create(['name' => 'Invoices', 'slug' => 'invoices', 'parent_id' => $projects->id]);
        $archivedInvoices = Folder::create(['name' => 'Invoices', 'slug' => 'invoices', 'parent_id' => $archived->id]);

        $media = $this->addFile($archivedInvoices, 'old-invoice.pdf');

        $this->get(getMediaFullPath($media))->assertOk();
    }

    public function test_it_does_not_serve_a_file_with_the_same_name_from_another_folder()
    {
        $this->withExceptionHandling();

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);
        $archived = Folder::create(['name' => 'Archived', 'slug' => 'archived', 'parent_id' => $root->id]);

        $this->addFile($archived, 'invoice.pdf');

        $this->get('/projects/invoice.pdf')->assertNotFound();
    }

    public function test_it_returns_404_for_a_missing_file()
    {
        $this->withExceptionHandling();

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);

        $this->get('/projects/missing.pdf')->assertNotFound();
    }

    public function test_find_by_path_returns_null_for_a_nonexistent_path()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        Folder::create(['name' => 'Projects', 'slug' => 'projects', 'parent_id' => $root->id]);

        $this->assertNull(Folder::findByPath('projects/does-not-exist'));
        $this->assertNull(Folder::findByPath('root/projects'));
        $this->assertNull(Folder::findByPath(''));
    }
}
