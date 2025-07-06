<?php

namespace LivewireFilemanager\Filemanager\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use LivewireFilemanager\Filemanager\Http\Requests\Api\BulkUploadRequest;
use LivewireFilemanager\Filemanager\Http\Resources\MediaResource;
use LivewireFilemanager\Filemanager\Models\Folder;

class BulkUploadController extends Controller
{
    use AuthorizesRequests;

    public function store(BulkUploadRequest $request)
    {
        $folder = Folder::findOrFail($request->folder_id);
        $this->authorize('update', $folder);

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $this->executeCallback('before_upload', $file);

                $media = $folder
                    ->addMedia($file)
                    ->withCustomProperties([
                        'user_id' => optional(Auth::user())->id,
                    ])
                    ->toMediaCollection('medialibrary');

                $this->executeCallback('after_upload', $media);

                $uploadedFiles[] = new MediaResource($media);
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        $response = [
            'message' => 'Bulk upload completed',
            'uploaded' => count($uploadedFiles),
            'failed' => count($errors),
            'files' => $uploadedFiles,
        ];

        if (! empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, empty($errors) ? Response::HTTP_OK : Response::HTTP_PARTIAL_CONTENT);
    }

    private function executeCallback(string $callbackName, $data = null)
    {
        $callback = config("livewire-fileuploader.callbacks.{$callbackName}");

        if ($callback && is_callable($callback)) {
            call_user_func($callback, $data);
        }
    }
}
