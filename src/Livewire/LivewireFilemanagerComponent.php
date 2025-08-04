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

    protected $listeners = ['fileAdded'];

    public function mount()
    {
        if (! session('currentFolderId')) {
            session(['currentFolderId' => Folder::whereNotNull('parent_id')->first() ? Folder::whereNotNull('parent_id')->first()->id : null]);
        }

        $currentFolderId = session('currentFolderId');

        $this->currentFolder = Folder::with(['children', 'parent'])->where('id', $currentFolderId)->first();
        $this->breadcrumb = $this->generateBreadcrumb($this->currentFolder);

        if ($this->currentFolder) {
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
            $this->folders = Folder::whereNotNull('parent_id')->where('name', 'like', '%'.$this->search.'%')->get();
            $this->searchedFiles = Media::where('collection_name', 'medialibrary')->where('name', 'like', '%'.$this->search.'%')->get();
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

    #[On('reset-folder')]
    public function resetFolders()
    {
        $this->selectedFolders = [];
        $this->selectedFiles = [];
    }

    public function updatedSearch()
    {
        $this->currentFolder = Folder::whereNull('parent_id')->first();

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
                        ->first();
                    if ($existingFolder) {
                        $fail(__('livewire-filemanager::filemanager.folder_already_exists'));
                    }

                    $maxDepth = config('livewire-fileuploader.folders.max_depth');
                    if ($maxDepth !== null && $this->currentFolder) {
                        if ($this->currentFolder->getDepth() >= $maxDepth - 1) {
                            $fail(__('livewire-filemanager::filemanager.validation.max_folder_depth_exceeded', ['max' => $maxDepth]));
                        }
                    }
                },
            ],
        ]);

        $newFolder = new Folder;

        $newFolder->name = trim($this->newFolderName) ?: __('livewire-filemanager::filemanager.folder_without_title');
        $newFolder->slug = Str::slug(trim($this->newFolderName) ?: __('livewire-filemanager::filemanager.folder_without_title'));
        $newFolder->parent_id = ($this->currentFolder ? $this->currentFolder->id : null);
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
        $this->dispatch('reset-folder');

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

        if (count($this->selectedFolders) > 0) {
            $this->dispatch('reset-folder');
        }
    }

    public function handleFolderClick($folderId)
    {
        if (count($this->selectedFolders) > 1) {
            $this->dispatch('reset-folder');
        } else {
            if (in_array($folderId, $this->selectedFolders)) {
                $this->dispatch('load-folder', $folderId);
            } else {
                $this->dispatch('reset-folder');
            }
        }

        if (count($this->selectedFiles) > 0) {
            $this->dispatch('reset-media');
        }
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.livewire-filemanager');
    }
}
