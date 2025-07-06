<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Models\Media;
use LivewireFilemanager\Filemanager\Tests\Models\TestFolderModel;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\TestCase;

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
            'model_type' => 'LivewireFilemanager\\Filemanager\\Models\\Folder',
            'model_id' => 1,
            'uuid' => \Illuminate\Support\Str::uuid(),
            'collection_name' => 'medialibrary',
            'name' => 'Test File',
            'file_name' => 'test-file.txt',
            'mime_type' => 'text/plain',
            'disk' => 'local',
            'conversions_disk' => 'local',
            'size' => 1024,
            'manipulations' => [],
            'custom_properties' => [
                'user_id' => $this->testUser->id,
            ],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => 1,
        ]);

        $this->assertNotNull(Media::find($media->id));

        $user2 = TestUserModel::create([
            'email' => 'user2@example.com',
        ]);

        $this->actingAs($user2);
        $this->assertNull(Media::find($media->id));
    }
}
