<?php

namespace LivewireFilemanager\Filemanager\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use LivewireFilemanager\Filemanager\Models\Folder;

class UpdateFolderRequest extends FormRequest
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
                    $existingFolder = Folder::where('slug', $slug)
                        ->where('parent_id', $this->route('folder')->parent_id)
                        ->where('id', '!=', $this->route('folder')->id)
                        ->first();
                    if ($existingFolder) {
                        $fail('A folder with this name already exists.');
                    }
                },
            ],
        ];
    }
}
