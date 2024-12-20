<div x-cloak x-data="{ delete_items: false }"
    x-on:delete-items.window="delete_items = true"
    x-on:reset-media.window="delete_items = false">

    <x-livewire-filemanager-modal :modal="'delete_items'">
        <x-slot name="title">{{ __('livewire-filemanager::filemanager.delete_items') }}</x-slot>

        <p class="text-black dark:text-zinc-300">{{ __('livewire-filemanager::filemanager.delete_items_warning') }}</p>

        <x-slot name="action">
            <x-livewire-filemanager::buttons.danger type="button" wire:click="delete">
                {{ __('livewire-filemanager::filemanager.actions.delete') }}
            </x-livewire-filemanager::buttons.danger>
        </x-slot>
    </x-livewire-filemanager-modal>
</div>
