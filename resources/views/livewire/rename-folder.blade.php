<div x-cloak x-data="{ folder: false }"
    x-on:rename-folder.window="folder = true"
    x-on:reset-folder.window="folder = false">

    <x-livewire-filemanager-modal :modal="'folder'">
        <x-slot name="title">
            {{ __('livewire-filemanager::filemanager.rename_folder') }}
        </x-slot>

        <div>
            <input type="text" wire:model="name" class="rounded border border-zinc-300 w-full py-2 px-3 zinc-500 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:me-2 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-500">
        </div>
        @error('name')
            <span class="text-sm text-red-500 dark:text-red-400">{{ $message }}</span>
        @enderror

        <x-slot name="action">
            <x-livewire-filemanager::buttons.primary type="button" wire:click="save">
                {{ __('livewire-filemanager::filemanager.actions.save') }}
            </x-livewire-filemanager::buttons.primary>
        </x-slot>
    </x-livewire-filemanager-modal>
</div>
