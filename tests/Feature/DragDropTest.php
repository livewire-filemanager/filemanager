<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class DragDropTest extends TestCase
{
    use RefreshDatabase;

    protected $rootFolder;

    protected $sourceFolder;

    protected $targetFolder;

    protected $nestedFolder;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->rootFolder = Folder::create([
            'name' => 'Root',
            'parent_id' => null,
        ]);

        $this->sourceFolder = Folder::create([
            'name' => 'Source Folder',
            'parent_id' => $this->rootFolder->id,
        ]);

        $this->targetFolder = Folder::create([
            'name' => 'Target Folder',
            'parent_id' => $this->rootFolder->id,
        ]);

        $this->nestedFolder = Folder::create([
            'name' => 'Nested Folder',
            'parent_id' => $this->sourceFolder->id,
        ]);
    }

    /** @test */
    public function can_move_single_folder_to_another_folder()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->targetFolder->id, [$this->sourceFolder->id], [])
            ->assertSet('selectedFolders', [])
            ->assertSet('selectedFiles', []);

        $this->assertEquals($this->targetFolder->id, $this->sourceFolder->fresh()->parent_id);
    }

    /** @test */
    public function can_move_multiple_folders_to_another_folder()
    {
        $anotherFolder = Folder::create([
            'name' => 'Another Folder',
            'parent_id' => $this->rootFolder->id,
        ]);

        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->targetFolder->id, [$this->sourceFolder->id, $anotherFolder->id], [])
            ->assertSet('selectedFolders', [])
            ->assertSet('selectedFiles', []);

        $this->assertEquals($this->targetFolder->id, $this->sourceFolder->fresh()->parent_id);
        $this->assertEquals($this->targetFolder->id, $anotherFolder->fresh()->parent_id);
    }

    /** @test */
    public function can_move_file_to_folder()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $media = $this->sourceFolder->addMedia($file->getRealPath())
            ->usingName('test.jpg')
            ->toMediaCollection('medialibrary');

        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->targetFolder->id, [], [$media->id])
            ->assertSet('selectedFolders', [])
            ->assertSet('selectedFiles', []);

        $this->assertEquals($this->targetFolder->id, $media->fresh()->model_id);
    }

    /** @test */
    public function can_move_multiple_files_to_folder()
    {
        $file1 = UploadedFile::fake()->image('test1.jpg');
        $media1 = $this->sourceFolder->addMedia($file1->getRealPath())
            ->usingName('test1.jpg')
            ->toMediaCollection('medialibrary');

        $file2 = UploadedFile::fake()->image('test2.jpg');
        $media2 = $this->sourceFolder->addMedia($file2->getRealPath())
            ->usingName('test2.jpg')
            ->toMediaCollection('medialibrary');

        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->targetFolder->id, [], [$media1->id, $media2->id])
            ->assertSet('selectedFolders', [])
            ->assertSet('selectedFiles', []);

        $this->assertEquals($this->targetFolder->id, $media1->fresh()->model_id);
        $this->assertEquals($this->targetFolder->id, $media2->fresh()->model_id);
    }

    /** @test */
    public function can_move_mixed_items_to_folder()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $media = $this->sourceFolder->addMedia($file->getRealPath())
            ->usingName('test.jpg')
            ->toMediaCollection('medialibrary');

        $anotherFolder = Folder::create([
            'name' => 'Another Folder',
            'parent_id' => $this->rootFolder->id,
        ]);

        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->targetFolder->id, [$anotherFolder->id], [$media->id])
            ->assertSet('selectedFolders', [])
            ->assertSet('selectedFiles', []);

        $this->assertEquals($this->targetFolder->id, $anotherFolder->fresh()->parent_id);
        $this->assertEquals($this->targetFolder->id, $media->fresh()->model_id);
    }

    /** @test */
    public function cannot_move_folder_to_itself()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->sourceFolder->id, [$this->sourceFolder->id], []);

        $this->assertEquals($this->rootFolder->id, $this->sourceFolder->fresh()->parent_id);
    }

    /** @test */
    public function cannot_move_parent_folder_to_its_child()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->nestedFolder->id, [$this->sourceFolder->id], []);

        $this->assertEquals($this->rootFolder->id, $this->sourceFolder->fresh()->parent_id);
    }

    /** @test */
    public function moving_folder_also_moves_its_children()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', $this->targetFolder->id, [$this->sourceFolder->id], []);

        $this->assertEquals($this->targetFolder->id, $this->sourceFolder->fresh()->parent_id);
        $this->assertEquals($this->sourceFolder->id, $this->nestedFolder->fresh()->parent_id);
    }

    /** @test */
    public function selection_is_cleared_after_move()
    {
        $component = Livewire::test(LivewireFilemanagerComponent::class)
            ->set('selectedFolders', [$this->sourceFolder->id])
            ->set('selectedFiles', []);

        $component->call('moveItemsToFolder', $this->targetFolder->id, [$this->sourceFolder->id], [])
            ->assertSet('selectedFolders', [])
            ->assertSet('selectedFiles', []);
    }

    /** @test */
    public function move_to_non_existent_folder_does_nothing()
    {
        $originalParentId = $this->sourceFolder->parent_id;

        Livewire::test(LivewireFilemanagerComponent::class)
            ->call('moveItemsToFolder', 999999, [$this->sourceFolder->id], []);

        $this->assertEquals($originalParentId, $this->sourceFolder->fresh()->parent_id);
    }
}
