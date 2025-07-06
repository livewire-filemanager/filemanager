<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $user1;

    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->user1 = TestUserModel::create([
            'email' => 'user1@example.com',
        ]);

        $this->user2 = TestUserModel::create([
            'email' => 'user2@example.com',
        ]);

        Storage::fake('local');
        Config::set('livewire-fileuploader.acl_enabled', true);
    }

    public function test_user_cannot_access_other_users_folders_when_acl_enabled()
    {
        $folder = Folder::create([
            'name' => 'User 1 Folder',
            'slug' => 'user-1-folder',
            'parent_id' => null,
            'user_id' => $this->user1->id,
        ]);

        $response = $this->actingAs($this->user2)
            ->getJson("/api/filemanager/v1/folders/{$folder->id}");

        $response->assertStatus(404);
    }

    public function test_user_cannot_update_other_users_folders_when_acl_enabled()
    {
        $folder = Folder::create([
            'name' => 'User 1 Folder',
            'slug' => 'user-1-folder',
            'parent_id' => null,
            'user_id' => $this->user1->id,
        ]);

        $response = $this->actingAs($this->user2)
            ->putJson("/api/filemanager/v1/folders/{$folder->id}", [
                'name' => 'Hacked Folder',
            ]);

        $response->assertStatus(404);
    }

    public function test_user_cannot_delete_other_users_folders_when_acl_enabled()
    {
        $folder = Folder::create([
            'name' => 'User 1 Folder',
            'slug' => 'user-1-folder',
            'parent_id' => 1,
            'user_id' => $this->user1->id,
        ]);

        $response = $this->actingAs($this->user2)
            ->deleteJson("/api/filemanager/v1/folders/{$folder->id}");

        $response->assertStatus(404);
    }

    public function test_can_access_own_resources_when_acl_enabled()
    {
        $this->actingAs($this->user1);

        $folder = Folder::create([
            'name' => 'User 1 Folder',
            'slug' => 'user-1-folder',
            'parent_id' => null,
        ]);

        $response = $this->actingAs($this->user1)
            ->getJson("/api/filemanager/v1/folders/{$folder->id}");

        $response->assertStatus(200);
    }

    public function test_global_access_when_acl_disabled()
    {
        Config::set('livewire-fileuploader.acl_enabled', false);

        $folder = Folder::create([
            'name' => 'Any User Folder',
            'slug' => 'any-user-folder',
            'parent_id' => null,
            'user_id' => $this->user1->id,
        ]);

        $response = $this->actingAs($this->user2)
            ->getJson("/api/filemanager/v1/folders/{$folder->id}");

        $response->assertStatus(200);
    }
}
