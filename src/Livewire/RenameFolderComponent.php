<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Folder;

class RenameFolderComponent extends Component
{
    public $folder;

    public $name;

    #[On('rename-folder')]
    public function renameFolder($folder)
    {
        $this->folder = Folder::find($folder['id']);

        $this->name = $this->folder->name;
    }

    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                function ($attribute, $value, $fail) {
                    $slug = Str::slug(trim($value));
                    $existingFolder = Folder::where('slug', $slug)
                        ->first();
                    if ($existingFolder) {
                        $fail(__('livewire-filemanager::filemanager.folder_already_exists'));
                    }
                },
            ],
        ], [
            'name.required' => __('livewire-filemanager::filemanager.validation.folder_name_required'),
        ]);

        $this->folder->name = $this->name;
        $this->folder->slug = Str::slug(trim($this->name) ?: __('livewire-filemanager::filemanager.folder_without_title'));
        $this->folder->save();

        $this->dispatch('reset-folder');
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.rename-folder');
    }
}
