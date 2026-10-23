<?php

namespace LivewireFilemanager\Filemanager\Models;

use LivewireFilemanager\Filemanager\Traits\HasMediaOwner;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use HasMediaOwner;

    // Spatie's ConversionCollection accesses $media->model whenever the
    // owning model has registerMediaConversionsUsingModelInstance = true
    // (Folder's default). Without eager loading it here, that access
    // triggers a lazy load, which throws if the app has disabled lazy
    // loading (Model::preventLazyLoading()).
    protected $with = ['model'];
}
