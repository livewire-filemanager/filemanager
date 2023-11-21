<?php

namespace LivewireFilemanager\Filemanager\Http\Components;

use Illuminate\View\Component;

class BladeFilemanagerComponent extends Component
{
    public function render()
    {
        return view('livewire-filemanager::components.livewire-filemanager');
    }
}
