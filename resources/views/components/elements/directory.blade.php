@props(['folder', 'selectedFolders', 'index' => 0])

<div
    x-data="{
        clickTimeout: null,
        get viewMode() {
            return this.$root.viewMode || 'grid';
        }
    }"
    :class="{
        '!bg-gray-200/50 !hover:bg-gray-200/60 !dark:bg-gray-700 !hover:dark:bg-gray-700 group': @json($selectedFolders).includes({{ $folder->id }})
    }"
    x-on:click="
        if (this.clickTimeout) {
            clearTimeout(this.clickTimeout)
        }

        this.clickTimeout = setTimeout(() => {
            $wire.toggleFolderSelection({{ $folder->id }});
            $wire.handleFolderClick({{ $folder->id }});
            shiftKey = false;
        }, 200);
    "
    x-on:dblclick="$wire.navigateToFolder({{ $folder->id }}); shiftKey = false"
    data-id="{{ $folder->id }}"
    class="folder cursor-pointer select-none"
    :class="{
        'mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 hover:bg-blue-100/30 hover:dark:bg-gray-700 text-center': viewMode === 'grid',
        'w-full flex items-center px-4 hover:bg-blue-50/50 dark:hover:bg-zinc-700/50': viewMode === 'list'
    }"
    :style="viewMode === 'list' ? 'height: 28px;' : ''">

    <template x-if="viewMode === 'grid'">
        <div>
            <x-livewire-filemanager::icons.folder class="mx-auto w-16 h-16 mb-2" />
            <div class="flex flex-wrap text-center">
                <span :class="{ 'bg-blue-500 text-white dark:bg-blue-700 group': @json($selectedFolders).includes({{ $folder->id }}) }" class="text-ellipsis overflow-hidden break-words w-full block text-xs max-w-[150px] dark:text-zinc-200 rounded">{{ $folder->name }}</span>
                <small :class="{ 'text-blue-900': @json($selectedFolders).includes({{ $folder->id }}) }" class="w-full block text-xs text-blue-500">{{ $folder->elements() }}</small>
            </div>
        </div>
    </template>

    <template x-if="viewMode === 'list'">
        <div class="flex items-center w-full h-full">
            <x-livewire-filemanager::icons.folder class="w-4 h-4 me-3 flex-shrink-0" />
            <div class="flex-1 min-w-0 text-left">
                <div :class="{ 'text-white': @json($selectedFolders).includes({{ $folder->id }}) }" class="text-sm dark:text-zinc-200 leading-tight">{{ $folder->name }}</div>
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400 text-right" style="min-width: 100px;">
                {{ $folder->elements() }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400 text-right ms-8" style="min-width: 60px;">
                {{ __('livewire-filemanager::filemanager.folder') }}
            </div>
        </div>
    </template>
</div>
