<div>
    @if(!$currentFolder)
        <div class="px-4 py-12 w-full" x-data="{ open: false }">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>

                <h3 class="mt-2 text-sm font-semibold text-gray-900">{{ __('livewire-filemanager::filemanager.root_folder_not_configurated') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('livewire-filemanager::filemanager.root_folder_not_configurated_help') }}</p>

                <div class="mt-6">
                    <x-livewire-filemanager-button x-on:click="open = true">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"></path>
                        </svg>

                        <span>{{ __('livewire-filemanager::filemanager.add_your_first_folder') }}</span>
                    </x-livewire-filemanager-button>
                </div>
            </div>

            <x-livewire-filemanager-modal>
                <x-slot name="title">Modal title</x-slot>
            </x-livewire-filemanager-modal>
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
