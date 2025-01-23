<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use LivewireFilemanager\Filemanager\Models\Media;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\TestCase;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\Models\TestFolderModel;

class FilemanagerGlobalScopeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('livewire-fileuploader.acl_enabled', true);
    }
    /**
     * User can only view the folders that they own.
     *
     * @group filemanager
     *
     * @test
     */
    public function user_can_only_view_folders_that_they_own()
    {
        $this->actingAs($this->testUser);

        $folder = TestFolderModel::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
        ]);

        $this->assertNotNull(Folder::find($folder->id));

        $user2 = TestUserModel::create([
            'email' => 'user2@example.com',
        ]);
        $this->actingAs($user2);
        $this->assertNull(Folder::find($folder->id));
    }

    /**
     * User can only view the files that they own.
     * 
     * @group filemanager
     * 
     * @test
     */
    public function user_can_only_view_files_that_they_own()
    {
        $this->actingAs($this->testUser);

        $media = Media::create([
            'name' => 'Test File',
            'custom_properties' => [
                'user_id' => $this->testUser->id,
            ],
        ]);

        $this->assertNotNull(Media::find($media->id));

        $user2 = TestUserModel::create([
            'email' => 'user2@example.com',
        ]);

        $this->actingAs($user2);
        $this->assertNull(Media::find($media->id));
    }
}
