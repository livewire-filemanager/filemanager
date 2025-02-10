<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use LivewireFilemanager\Filemanager\Models\Folder;
use LivewireFilemanager\Filemanager\Models\Media;

class LivewireFilemanagerComponent extends Component
{
    use WithFileUploads;

    public $currentFolder = null;

    public $search = '';

    public $searchedFiles = null;

    public $folders = [];

    public $selectedFolders = [];

    public $files = [];

    public $selectedFiles = [];

    public $isCreatingNewFolder = false;

    public $newFolderName = '';

    public $savedByEnter = false;

    public $breadcrumb = [];
    public $tenant_id = null;
    public $user_id = null;

    protected $listeners = ['fileAdded'];


    public function mount($user_id = null, $tenant_id = null)
    {
        $this->tenant_id = $tenant_id;
        $this->user_id = $user_id;

        $currentFolderId = session('currentFolderId');

        if ($currentFolderId) {
            $this->currentFolder = Folder::with(['children', 'parent'])
                ->where('user_id', $user_id)
                ->when(config('livewire-fileuploader.tenant_enabled'), function ($query) use ($tenant_id) {
                    return $query->where('tenant_id', $tenant_id);
                })
                ->where('id', $currentFolderId)
                ->first();
        }

        if (!$this->currentFolder) {
            // take the root
            $this->currentFolder = Folder::with(['children', 'parent'])
                ->where('user_id', $user_id)
                ->when(config('livewire-fileuploader.tenant_enabled'), function ($query) use ($tenant_id) {
                    return $query->where('tenant_id', intval($tenant_id));
                })
                ->whereNull('parent_id')
                //->toSql();
                ->first();
        }

        if (!$this->currentFolder) {
            $initialRootFolder = [
                'name' => 'root',
                'slug' => 'root',
                'parent_id' => null,
                'user_id' => $user_id,
            ];
            if (config('livewire-fileuploader.tenant_enabled')) {
                $initialRootFolder['tenant_id'] = $tenant_id;
            }
            $this->currentFolder = Folder::create($initialRootFolder);
        }

        $this->breadcrumb = $this->generateBreadcrumb($this->currentFolder);

        if ($this->currentFolder) {
            session(['currentFolderId' => $this->currentFolder->id]);
            $this->loadFolders();
        }
    }

    public function createRootFolder()
    {
        $this->validate([
            'newFolderName' => 'required|max:255',
        ], [
            'newFolderName.required' => __('livewire-filemanager::filemanager.validation.folder_name_required'),
        ]);
    }

    public function toggleFolderSelection(int $folderId)
    {
        if (! in_array($folderId, $this->selectedFolders)) {
            $this->selectedFolders[] = $folderId;
        } else {
            $this->selectedFolders = array_diff($this->selectedFolders, [$folderId]);
        }
    }

    public function toggleFileSelection(int $fileId)
    {
        if (! in_array($fileId, $this->selectedFiles)) {
            $this->selectedFiles[] = $fileId;
        } else {
            $this->selectedFiles = array_diff($this->selectedFiles, [$fileId]);
        }
    }

    public function loadFolders()
    {
        if ($this->search != '') {
            $this->folders = Folder::whereNotNull('parent_id')
                ->where('name', 'like', '%'.$this->search.'%')
                ->where('user_id', $this->user_id)
                ->when(config('livewire-fileuploader.tenant_enabled'), function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })
                ->get();

            $folderIds = Folder::where('name', 'like', '%'.$this->search.'%')
                ->where('user_id', $this->user_id)
                ->when(config('livewire-fileuploader.tenant_enabled'), function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })
                ->select('id')
                ->get()
                ->pluck('id');

            $this->searchedFiles = Media::where('collection_name', 'medialibrary')
                ->where('name', 'like', '%'.$this->search.'%')
                ->whereIn('folder_id', $folderIds)
                ->get();
        } else {
            $this->folders = $this->currentFolder->fresh()->children;
            $this->searchedFiles = null;
        }

        $this->selectedFolders = [];
        $this->selectedFiles = [];
    }

    public function deleteItems()
    {
        $this->dispatch('delete-items', folders: $this->selectedFolders, files: $this->selectedFiles);
    }

    #[On('folder-deleted')]
    public function folderDeleted()
    {
        $this->loadFolders();
    }

    #[On('reset-media')]
    public function resetMedias()
    {
        $this->selectedFolders = [];
        $this->selectedFiles = [];
    }

    public function updatedSearch()
    {
        $this->currentFolder = Folder::find(1);

        session(['currentFolderId' => $this->currentFolder->id]);

        $this->breadcrumb = $this->generateBreadcrumb($this->currentFolder);

        $this->loadFolders();
    }

    private function generateBreadcrumb($folder)
    {
        $breadcrumb = [];

        while ($folder) {
            array_unshift($breadcrumb, $folder);

            $folder = $folder->parent;
        }

        return $breadcrumb;
    }

    public function createNewFolder()
    {
        $this->isCreatingNewFolder = true;

        $this->newFolderName = __('livewire-filemanager::filemanager.folder_without_title');

        $this->dispatch('new-folder-created');
    }

    public function saveNewFolder()
    {
        $this->validate([
            'newFolderName' => [
                'required',
                function ($attribute, $value, $fail) {
                    $slug = Str::slug(trim($value));
                    $existingFolder = Folder::where('slug', $slug)
                        ->where('parent_id', ($this->currentFolder ? $this->currentFolder->id : null))
                        ->where('user_id', $this->user_id)
                        ->when(config('livewire-fileuploader.tenant_enabled'), function ($query) {
                            return $query->where('tenant_id', $this->tenant_id);
                        })
                        ->first();
                    if ($existingFolder) {
                        $fail(__('livewire-filemanager::filemanager.folder_already_exists'));
                    }
                },
            ],
        ]);

        $newFolder = new Folder;
        $newFolder->name = trim($this->newFolderName) ?: __('livewire-filemanager::filemanager.folder_without_title');
        $newFolder->slug = Str::slug(trim($this->newFolderName) ?: __('livewire-filemanager::filemanager.folder_without_title'));
        $newFolder->parent_id = ($this->currentFolder ? $this->currentFolder->id : null);
        if ($this->user_id) {
            $newFolder->user_id = $this->user_id;
        }
        if ($this->tenant_id) {
            $newFolder->tenant_id = $this->tenant_id;
        }
        $newFolder->save();

        $this->currentFolder = $newFolder;

        $this->newFolderName = '';

        $this->breadcrumb = $this->generateBreadcrumb($this->currentFolder);
        $this->isCreatingNewFolder = false;

        session(['currentFolderId' => $newFolder->id]);

        $this->loadFolders();
    }

    public function navigateToParent()
    {
        $this->search = '';

        if ($this->currentFolder->parent_id !== null) {
            $parentFolder = Folder::find($this->currentFolder->parent_id);

            $this->currentFolder = $parentFolder;

            session(['currentFolderId' => $parentFolder->id]);

            array_pop($this->breadcrumb);

            $this->loadFolders();
        }
    }

    public function navigateToFolder($folderId)
    {
        $this->search = '';

        $folder = Folder::find($folderId);

        $this->currentFolder = $folder;

        $this->breadcrumb = $this->generateBreadcrumb($this->currentFolder);

        $this->loadFolders();

        session(['currentFolderId' => $folder->id]);
    }

    public function navigateToBreadcrumb($breadcrumbIndex)
    {
        $this->search = '';

        $this->breadcrumb = array_slice($this->breadcrumb, 0, $breadcrumbIndex + 1);
        $this->currentFolder = end($this->breadcrumb);
        session(['currentFolderId' => $this->currentFolder->id]);

        $this->loadFolders();
    }

    public function updatedFiles()
    {
        foreach ($this->files as $file) {
            $this->currentFolder
                ->addMedia($file->getRealPath())
                ->usingName($file->getClientOriginalName())
                ->sanitizingFileName(function ($fileName) use ($file) {
                    $extension = pathinfo($file->getRealPath(), PATHINFO_EXTENSION);
                    $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                    $slugified_name = Str::slug($name);

                    return strtolower($slugified_name.'.'.$extension);
                })
                ->withCustomProperties([
                    'user_id' => optional(Auth::user())->id,
                ])
                ->toMediaCollection('medialibrary');
        }

        $this->files = [];
    }

    public function handleMediaClick($fileId)
    {
        if (count($this->selectedFiles) > 1) {
            $this->dispatch('reset-media');
        } else {
            if (in_array($fileId, $this->selectedFiles)) {
                $this->dispatch('load-media', $fileId);
            } else {
                $this->dispatch('reset-media');
            }
        }
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.livewire-filemanager');
    }
}
