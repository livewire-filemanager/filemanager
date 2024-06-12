<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    #[On('copy-link')]
    public function copyLink()
    {
        // Send notification
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.media-panel');
    }
}
