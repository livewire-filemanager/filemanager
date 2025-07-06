@props(['folder', 'media', 'selectedFiles', 'key', 'index' => 0])

<div
    x-data="{
        get viewMode() {
            return this.$root.viewMode || 'grid';
        }
    }"
    x-on:click="$wire.toggleFileSelection({{ $media->id }}); shiftKey = false; $wire.handleMediaClick({{ $media->id }})"
    data-id="{{ $media->id }}"
    id="{{ $key }}"
    class="file cursor-pointer select-none"
    :class="{
        '!bg-gray-200/50 !hover:bg-gray-200/60 !dark:bg-gray-700 !hover:dark:bg-gray-700 group': @json($selectedFiles).includes({{ $media->id }}),
        'mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 hover:bg-blue-100/30 hover:dark:bg-gray-700 text-center': viewMode === 'grid',
        'w-full flex items-center px-4 hover:bg-blue-50/50 dark:hover:bg-zinc-700/50': viewMode === 'list'
    }"
    :style="viewMode === 'list' ? 'height: 28px;' : ''">

    <template x-if="viewMode === 'grid'">
        <div>
            <div>
                @if($media->hasGeneratedConversion('thumbnail'))
                    <img src="{{ $media->getUrl('thumbnail') }}" class="mx-auto shadow border p-1 bg-white max-w-20 max-h-20 mb-2" alt="folder">
                @else
                    <x-dynamic-component id="icon-{{ $key }}" :component="'livewire-filemanager::icons.mimes.' . getFileType($media->mime_type)" class="mx-auto w-16 h-16 mb-2.5" />
                @endif
            </div>
            <div class="flex flex-wrap text-center">
                <span class="text-ellipsis overflow-hidden break-words w-full block text-xs max-w-[150px] dark:text-zinc-200">{{ trimString($media->name, 38) }}</span>
            </div>
        </div>
    </template>

    <template x-if="viewMode === 'list'">
        <div class="flex items-center w-full h-full">
            <div class="flex-shrink-0 me-3">
                @if($media->hasGeneratedConversion('thumbnail'))
                    <img src="{{ $media->getUrl('thumbnail') }}" class="w-4 h-4 object-cover rounded" alt="thumbnail">
                @else
                    <x-dynamic-component :component="'livewire-filemanager::icons.mimes.' . getFileType($media->mime_type)" class="w-4 h-4" />
                @endif
            </div>
            <div class="flex-1 min-w-0 text-left">
                <div class="text-sm dark:text-zinc-200 leading-tight">{{ $media->name }}</div>
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400 text-right" style="min-width: 100px;">
                {{ formatBytes($media->size) }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400 text-right ms-8" style="min-width: 60px;">
                {{ strtoupper(pathinfo($media->name, PATHINFO_EXTENSION) ?: 'FILE') }}
            </div>
        </div>
    </template>
</div>
