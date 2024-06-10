@props(['folder', 'media', 'selectedFiles'])

<div
    :class="{ '!bg-gray-200/50 !hover:bg-gray-200/60 group': @json($selectedFiles).includes({{ $media->id }}) }"
    x-on:click="$wire.toggleFileSelection({{ $media->id }}); shiftKey = false; $wire.handleMediaClick({{ $media->id }})"
    data-id="{{ $media->id }}"
    class="file cursor-pointer mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 hover:bg-blue-100/30 text-center select-none">
    @if($media->hasGeneratedConversion('mini-thumb-webp'))
        <img src="{{ $media->getUrl('mini-thumb-webp') }}" class="mx-auto shadow-[rgba(50,50,93,0.25)_0px_6px_12px_-2px,_rgba(0,0,0,0.3)_0px_3px_7px_-3px] p-1 bg-white max-w-20 max-h-20 mb-2" alt="folder">
    @else
        <x-dynamic-component :component="'livewire-filemanager::icons.mimes.' . getFileType($media->mime_type)" class="w-8 h-8" />
    @endif

    <div class="flex flex-wrap text-center">
        <span class="text-ellipsis overflow-hidden break-words w-full block text-xs max-w-[150px]">{{ trimString($media->name, 38) }}</span>
    </div>
</div>
