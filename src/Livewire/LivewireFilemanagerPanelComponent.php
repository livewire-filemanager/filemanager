<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Media;

class LivewireFilemanagerPanelComponent extends Component
{
    public $media;

    #[On('load-media')]
    public function loadMedia(int $media_id)
    {
        $this->media = Media::find($media_id);
    }

    #[On('reset-media')]
    public function resetMedia()
    {
        $this->media = null;
    }

    public function renameFile()
    {
        $this->dispatch('rename-file', file: $this->media);
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.media-panel');
    }
}
