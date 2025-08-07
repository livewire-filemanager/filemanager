@props(['folder', 'media', 'selectedFiles', 'selectedFolders' => [], 'key'])

<div
    x-data="{ isDragging: false }"
    draggable="true"
    x-on:dragstart="
        const isSelected = @json($selectedFiles).includes({{ $media->id }});
        if (!isSelected) {
            $wire.clearSelection();
            $wire.toggleFileSelection({{ $media->id }});
        }
        isDragging = true;
        event.dataTransfer.effectAllowed = 'move';
        const selectedFolders = @json($selectedFolders ?? []);
        event.dataTransfer.setData('text/plain', JSON.stringify({
            folders: isSelected ? selectedFolders : [],
            files: isSelected ? @json($selectedFiles) : [{{ $media->id }}]
        }));
    "
    x-on:dragend="isDragging = false"
    :class="{ 
        '!bg-gray-200/50 !hover:bg-gray-200/60 !dark:bg-gray-700 !hover:dark:bg-gray-700 group': @json($selectedFiles).includes({{ $media->id }}),
        'opacity-50': isDragging
    }"
    x-on:click.stop="
        const isSelected = @json($selectedFiles).includes({{ $media->id }});
        
        if (event.ctrlKey || event.metaKey) {
            $wire.toggleFileSelection({{ $media->id }});
        } else {
            if (!isSelected) {
                $wire.clearSelection();
                $wire.toggleFileSelection({{ $media->id }});
            }
        }
        
        $nextTick(() => {
            $wire.handleMediaClick({{ $media->id }});
        });
    "
    x-on:mousedown.stop=""
    data-id="{{ $media->id }}"
    id="{{ $key }}"
    class="file cursor-pointer mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 hover:bg-blue-100/30 hover:dark:bg-gray-700 text-center select-none">
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
