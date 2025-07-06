<?php

namespace LivewireFilemanager\Filemanager\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use LivewireFilemanager\Filemanager\Http\Requests\Api\UpdateFileRequest;
use LivewireFilemanager\Filemanager\Http\Resources\MediaResource;
use LivewireFilemanager\Filemanager\Models\Media;

class FileController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $files = Media::query()
            ->when($request->folder_id, function ($query, $folderId) {
                return $query->where('model_id', $folderId);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->get();

        return MediaResource::collection($files);
    }

    public function show(Media $file)
    {
        $this->authorize('view', $file);

        $filePath = $file->getPath();

        if (! file_exists($filePath)) {
            return response()->json(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        $fileMimeType = mime_content_type($filePath);

        if (in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'])) {
            return response()->file($filePath);
        }

        return response()->download($filePath, $file->file_name);
    }

    public function update(UpdateFileRequest $request, Media $file)
    {
        $this->authorize('update', $file);

        $file->update([
            'name' => $request->name,
        ]);

        return new MediaResource($file);
    }

    public function destroy(Media $file)
    {
        $this->authorize('delete', $file);

        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
}
