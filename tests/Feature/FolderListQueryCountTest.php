<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class FolderListQueryCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_loading_the_folder_list_does_not_scale_with_the_number_of_folders()
    {
        $root = Folder::create(['name' => 'Root', 'slug' => 'root', 'parent_id' => null]);

        foreach (range(1, 5) as $i) {
            Folder::create(['name' => "Child {$i}", 'slug' => "child-{$i}", 'parent_id' => $root->id]);
        }

        $component = new LivewireFilemanagerComponent;
        $component->currentFolder = $root;

        DB::enableQueryLog();
        $component->loadFolders();
        $queryCountWithFiveFolders = count(DB::getQueryLog());
        DB::flushQueryLog();

        foreach (range(6, 20) as $i) {
            Folder::create(['name' => "Child {$i}", 'slug' => "child-{$i}", 'parent_id' => $root->id]);
        }

        $component = new LivewireFilemanagerComponent;
        $component->currentFolder = $root;

        DB::flushQueryLog();
        $component->loadFolders();
        $queryCountWithTwentyFolders = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertEquals(
            $queryCountWithFiveFolders,
            $queryCountWithTwentyFolders,
            'Loading the folder list should issue the same number of queries regardless of how many folders it contains.'
        );
    }
}
