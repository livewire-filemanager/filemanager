<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Media;

class LivewireFilemanagerPanelComponent extends Component
{
    public ?int $mediaId = null;

    #[On('load-media')]
    public function loadMedia(int $media_id): void
    {
        $this->mediaId = $media_id;
    }

    #[On('reset-media')]
    public function resetMedia(): void
    {
        $this->mediaId = null;
    }

    public function renameFile(): void
    {
        $media = Media::find($this->mediaId);

        if ($media) {
            $this->dispatch('rename-file', file: $media);
        }
    }

    public function render()
    {
        $media = $this->mediaId ? Media::find($this->mediaId) : null;

        return view('livewire-filemanager::livewire.media-panel', compact('media'));
    }
}
