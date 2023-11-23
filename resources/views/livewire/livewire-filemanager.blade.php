<div>
    @if(!$currentFolder)
        <div class="w-full">
            {{ __('livewire-filemanager::filemanager.root_folder_not_configurated') }}
        </div>
    @else
        <div class="w-full col-span-4" x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">
            <div class="w-full shadow-sm bg-white dark:bg-gray-700 dark:border-gray-800 pt-4 border border-slate-300 sm:rounded">
                <div class="px-4 pb-4 sm:px-5 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-200">
                        {{ $currentFolder->name }}

                        <span class="px-2 text-gray-600  dark:text-slate-300">|</span>
                        <span class="text-gray-500 text-sm dark:text-slate-300">{{ $currentFolder->elements() }}{!! ((count($selectedFolders) + count($selectedFiles)) > 0 ? ' <span class="text-slate-700">(' . (count($selectedFolders) + count($selectedFiles)) . ' ' . trans_choice('lochness::client.filemanager.selected', (count($selectedFolders) + count($selectedFiles))) . ')</span>' : '') !!}</span>
                    </h2>
                </div>
            </div>
        </div>
    @endif
</div>
