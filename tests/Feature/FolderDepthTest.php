<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class FolderDepthTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_folders_when_no_depth_limit()
    {
        config(['livewire-fileuploader.folders.max_depth' => null]);

        $parent = Folder::create(['name' => 'Parent', 'slug' => 'parent', 'parent_id' => null]);
        $child = Folder::create(['name' => 'Child', 'slug' => 'child', 'parent_id' => $parent->id]);
        $grandchild = Folder::create(['name' => 'Grandchild', 'slug' => 'grandchild', 'parent_id' => $child->id]);

        $this->assertDatabaseHas('folders', ['id' => $grandchild->id]);
        $this->assertEquals(2, $grandchild->getDepth());
    }

    public function test_folder_depth_calculation()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $level1 = Folder::create(['name' => 'Level1', 'slug' => 'level1', 'parent_id' => $root->id]);
        $level2 = Folder::create(['name' => 'Level2', 'slug' => 'level2', 'parent_id' => $level1->id]);
        $level3 = Folder::create(['name' => 'Level3', 'slug' => 'level3', 'parent_id' => $level2->id]);

        $this->assertEquals(0, $root->getDepth());
        $this->assertEquals(1, $level1->getDepth());
        $this->assertEquals(2, $level2->getDepth());
        $this->assertEquals(3, $level3->getDepth());
    }

    public function test_cannot_exceed_max_depth_via_livewire()
    {
        config(['livewire-fileuploader.folders.max_depth' => 2]);

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $level1 = Folder::create(['name' => 'Level1', 'slug' => 'level1', 'parent_id' => $root->id]);

        Livewire::test(LivewireFilemanagerComponent::class)
            ->set('currentFolder', $level1)
            ->set('newFolderName', 'TooDeep')
            ->call('saveNewFolder')
            ->assertHasErrors(['newFolderName' => 'Cannot create folder. Maximum folder depth of 2 would be exceeded.']);

        $this->assertDatabaseMissing('folders', ['name' => 'TooDeep']);
    }

    public function test_can_create_folder_at_max_depth_limit()
    {
        config(['livewire-fileuploader.folders.max_depth' => 3]);

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $level1 = Folder::create(['name' => 'Level1', 'slug' => 'level1', 'parent_id' => $root->id]);

        Livewire::test(LivewireFilemanagerComponent::class)
            ->set('currentFolder', $level1)
            ->set('newFolderName', 'Level2')
            ->call('saveNewFolder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('folders', ['name' => 'Level2', 'parent_id' => $level1->id]);
    }

    public function test_validation_prevents_exceeding_max_depth()
    {
        config(['livewire-fileuploader.folders.max_depth' => 2]);

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);
        $level1 = Folder::create(['name' => 'Level1', 'slug' => 'level1', 'parent_id' => $root->id]);

        $request = new \LivewireFilemanager\Filemanager\Http\Requests\Api\StoreFolderRequest;
        $request->merge([
            'name' => 'TooDeep',
            'parent_id' => $level1->id,
        ]);

        $validator = \Illuminate\Support\Facades\Validator::make(
            $request->all(),
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('parent_id', $validator->errors()->toArray());
    }

    public function test_validation_allows_folder_within_depth_limit()
    {
        config(['livewire-fileuploader.folders.max_depth' => 3]);

        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);

        $request = new \LivewireFilemanager\Filemanager\Http\Requests\Api\StoreFolderRequest;
        $request->merge([
            'name' => 'Subfolder',
            'parent_id' => $root->id,
        ]);

        $validator = \Illuminate\Support\Facades\Validator::make(
            $request->all(),
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }

    public function test_can_create_root_folder_with_depth_limit()
    {
        config(['livewire-fileuploader.folders.max_depth' => 1]);

        Livewire::test(LivewireFilemanagerComponent::class)
            ->set('currentFolder', null)
            ->set('newFolderName', 'NewRoot')
            ->call('saveNewFolder')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('folders', ['name' => 'NewRoot', 'parent_id' => null]);
    }

    protected function user()
    {
        return TestUserModel::create(['email' => 'test@example.com']);
    }
}
