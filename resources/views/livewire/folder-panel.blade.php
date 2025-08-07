<div x-cloak x-data="{ show: false }"
    x-on:load-folder.window="show = true"
    x-on:load-media.window="show = false"
    x-on:reset-folder.window="show = false"
    x-on:reset-media.window="show = false"
    x-on:click.stop=""
    :class="{ 'block animate-[slideIn_0.5s_forwards]': show, 'hidden animate-[slideOut_0.5s_forwards]': !show }"
    class="absolute w-screen max-w-md top-0 end-0 bottom-0">
        <div class="bg-white border-l min-h-full shadow-lg border-zinc-300 p-4 relative dark:bg-zinc-900 dark:border-zinc-800">
            @if($folder)
                <strong class="text-black text-lg dark:text-gray-300">{{ $folder->name }}</strong>
            @endif

            <div class="absolute end-4 top-4 flex h-7 items-center">
                <button @click="Livewire.dispatch('clear-all-selections'); Livewire.dispatch('reset-folder', { folder_id: null })" type="button" class="relative rounded-md border text-zinc-500 border-zinc-300 p-1 focus:outline-none focus:ring-2 focus:ring-white dark:border-zinc-600 dark:text-zinc-500">
                    <span class="absolute -inset-2.5"></span>
                    <span class="sr-only">Close panel</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            @if($folder)
                <div class="mt-4">
                    <strong class="text-black font-medium dark:text-gray-300">Informations</strong>
                    <dl class="mt-2 divide-y divide-gray-200 border-b border-t border-gray-200 dark:divide-zinc-600 dark:border-zinc-600">
                        <div class="flex justify-between py-3 text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-200">{{ __('livewire-filemanager::filemanager.created') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-300">{{ $folder->created_at->diffForHumans() }}</dd>
                        </div>
                        <div class="flex justify-between py-3 text-sm font-medium">
                            <dt class="text-gray-500 dark:text-gray-200">{{ __('livewire-filemanager::filemanager.modified') }}</dt>
                            <dd class="text-gray-900 dark:text-gray-300">{{ $folder->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="pb-4 pt-4 relative">
                    <div class="flex gap-x-4 text-sm">
                        <button type="button" wire:click="renameFolder" class="group border rounded p-1.5 inline-flex items-center font-medium text-blue-500 group-hover:text-blue-900 dark:text-blue-300 dark:group-hover:text-blue-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                        </button>
                    </div>

                    <div id="copyNotification" class="hidden top-0 text-white text-sm rounded px-3 p-2 mt-2"></div>
                </div>
            @endif
        </div>
</div>
