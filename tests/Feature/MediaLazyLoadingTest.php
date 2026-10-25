<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\TestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class MediaLazyLoadingTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [...parent::getPackageProviders($app), MediaLibraryServiceProvider::class];
    }

    protected function tearDown(): void
    {
        Model::preventLazyLoading(false);

        parent::tearDown();
    }

    public function test_conversion_urls_can_be_resolved_when_lazy_loading_is_prevented()
    {
        $folder = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);

        foreach (range(1, 2) as $i) {
            $folder->addMedia($this->getTestJpg())->preservingOriginal()->toMediaCollection('medialibrary');
        }

        Model::preventLazyLoading();

        $urls = Folder::find($folder->id)->getMedia('medialibrary')->map->getUrl('thumbnail');

        $this->assertCount(2, $urls);
    }
}
