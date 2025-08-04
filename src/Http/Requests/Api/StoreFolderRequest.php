<?php

namespace LivewireFilemanager\Filemanager\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreFolderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $slug = Str::slug(trim($value));
                    $existingFolder = \LivewireFilemanager\Filemanager\Models\Folder::where('slug', $slug)
                        ->where('parent_id', $this->parent_id)
                        ->first();
                    if ($existingFolder) {
                        $fail('A folder with this name already exists.');
                    }
                },
            ],
            'parent_id' => [
                'nullable',
                'exists:folders,id',
                function ($attribute, $value, $fail) {
                    $maxDepth = config('livewire-fileuploader.folders.max_depth');
                    if ($maxDepth === null || !$value) {
                        return;
                    }
                    
                    $parentFolder = \LivewireFilemanager\Filemanager\Models\Folder::find($value);
                    if ($parentFolder && $parentFolder->getDepth() >= $maxDepth - 1) {
                        $fail(__('livewire-filemanager::filemanager.validation.max_folder_depth_exceeded', ['max' => $maxDepth]));
                    }
                },
            ],
        ];
    }
}
