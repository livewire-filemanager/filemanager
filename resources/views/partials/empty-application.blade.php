<div class="px-4 py-12 w-full bg-white dark:bg-zinc-800" x-data="{ create_root_folder: false }">
    <div class="text-center">
        <x-livewire-filemanager::icons.folder class="mx-auto h-16" />

        <h3 class="mt-2 font-semibold text-gray-900 dark:text-zinc-300">{{ __('livewire-filemanager::filemanager.root_folder_not_configurated') }}</h3>
        <p class="mt-1 text-base text-gray-500 dark:text-zinc-300">{{ __('livewire-filemanager::filemanager.root_folder_not_configurated_help') }}</p>

        <div class="mt-6">
            <x-livewire-filemanager::buttons.primary x-on:click="create_root_folder = true">
                <x-livewire-filemanager::icons.plus />

                <span>{{ __('livewire-filemanager::filemanager.add_your_first_folder') }}</span>
            </x-livewire-filemanager::buttons.primary>
        </div>
    </div>

    <x-livewire-filemanager-modal :modal="'create_root_folder'">
        <x-slot name="title">{{ __('livewire-filemanager::filemanager.add_your_first_folder') }}</x-slot>

        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900 dark:text-zinc-300">{{ __('livewire-filemanager::filemanager.root_folder_name') }}</label>
            <div class="relative mt-2 rounded-md shadow-sm">
                <x-livewire-filemanager::form.text-input type="text" autofocus wire:model="newFolderName" name="folder" id="folder" class="{{ ($errors->has('newFolderName') ? 'focus:ring-red-500 focus:border-red-500 focus:ring-red-500 ring-red-500 dark:focus:ring-red-600 dark:ring-red-600' : 'ring-gray-300 focus:ring-indigo-600') }}" />

                @error('newFolderName')
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <svg class="h-5 w-5 text-red-500 dark:text-red-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @enderror
            </div>

            @error('newFolderName')
                <p class="mt-2 text-sm text-red-600 dark:text-red-600" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <x-slot name="action">
            <x-livewire-filemanager::buttons.primary type="button" wire:click="saveNewFolder">
                {{ __('livewire-filemanager::filemanager.actions.save') }}
            </x-livewire-filemanager::buttons.primary>
        </x-slot>
    </x-livewire-filemanager-modal>
</div>
