<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use LivewireFilemanager\Filemanager\Models\Media;

class RenameFileComponent extends Component
{
    public $file;

    public $name;

    #[On('rename-file')]
    public function renameFile($file)
    {
        $this->file = Media::find($file['id']);

        $this->name = pathinfo($this->file->name, PATHINFO_FILENAME);
    }

    public function save()
    {
        $this->validate([
            'name' => ['required'],
        ], [
            'name.required' => __('livewire-filemanager::filemanager.validation.file_name_required'),
        ]);

        $baseSlug = Str::slug(trim($this->name));
        $extension = $this->file->extension;
        $name = $this->name.'.'.$extension;
        $newFileName = $baseSlug.'.'.$extension;

        $counter = 1;
        while (Media::where('file_name', $newFileName)->where('id', '!=', $this->file->id)->exists()) {
            $newFileName = $baseSlug.'-'.$counter.'.'.$extension;
            $name = $this->name.'-'.$counter.'.'.$extension;
            $counter++;
        }

        $oldPath = $this->file->getPathRelativeToRoot();
        $newPath = str_replace($this->file->file_name, $newFileName, $oldPath);
        Storage::disk($this->file->disk)->move($oldPath, $newPath);

        $this->file->name = $name;
        $this->file->file_name = $newFileName;
        $this->file->save();

        $this->dispatch('reset-media');
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.rename-file');
    }
}
