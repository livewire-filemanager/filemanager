<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Folder;

class LivewireFilemanagerFolderPanelComponent extends Component
{
    public $folder;

    #[On('load-folder')]
    public function loadFolder(int $folder_id)
    {
        $this->folder = Folder::find($folder_id);
    }

    #[On('reset-folder')]
    public function resetFolder()
    {
        $this->folder = null;
    }

    public function renameFolder()
    {
        $this->dispatch('rename-folder', folder: $this->folder);
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.folder-panel');
    }
}
