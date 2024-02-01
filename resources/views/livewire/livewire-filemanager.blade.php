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
                <x-slot name="title">{{ __('livewire-filemanager::filemanager.add_your_first_folder') }}</x-slot>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{ __('livewire-filemanager::filemanager.root_folder_name') }}</label>
                    <div class="relative mt-2 rounded-md shadow-sm">
                        <input type="text" wire:model="newFolderName" name="folder" id="folder" class="block w-full rounded-md border-0 py-1.5 pl-3 text-gray-900 focus:outline-none shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset sm:text-sm sm:leading-6 {{ ($errors->has('newFolderName') ? 'focus:ring-red-500 focus:border-red-500 focus:ring-red-500 ring-red-500' : 'ring-gray-300 focus:ring-indigo-600') }}">

                        @error('newFolderName')
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @enderror
                    </div>

                    @error('newFolderName')
                        <p class="mt-2 text-sm text-red-600" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <x-slot name="action">
                    <button type="button" wire:click="saveNewFolder" class="bg-slate-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 px-5 py-2 rounded-lg text-white">
                        {{ __('livewire-filemanager::filemanager.actions.save') }}
                    </button>
                </x-slot>
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
