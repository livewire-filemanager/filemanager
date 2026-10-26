<?php

namespace LivewireFilemanager\Filemanager\Http\Controllers\Files;

use Illuminate\Support\Facades\Gate;
use LivewireFilemanager\Filemanager\Models\Folder;

class FileController
{
    public function show($path)
    {
        $segments = explode('/', trim($path, '/'));
        $fileName = array_pop($segments);

        $folder = Folder::findByPath(implode('/', $segments));

        abort_if(! $folder, 404);

        $file = $folder->media()
            ->where(function ($query) use ($fileName) {
                $query->where('file_name', $fileName)
                    ->orWhere('name', $fileName);
            })
            ->firstOrFail();

        if (config('livewire-filemanager.acl_enabled')) {
            abort_unless(Gate::allows('view', $file), 404);
        }

        $filePath = $file->getPath();

        if (! file_exists($filePath)) {
            abort(404, 'File not found on server');
        }

        $fileMimeType = mime_content_type($filePath);

        if (in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'])) {
            return response()->file($filePath);
        }

        return response()->download($filePath, $fileName);
    }
}
