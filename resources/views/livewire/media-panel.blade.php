<div x-cloak x-data="{ show: false }"
    x-on:load-media.window="show = true"
    x-on:load-folder.window="show = false"
    x-on:reset-media.window="show = false"
    x-on:reset-folder.window="show = false"
    x-on:click.stop=""
    :class="{ 'block animate-[slideIn_0.5s_forwards]': show, 'hidden animate-[slideOut_0.5s_forwards]': !show }"
    class="absolute w-screen max-w-md top-0 end-0 bottom-0">
        <div class="bg-white border-l min-h-full shadow-lg border-zinc-300 p-4 relative dark:bg-zinc-900 dark:border-zinc-800">
            <div class="absolute end-4 top-4 flex h-7 items-center">
                <button @click="Livewire.dispatch('clear-all-selections'); Livewire.dispatch('reset-media', { media_id: null })" type="button" class="relative rounded-md border text-zinc-500 border-zinc-300 p-1 focus:outline-none focus:ring-2 focus:ring-white dark:border-zinc-600 dark:text-zinc-500">
                    <span class="absolute -inset-2.5"></span>
                    <span class="sr-only">Close panel</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            @if($media)
                <div class="px-4">
                    @if($media->hasGeneratedConversion('thumbnail'))
                        <img src="{{ $media->getUrl('thumbnail') }}" class="mx-auto shadow border p-1 bg-white max-w-20 max-h-20 mb-2" alt="folder">
                    @else
                        <x-dynamic-component :component="'livewire-filemanager::icons.mimes.' . getFileType($media->mime_type)" class="mx-auto w-16 h-16 mb-2.5" />
                    @endif
                </div>

                <ul class="mt-12 border-t pt-4 dark:border-zinc-600">
                    <li>
                        <strong class="text-black dark:text-gray-300">{{ $media->name }}</strong>
                    </li>
                    <li>
                        <span class="text-black dark:text-gray-300">{{ $media->mime_type }} - {{ $media->human_readable_size }}</span>
                    </li>
                </ul>

                <div class="mt-4">
                    <strong class="text-black font-medium dark:text-gray-300">Informations</strong>
                    <dl class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200 dark:divide-zinc-600 dark:border-zinc-600">
                        <div class="flex justify-between py-3 text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-200">{{ __('livewire-filemanager::filemanager.created') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-300">{{ $media->created_at->diffForHumans() }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-200">{{ __('livewire-filemanager::filemanager.modified') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-300">{{ $media->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="pb-4 pt-4 relative">
                    <div class="flex gap-x-4 text-sm">
                        <button type="button" wire:click="renameFile" class="group border rounded p-1.5 inline-flex items-center font-medium text-blue-500 group-hover:text-blue-900 dark:text-blue-300 dark:group-hover:text-blue-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                        </button>

                        <a href="{{ $media->getUrl() }}" download class="group border rounded p-1.5 inline-flex items-center font-medium text-blue-500 group-hover:text-blue-900 dark:text-blue-300 dark:group-hover:text-blue-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </a>

                        <button type="button" wire:click.prevent="$dispatch('copy-link', { link: '{{ getMediaFullPath($media) }}' })" class="group border rounded p-1.5 inline-flex items-center font-medium text-blue-500 group-hover:text-blue-900 dark:text-blue-300 dark:group-hover:text-blue-400">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M12.232 4.232a2.5 2.5 0 013.536 3.536l-1.225 1.224a.75.75 0 001.061 1.06l1.224-1.224a4 4 0 00-5.656-5.656l-3 3a4 4 0 00.225 5.865.75.75 0 00.977-1.138 2.5 2.5 0 01-.142-3.667l3-3z"></path>
                                <path d="M11.603 7.963a.75.75 0 00-.977 1.138 2.5 2.5 0 01.142 3.667l-3 3a2.5 2.5 0 01-3.536-3.536l1.225-1.224a.75.75 0 00-1.061-1.06l-1.224 1.224a4 4 0 105.656 5.656l3-3a4 4 0 00-.225-5.865z"></path>
                            </svg>
                            <span class="ms-2">{{ __('livewire-filemanager::filemanager.actions.copy_url') }}</span>
                        </button>
                    </div>

                    <div id="copyNotification" class="hidden top-0 text-white text-sm rounded px-3 p-2 mt-2"></div>
                </div>
            @endif
        </div>
</div>
