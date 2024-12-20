@props(['folder', 'selectedFolders'])

<div
    :class="{ '!bg-gray-200/50 !hover:bg-gray-200/60 !dark:bg-gray-700 !hover:dark:bg-gray-700 group': @json($selectedFolders).includes({{ $folder->id }}) }"
    x-on:click="$wire.toggleFolderSelection({{ $folder->id }}); shiftKey = false"
    x-on:dblclick="$wire.navigateToFolder({{ $folder->id }}); shiftKey = false"
    data-id="{{ $folder->id }}"
    class="folder cursor-pointer mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 hover:bg-blue-100/30 hover:dark:bg-gray-700 text-center select-none">
        <x-livewire-filemanager::icons.folder class="mx-auto w-16 h-16 mb-2" />

        <div class="flex flex-wrap text-center">
            <span :class="{ 'bg-blue-500 text-white dark:bg-blue-700 group': @json($selectedFolders).includes({{ $folder->id }}) }" class="text-ellipsis overflow-hidden break-words w-full block text-xs max-w-[150px] dark:text-zinc-200 rounded">{{ $folder->name }}</span>
            <small :class="{ 'text-blue-900': @json($selectedFolders).includes({{ $folder->id }}) }" class="w-full block text-xs text-blue-500">{{ $folder->elements() }}</small>
        </div>
</div>
