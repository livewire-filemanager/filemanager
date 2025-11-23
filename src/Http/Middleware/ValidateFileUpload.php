<?php

namespace LivewireFilemanager\Filemanager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ValidateFileUpload
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasFile('files')) {
            $this->validateFiles($request);
        }

        return $next($request);
    }

    private function validateFiles(Request $request)
    {
        $maxSize = config('livewire-filemanager.api.max_file_size', 10240);
        $allowedExtensions = config('livewire-filemanager.api.allowed_extensions', ['jpg', 'jpeg', 'png', 'pdf', 'txt']);

        $files = $request->file('files');
        if (! is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $validator = Validator::make([
                'file' => $file,
            ], [
                'file' => [
                    'required',
                    'file',
                    'max:'.$maxSize,
                    'mimes:'.implode(',', $allowedExtensions),
                ],
            ]);

            if ($validator->fails()) {
                abort(Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->first());
            }

            $this->validateFileName($file->getClientOriginalName());
            $this->validateFilePath($file->getRealPath());
        }
    }

    private function validateFileName(string $fileName)
    {
        if (preg_match('/[\\\\\/\:\*\?\"\<\>\|]/', $fileName)) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid file name characters');
        }

        if (strpos($fileName, '..') !== false) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Path traversal detected in file name');
        }
    }

    private function validateFilePath(string $filePath)
    {
        $realPath = realpath($filePath);
        $allowedPath = realpath(sys_get_temp_dir());

        if ($realPath === false || strpos($realPath, $allowedPath) !== 0) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid file path');
        }
    }
}
