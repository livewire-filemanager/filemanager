<?php

namespace LivewireFilemanager\Filemanager\Models;

use LivewireFilemanager\Filemanager\Traits\HasMediaOwner;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use HasMediaOwner;
}
