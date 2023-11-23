<?php

namespace LivewireFilemanager\Filemanager\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Auth;
use LivewireFilemanager\Filemanager\Models\Folder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    public function mount()
    {
        if(!session('currentFolderId')) {
            session(['currentFolderId' => 1]);
        }

        $currentFolderId = session('currentFolderId');

        $this->currentFolder = Folder::with(['children', 'parent'])->where('id', $currentFolderId)->first();
        $this->breadcrumb = $this->generateBreadcrumb($this->currentFolder);

        if($this->currentFolder) {
            $this->loadFolders();
        }
    }

    public function toggleFolderSelection($folderId)
    {
        if (in_array($folderId, $this->selectedFolders)) {
            $this->selectedFolders = array_diff($this->selectedFolders, [$folderId]);
        } else {
            $this->selectedFolders[] = $folderId;
        }
    }

    public function toggleFileSelection($fileId)
    {
        if (in_array($fileId, $this->selectedFiles)) {
            $this->selectedFiles = array_diff($this->selectedFiles, [$fileId]);
        } else {
            $this->selectedFiles[] = $fileId;
        }
    }

    public function loadFolders()
    {
        if ($this->search != '') {
            $this->folders = Folder::where('id', '!=', 1)->where('name', 'like', '%' . $this->search . '%')->get();
            $this->searchedFiles = Media::where('collection_name', 'medialibrary')->where('name', 'like', '%' . $this->search . '%')->get();
        } else {
            $this->folders = $this->currentFolder->fresh()->children;
            $this->searchedFiles = null;
        }

        $this->selectedFolders = [];
        $this->selectedFiles = [];
    }

    public function deleteItems()
    {
        $this->dispatch('openModal', 'lochness::livewire.delete-items', ['folders' => $this->selectedFolders, 'files' => $this->selectedFiles]);
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

        $this->newFolderName = 'dossier sans titre';

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
                                            ->where('parent_id', $this->currentFolder->id)
                                            ->first();
                    if ($existingFolder) {
                        $fail('A folder with this name already exists in the current directory.');

                        Toaster::error(__('lochness::client.filemanager.folder_already_exists'));
                    }
                },
            ],
        ]);

        $newFolder = new Folder();

        $newFolder->name = trim($this->newFolderName) ?: 'dossier sans titre';
        $newFolder->slug = Str::slug(trim($this->newFolderName) ?: 'dossier sans titre');
        $newFolder->parent_id = $this->currentFolder->id;
        $newFolder->save();

        $this->newFolderName = '';

        $this->isCreatingNewFolder = false;

        Toaster::success(__('lochness::client.resources.status.informations_updated_success'));

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
                ->usingName(Str::slug($file->getClientOriginalName()))
                ->usingFileName($file->getClientOriginalName())
                ->withCustomProperties([
                    'user_id' => Auth::user()->id,
                ])
                ->toMediaCollection('medialibrary');
        }

        Toaster::success(trans_choice('lochness::client.filemanager.files_uploaded', count($this->files)));

        $this->files = [];
    }

    public function render()
    {
        return view('livewire-filemanager::livewire.livewire-filemanager');
    }
}
