<?php

namespace LivewireFilemanager\Filemanager\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LivewireFilemanager\Filemanager\Http\Requests\Api\StoreFolderRequest;
use LivewireFilemanager\Filemanager\Http\Requests\Api\UpdateFolderRequest;
use LivewireFilemanager\Filemanager\Http\Requests\Api\UploadFilesRequest;
use LivewireFilemanager\Filemanager\Http\Resources\FolderResource;
use LivewireFilemanager\Filemanager\Http\Resources\MediaResource;
use LivewireFilemanager\Filemanager\Models\Folder;

class FolderController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $folders = Folder::with(['children', 'media'])
            ->when($request->parent_id, function ($query, $parentId) {
                return $query->where('parent_id', $parentId);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->get();

        return FolderResource::collection($folders);
    }

    public function store(StoreFolderRequest $request)
    {
        $this->executeCallback('before_upload', $request);

        $folder = new Folder();
        $folder->name = $request->name;
        $folder->slug = Str::slug($request->name);
        $folder->parent_id = $request->parent_id;
        $folder->save();

        $this->executeCallback('after_upload', $folder);

        return new FolderResource($folder);
    }

    public function show(Folder $folder)
    {
        $this->authorize('view', $folder);

        return new FolderResource($folder->load(['children', 'media']));
    }

    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        $this->authorize('update', $folder);

        $folder->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return new FolderResource($folder);
    }

    public function destroy(Folder $folder)
    {
        $this->authorize('delete', $folder);

        if ($folder->isHomeFolder()) {
            return response()->json(['message' => 'Cannot delete home folder'], Response::HTTP_FORBIDDEN);
        }

        $folder->delete();

        return response()->json(['message' => 'Folder deleted successfully']);
    }

    public function upload(UploadFilesRequest $request, Folder $folder)
    {
        $this->authorize('update', $folder);

        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $this->executeCallback('before_upload', $file);

            $media = $folder
                ->addMedia($file)
                ->withCustomProperties([
                    'user_id' => optional(Auth::user())->id,
                ])
                ->toMediaCollection('medialibrary');

            $this->executeCallback('after_upload', $media);

            $uploadedFiles[] = new MediaResource($media);
        }

        return response()->json([
            'message' => 'Files uploaded successfully',
            'files' => $uploadedFiles,
        ]);
    }

    private function executeCallback(string $callbackName, $data = null)
    {
        $callback = config("livewire-fileuploader.callbacks.{$callbackName}");

        if ($callback && is_callable($callback)) {
            call_user_func($callback, $data);
        }
    }
}
