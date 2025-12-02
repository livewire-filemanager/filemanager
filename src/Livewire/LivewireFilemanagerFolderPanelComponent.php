<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Folder;

class LivewireFilemanagerFolderPanelComponent extends Component
{
    public ?int $folderId = null;

    #[On('load-folder')]
    public function loadFolder(int $folder_id): void
    {
        $this->folderId = $folder_id;
    }

    #[On('reset-folder')]
    public function resetFolder(): void
    {
        $this->folderId = null;
    }

    public function renameFolder(): void
    {
        $folder = Folder::find($this->folderId);

        if ($folder) {
            $this->dispatch('rename-folder', folder: $folder);
        }
    }

    public function render()
    {
        $folder = $this->folderId ? Folder::find($this->folderId) : null;

        return view('livewire-filemanager::livewire.folder-panel', compact('folder'));
    }
}
