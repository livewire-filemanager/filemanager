<?php

namespace LivewireFilemanager\Filemanager\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadFilesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $maxSize = config('livewire-filemanager.api.max_file_size', 10240);
        $allowedExtensions = config('livewire-filemanager.api.allowed_extensions', ['jpg', 'jpeg', 'png', 'pdf', 'txt']);

        return [
            'files' => 'required|array|min:1',
            'files.*' => [
                'required',
                'file',
                'max:'.$maxSize,
                'mimes:'.implode(',', $allowedExtensions),
            ],
        ];
    }
}
