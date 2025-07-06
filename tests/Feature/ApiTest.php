<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->user = TestUserModel::create([
            'email' => 'test@example.com',
        ]);

        Storage::fake('local');
    }

    public function test_can_list_folders()
    {
        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/filemanager/v1/folders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'parent_id',
                        'is_home_folder',
                        'elements_count',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_can_create_folder()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/filemanager/v1/folders', [
                'name' => 'New Folder',
                'parent_id' => null,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'parent_id',
                ],
            ]);

        $this->assertDatabaseHas('folders', [
            'name' => 'New Folder',
            'slug' => 'new-folder',
        ]);
    }

    public function test_can_show_folder()
    {
        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/filemanager/v1/folders/{$folder->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $folder->id,
                    'name' => 'Test Folder',
                    'slug' => 'test-folder',
                ],
            ]);
    }

    public function test_can_update_folder()
    {
        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/filemanager/v1/folders/{$folder->id}", [
                'name' => 'Updated Folder',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Folder',
                    'slug' => 'updated-folder',
                ],
            ]);

        $this->assertDatabaseHas('folders', [
            'id' => $folder->id,
            'name' => 'Updated Folder',
            'slug' => 'updated-folder',
        ]);
    }

    public function test_can_delete_folder()
    {
        $parentFolder = Folder::create([
            'name' => 'Parent Folder',
            'slug' => 'parent-folder',
            'parent_id' => null,
        ]);

        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => $parentFolder->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/filemanager/v1/folders/{$folder->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Folder deleted successfully',
            ]);

        $this->assertDatabaseMissing('folders', [
            'id' => $folder->id,
        ]);
    }

    public function test_cannot_delete_home_folder()
    {
        $homeFolder = Folder::create([
            'name' => 'Home',
            'slug' => 'home',
            'parent_id' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/filemanager/v1/folders/{$homeFolder->id}");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Cannot delete home folder',
            ]);
    }

    public function test_can_upload_files_to_folder()
    {
        $this->withExceptionHandling();

        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => null,
        ]);

        $txtFile = UploadedFile::fake()->create('test.txt', 100, 'text/plain');
        $jpgFile = UploadedFile::fake()->image('test.jpg', 600, 400);

        $response = $this->actingAs($this->user)
            ->postJson("/api/filemanager/v1/folders/{$folder->id}/upload", [
                'files' => [$txtFile, $jpgFile],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'files' => [
                    '*' => [
                        'id',
                        'name',
                        'file_name',
                        'mime_type',
                        'size',
                        'url',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson([
                'message' => 'Files uploaded successfully',
            ]);

        $this->assertDatabaseHas('media', [
            'name' => 'test',
            'mime_type' => 'application/x-empty',
        ]);

        $this->assertDatabaseHas('media', [
            'name' => 'test',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function test_can_bulk_upload_files()
    {
        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => null,
        ]);

        $txtFile = UploadedFile::fake()->create('test.txt', 100, 'text/plain');
        $jpgFile = UploadedFile::fake()->image('test.jpg', 600, 400);

        $response = $this->actingAs($this->user)
            ->postJson('/api/filemanager/v1/files/bulk', [
                'folder_id' => $folder->id,
                'files' => [$txtFile, $jpgFile],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'uploaded',
                'failed',
                'files' => [
                    '*' => [
                        'id',
                        'name',
                        'file_name',
                        'mime_type',
                        'size',
                        'url',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJson([
                'message' => 'Bulk upload completed',
                'uploaded' => 2,
                'failed' => 0,
            ]);

        $this->assertDatabaseHas('media', [
            'name' => 'test',
            'mime_type' => 'application/x-empty',
        ]);

        $this->assertDatabaseHas('media', [
            'name' => 'test',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function test_file_validation_rejects_invalid_extensions()
    {
        Storage::fake('local');

        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => null,
        ]);

        $file = UploadedFile::fake()->create('test.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($this->user)
            ->postJson("/api/filemanager/v1/folders/{$folder->id}/upload", [
                'files' => [$file],
            ]);

        $response->assertStatus(422);
    }

    public function test_file_validation_rejects_oversized_files()
    {
        Storage::fake('local');

        $folder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => null,
        ]);

        $file = UploadedFile::fake()->create('large.txt', 20000, 'text/plain');

        $response = $this->actingAs($this->user)
            ->postJson("/api/filemanager/v1/folders/{$folder->id}/upload", [
                'files' => [$file],
            ]);

        $response->assertStatus(422);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/filemanager/v1/folders');

        $response->assertStatus(401);
    }
}
