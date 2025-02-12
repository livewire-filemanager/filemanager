<?php

namespace LivewireFilemanager\Filemanager\Policies;

use Illuminate\Foundation\Auth\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    public function view(User $user, Media $file)
    {
        if (! config('livewire-fileuploader.acl_enabled')) {
            return true;
        }

        return $file->custom_properties['user_id'] === $user->id;
    }

    public function delete(User $user, Media $file)
    {
        if (! config('livewire-fileuploader.acl_enabled')) {
            return true;
        }

        return $file->custom_properties['user_id'] === $user->id;
    }
}
