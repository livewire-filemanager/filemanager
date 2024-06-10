<div>
    @if(!$currentFolder)
        <div class="px-4 py-12 w-full" x-data="{ open: false }">
            <div class="text-center">
                <x-livewire-filemanager::icons.folder class="mx-auto h-16" />

                <h3 class="mt-2 font-semibold text-gray-900">{{ __('livewire-filemanager::filemanager.root_folder_not_configurated') }}</h3>
                <p class="mt-1 text-base text-gray-500">{{ __('livewire-filemanager::filemanager.root_folder_not_configurated_help') }}</p>

                <div class="mt-6">
                    <x-livewire-filemanager::buttons.primary x-on:click="open = true">
                        <x-livewire-filemanager::icons.plus />

                        <span>{{ __('livewire-filemanager::filemanager.add_your_first_folder') }}</span>
                    </x-livewire-filemanager::buttons.primary>
                </div>
            </div>

            <x-livewire-filemanager-modal>
                <x-slot name="title">{{ __('livewire-filemanager::filemanager.add_your_first_folder') }}</x-slot>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">{{ __('livewire-filemanager::filemanager.root_folder_name') }}</label>
                    <div class="relative mt-2 rounded-md shadow-sm">
                        <x-livewire-filemanager::form.text-input type="text" autofocus wire:model="newFolderName" name="folder" id="folder" class="{{ ($errors->has('newFolderName') ? 'focus:ring-red-500 focus:border-red-500 focus:ring-red-500 ring-red-500' : 'ring-gray-300 focus:ring-indigo-600') }}" />

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
                    <x-livewire-filemanager::buttons.primary type="button" wire:click="saveNewFolder">
                        {{ __('livewire-filemanager::filemanager.actions.save') }}
                    </x-livewire-filemanager::buttons.primary>
                </x-slot>
            </x-livewire-filemanager-modal>
        </div>
    @else
        <div class="w-full" x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">
            <div class="w-full shadow-sm bg-white dark:bg-gray-700 dark:border-gray-800 pt-4 border border-slate-300 sm:rounded">
                <div class="px-4 pb-4 sm:px-5 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-200">
                        {{ $currentFolder->name }}

                        <span class="px-2 text-gray-600  dark:text-slate-300">|</span>
                        <span class="text-gray-500 text-sm dark:text-slate-300">{{ $currentFolder->elements() }}{!! ((count($selectedFolders) + count($selectedFiles)) > 0 ? ' <span class="text-slate-700">(' . (count($selectedFolders) + count($selectedFiles)) . ' ' . trans_choice('livewire-filemanager::filemanager.selected', (count($selectedFolders) + count($selectedFiles))) . ')</span>' : '') !!}</span>
                    </h2>

                    <div>
                        <input type="file" wire:model.live="files" name="files" id="fileInput" multiple style="display: none;">

                        <button class="border rounded p-1.5 px-4 flex text-sm items-center space-x-4 bg-slate-100" @click="Livewire.dispatch('reset-media', { media_id: null })" onclick="document.getElementById('fileInput').click();">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>

                            <span>{{ __('livewire-filemanager::filemanager.add_a_file') }}</span>
                        </button>
                    </div>

                    <div class="flex space-x-4 items-center">
                        <div class="flex items-center space-x-2 max-h-[25px]">
                            @if((count($selectedFolders) + count($selectedFiles)) > 0)
                                <div>
                                    <button wire:click="deleteItems" @click="Livewire.dispatch('reset-media', { media_id: null })" class="border rounded p-1.5 border-red-600 text-white bg-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mx-2 px-2">|</div>
                            @endif

                            @if ($this->currentFolder->id !== 1)
                                <div>
                                    <button class="border rounded p-1.5 border-slate-300" @click="Livewire.dispatch('reset-media', { media_id: null })" wire:click="navigateToParent">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mx-2 px-2">|</div>
                            @endif

                            <div>
                                <button class="border rounded p-1.5 border-slate-300" @click="Livewire.dispatch('reset-media', { media_id: null })" wire:click="createNewFolder">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                </button>
                            </div>

                            <input wire:model.live="search" @click="Livewire.dispatch('reset-media', { media_id: null })" class="rounded border border-slate-300 w-full py-2 px-3 zinc-500 leading-tight focus:outline-none focus:ring-lochness-200 focus:border-lochness-200 sm:mr-2 dark:bg-slate-700 dark:border-slate-800 dark:placeholder:text-slate-400 dark:text-slate-300 dark:focus:ring-1" type="search" placeholder="{{ __('livewire-filemanager::filemanager.search') }}...">
                        </div>
                    </div>
                </div>

                <div x-data="{ shiftKey: false }" class="border-t border-slate-300 shadow-inner overflow-x-hidden relative">
                    @if($search)
                        <div class="px-4 sm:px-5 py-1 bg-gray-100 border-b border-slate-300 text-sm">{{ (count($searchedFiles) + count($folders)) }} {{ trans_choice('livewire-filemanager::filemanager.search_results', count($searchedFiles) + count($folders)) }}</div>
                    @endif

                    <div id="folder-container" x-on:dblclick.self="$wire.createNewFolder()" class="p-2 pb-10 min-h-[400px] xl:min-h-[600px] xl:max-h-[600px] overflow-y-auto flex relative flex-wrap content-start" @keydown.shift.window="shiftKey = true" @keyup.shift.window="shiftKey = false">
                        @if ($isCreatingNewFolder)
                            <div class="cursor-pointer mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 text-center">
                                <x-livewire-filemanager::icons.folder class="mx-auto w-16 h-16 mb-2" />

                                <input type="text" id="new-folder-name" wire:model="newFolderName" wire:keydown.enter="saveNewFolder" class="text-center w-full rounded py-0.5 px-1 text-sm">
                            </div>
                        @endif

                        @foreach($folders->sortBy('name') as $folder)
                            <div
                            :class="{ '!bg-gray-200/50 !hover:bg-gray-200/60 group': @json($selectedFolders).includes({{ $folder->id }}) }"
                            x-on:click="$wire.toggleFolderSelection({{ $folder->id }}); shiftKey = false"
                            x-on:dblclick="$wire.navigateToFolder({{ $folder->id }}); shiftKey = false"
                            data-id="{{ $folder->id }}"
                            class="folder cursor-pointer mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 hover:bg-blue-100/30 text-center select-none">
                                <x-livewire-filemanager::icons.folder class="mx-auto w-16 h-16 mb-2" />
                                <div class="flex flex-wrap text-center">
                                    <span :class="{ '!bg-blue-500 !text-white group': @json($selectedFolders).includes({{ $folder->id }}) }" class="text-ellipsis overflow-hidden break-words w-full block text-sm max-w-[150px] rounded">{{ $folder->name }}</span>
                                    <small class="w-full block text-xs text-blue-500">{{ $folder->elements() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <nav class="border-t text-sm px-4 sm:px-5 py-1 flex items-center border-slate-300">
                    @foreach ($breadcrumb as $index => $folder)
                        <span class="cursor-pointer flex space-x-1 items-center" @click="Livewire.dispatch('reset-media', { media_id: null })" wire:click.prevent="navigateToBreadcrumb({{ $index }})">
                            <x-livewire-filemanager::icons.folder class="w-5 h-5" /> <span>{{ $folder->name }}</span>
                        </span>

                        @if (!$loop->last)
                            <div class="px-2">
                                <svg class="w-2 h-2" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 48 48" style="enable-background:new 0 0 48 48;" xml:space="preserve">
                                    <style type="text/css">
                                        .st0{fill:none;}
                                    </style>
                                    <path d="M15.7,0.8l-5.5,5.5L28,24L10.2,41.8l5.5,5.5L39,24L15.7,0.8z"/>
                                    <path class="st0" d="M0,0h48v48H0V0z"/>
                                </svg>
                            </div>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('new-folder-created', function () {
                const checkExist = setInterval(function() {
                    let input = document.getElementById('new-folder-name');
                    input.focus();
                    input.select();

                    clearInterval(checkExist);
                }, 100);
            });

            Livewire.on('copy-link', function (event) {
                navigator.clipboard.writeText(event.link)
                .then(() => {
                })
                .catch(err => {
                    console.error('Error in copying text: ', err);
                });
            });
        });
    </script>
</div>
