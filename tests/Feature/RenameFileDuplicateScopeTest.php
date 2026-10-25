<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Livewire\RenameFileComponent;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class RenameFileDuplicateScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_renaming_ignores_same_named_files_in_other_folders()
    {
        Storage::fake('public');

        $folderA = Folder::create(['name' => 'Folder A', 'parent_id' => null]);
        $folderB = Folder::create(['name' => 'Folder B', 'parent_id' => null]);

        // A file already named "test.pdf" exists, but in a different folder.
        $folderA->addMedia($this->getTestFilesDirectory().'/test.pdf')
            ->preservingOriginal()
            ->usingName('test.pdf')
            ->toMediaCollection('medialibrary');

        $fileToRename = $folderB->addMedia($this->getTestFilesDirectory().'/test.pdf')
            ->preservingOriginal()
            ->usingName('draft.pdf')
            ->toMediaCollection('medialibrary');

        Livewire::test(RenameFileComponent::class)
            ->call('renameFile', ['id' => $fileToRename->id])
            ->set('name', 'test')
            ->call('save');

        $this->assertEquals('test.pdf', $fileToRename->fresh()->file_name);
    }

    public function test_renaming_still_avoids_a_conflict_within_the_same_folder()
    {
        Storage::fake('public');

        $folder = Folder::create(['name' => 'Folder', 'parent_id' => null]);

        $folder->addMedia($this->getTestFilesDirectory().'/test.pdf')
            ->preservingOriginal()
            ->usingName('test.pdf')
            ->toMediaCollection('medialibrary');

        $fileToRename = $folder->addMedia($this->getTestFilesDirectory().'/test.pdf')
            ->preservingOriginal()
            ->usingName('draft.pdf')
            ->toMediaCollection('medialibrary');

        Livewire::test(RenameFileComponent::class)
            ->call('renameFile', ['id' => $fileToRename->id])
            ->set('name', 'test')
            ->call('save');

        $this->assertEquals('test-1.pdf', $fileToRename->fresh()->file_name);
    }
}
