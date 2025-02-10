<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Models\Media;

class DeleteItemsComponent extends Component
{
    public $files;

    public $folders;

    #[On('delete-items')]
    public function deleteItems(array $folders, array $files)
    {
        $this->folders = $folders;

        $this->files = $files;
    }

    public function delete()
    {
        foreach ($this->files as $file) {
            Media::find($file)->delete();
        }

        foreach ($this->folders as $folder) {
            Folder::find($folder)->delete();
        }

        $this->dispatch('reset-media');
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.delete-items');
    }
}
