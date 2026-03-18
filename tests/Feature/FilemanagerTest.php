<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class FilemanagerTest extends TestCase
{
    /**
     * @group filemanager
     */
    public function test_the_livewire_filemanager_component_can_be_rendered()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->assertStatus(200);
    }

    /**
     * @group filemanager
     */
    public function test_no_folder_for_starting_point()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->assertSee(__('livewire-filemanager::filemanager.root_folder_not_configurated'))
            ->assertSee(__('livewire-filemanager::filemanager.root_folder_not_configurated_help'))
            ->assertSee(__('livewire-filemanager::filemanager.add_your_first_folder'));
    }
}
