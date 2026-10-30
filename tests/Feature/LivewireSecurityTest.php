<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerPanelComponent;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\Models\TestUserModel;
use LivewireFilemanager\Filemanager\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

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

    public function test_livewire_component_shows_the_configured_max_file_size()
    {
        Config::set('livewire-filemanager.api.max_file_size', 102400);

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->assertSee('Max. file size: 100 MB');
    }

    public function test_livewire_component_shows_oversized_file_error_and_clears_files()
    {
        Config::set('livewire-filemanager.api.max_file_size', 1024);

        $largeFile = UploadedFile::fake()->create('large.txt', 2000, 'text/plain');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$largeFile])
            ->assertHasErrors(['files.0'])
            ->assertSet('files', [])
            ->assertSee('The file is too large. The maximum size is 1 MB.');
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

    public static function dangerousFileNames(): array
    {
        return [
            'pht' => ['shell.pht'],
            'phtm' => ['shell.phtm'],
            'phps' => ['shell.phps'],
            'htaccess' => ['.htaccess'],
        ];
    }

    #[DataProvider('dangerousFileNames')]
    public function test_livewire_component_rejects_dangerous_names_with_allowed_content(string $fileName)
    {
        $file = UploadedFile::fake()->createWithContent(
            $fileName,
            file_get_contents(__DIR__.'/../TestSupport/testfiles/test.jpg')
        )->mimeType('image/jpeg');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$file])
            ->assertHasErrors(['files.0']);

        $this->assertSame(0, $this->testFolder->getMedia('medialibrary')->count());
    }

    public function test_livewire_component_rejects_allowed_content_with_disallowed_extension()
    {
        $file = UploadedFile::fake()->createWithContent(
            'photo.svg',
            file_get_contents(__DIR__.'/../TestSupport/testfiles/test.jpg')
        )->mimeType('image/jpeg');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$file])
            ->assertHasErrors(['files.0']);
    }

    public function test_livewire_component_stores_allowed_file_under_slugified_name()
    {
        $file = UploadedFile::fake()->image('Mon Image.JPG', 600, 400);

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$file])
            ->assertHasNoErrors();

        $this->assertSame('mon-image.jpg', $this->testFolder->getFirstMedia('medialibrary')->file_name);
    }

    public function test_livewire_component_rejects_double_extension_php_files()
    {
        $doubleExtFile = UploadedFile::fake()->create('image.jpg.php', 100, 'application/x-php');

        Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerComponent::class)
            ->set('files', [$doubleExtFile])
            ->assertHasErrors(['files.0']);
    }

    public function test_media_panel_copy_link_cannot_break_out_of_javascript_string()
    {
        $media = $this->testFolder
            ->addMedia(UploadedFile::fake()->image('photo.jpg', 10, 10))
            ->usingFileName("x'});alert(1);({a:'.jpg")
            ->toMediaCollection('medialibrary');

        $html = Livewire::actingAs($this->user)
            ->test(LivewireFilemanagerPanelComponent::class)
            ->call('loadMedia', $media->id)
            ->html();

        $this->assertStringNotContainsString("x'});alert(1);({a:'", html_entity_decode($html, ENT_QUOTES));
        $this->assertStringContainsString('x\\u0027});alert(1);({a:\\u0027.jpg', html_entity_decode($html, ENT_QUOTES));
    }
}
