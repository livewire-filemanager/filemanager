@props(['modal'])

<div x-dialog x-model="{{ $modal }}" style="display: none" class="fixed inset-0 overflow-y-auto z-10">
    <div x-dialog:overlay x-transition.opacity class="fixed inset-0 bg-indigo-950/50 dark:bg-zinc-700/80"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div x-dialog:panel x-transition class="relative max-w-xl w-full bg-white rounded-xl shadow-lg overflow-y-auto dark:bg-zinc-800">
            <div class="absolute top-0 end-0 pt-4 pe-4">
                <button type="button" @click="$dialog.close()" class="bg-gray-50 rounded-lg p-2 text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:bg-zinc-700 dark:text-zinc-200">
                    <span class="sr-only">{{ __('livewire-filemanager::filemanager.actions.close_modal') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <h2 class="text-xl font-bold text-black dark:text-zinc-300">{{ $title }}</h2>

                <div class="py-8 text-black dark:text-zinc-300">
                    {{ $slot }}
                </div>
            </div>

            <div class="p-4 flex justify-end gap-x-2 bg-gray-50 dark:bg-zinc-900/30">
                <x-livewire-filemanager::buttons.secondary type="button" x-on:click="$dialog.close()">
                    {{ __('livewire-filemanager::filemanager.actions.cancel') }}
                </x-livewire-filemanager::buttons.secondary>

                {{ $action }}
            </div>
        </div>
    </div>
</div>
