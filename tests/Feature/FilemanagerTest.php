<?php

namespace LivewireFilemanager\Filemanager\Tests\Feature;

use Livewire\Livewire;
use LivewireFilemanager\Filemanager\Livewire\LivewireFilemanagerComponent;
use LivewireFilemanager\Filemanager\Tests\TestCase;

class FilemanagerTest extends TestCase
{
    /**
     * The component can be rendered.
     *
     * @group filemanager
     *
     * @test
     */
    public function the_livewire_filemanager_component_can_be_rendered()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->assertStatus(200);
    }

    /**
     * If the database doesn't have a root folder inside the database, the component will show a message.
     *
     * @group filemanager
     *
     * @test
     */
    public function no_folder_for_starting_point()
    {
        Livewire::test(LivewireFilemanagerComponent::class)
            ->assertSee(__('livewire-filemanager::filemanager.root_folder_not_configurated'))
            ->assertSee(__('livewire-filemanager::filemanager.root_folder_not_configurated_help'))
            ->assertSee(__('livewire-filemanager::filemanager.add_your_first_folder'));
    }
}
