<?php

namespace LivewireFilemanager\Filemanager\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'is_home_folder' => $this->isHomeFolder(),
            'elements_count' => $this->children_count + $this->getMedia('medialibrary')->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => FolderResource::collection($this->whenLoaded('children')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
