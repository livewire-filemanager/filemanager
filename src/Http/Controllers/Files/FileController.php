<?php

namespace LivewireFilemanager\Filemanager\Http\Controllers\Files;

use LivewireFilemanager\Filemanager\Models\Folder;

class FileController
{
    public function show($path)
    {
        $segments = explode('/', $path);
        $fileName = array_pop($segments);

        $folderPath = implode('/', $segments);

        $folder = Folder::where('slug', '=', end($segments))->firstOrFail();

        $fullFolderPath = buildFolderPath($folder->id);

        if ($folderPath !== $fullFolderPath) {
            abort(404);
        }

        $file = $folder->media()->where('model_id', $folder->id)->where('file_name', $fileName)->orWhere('name', $fileName)->firstOrFail();

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
