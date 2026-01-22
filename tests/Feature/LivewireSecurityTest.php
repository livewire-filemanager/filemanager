<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class LivewireSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $rootFolder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->user = TestUserModel::create([
            'email' => 'test@example.com',
        ]);

        Storage::fake('local');

        $this->rootFolder = Folder::create([
            'name' => 'Root',
            'slug' => 'root',
            'parent_id' => null,
        ]);

        $this->testFolder = Folder::create([
            'name' => 'Test Folder',
            'slug' => 'test-folder',
            'parent_id' => $this->rootFolder->id,
        ]);

        session(['currentFolderId' => $this->testFolder->id]);
    }

    public function test_livewire_component_rejects_php_files()
    {
        $phpFile = UploadedFile::fake()->create('shell.php', 100, 'application/x-php');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$phpFile])
            ->assertHasErrors(['files.0']);
    }

    public function test_livewire_component_rejects_phtml_files()
    {
        $phtmlFile = UploadedFile::fake()->create('malicious.phtml', 100, 'application/x-php');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$phtmlFile])
            ->assertHasErrors(['files.0']);
    }

    public function test_livewire_component_rejects_executable_files()
    {
        $exeFile = UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$exeFile])
            ->assertHasErrors(['files.0']);
    }

    public function test_livewire_component_accepts_allowed_image_files()
    {
        $jpgFile = UploadedFile::fake()->image('test.jpg', 600, 400);

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$jpgFile])
            ->assertHasNoErrors();
    }

    public function test_livewire_component_accepts_allowed_document_files()
    {
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$pdfFile])
            ->assertHasNoErrors();
    }

    public function test_livewire_component_rejects_oversized_files()
    {
        Config::set('livewire-filemanager.api.max_file_size', 1024);

        $largeFile = UploadedFile::fake()->create('large.txt', 2000, 'text/plain');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$largeFile])
            ->assertHasErrors(['files.0']);
    }

    public function test_livewire_component_respects_custom_allowed_extensions()
    {
        Config::set('livewire-filemanager.api.allowed_extensions', ['jpg', 'png']);

        $txtFile = UploadedFile::fake()->create('test.txt', 100, 'text/plain');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$txtFile])
            ->assertHasErrors(['files.0']);
    }

    public function test_livewire_component_rejects_double_extension_php_files()
    {
        $doubleExtFile = UploadedFile::fake()->create('image.jpg.php', 100, 'application/x-php');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$doubleExtFile])
            ->assertHasErrors(['files.0']);
    }
}
