<?php

namespace LivewireFilemanager\Filemanager\Policies;

use Illuminate\Foundation\Auth\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Media $file)
    {
        if (! config('livewire-filemanager.acl_enabled')) {
            return true;
        }

        return $file->custom_properties['user_id'] === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Media $file)
    {
        if (! config('livewire-filemanager.acl_enabled')) {
            return true;
        }

        return $file->custom_properties['user_id'] === $user->id;
    }

    public function delete(User $user, Media $file)
    {
        if (! config('livewire-filemanager.acl_enabled')) {
            return true;
        }

        return $file->custom_properties['user_id'] === $user->id;
    }

    public function restore(User $user, Media $file)
    {
        if (! config('livewire-filemanager.acl_enabled')) {
            return true;
        }

        return $file->custom_properties['user_id'] === $user->id;
    }

    public function forceDelete(User $user, Media $file)
    {
        if (! config('livewire-filemanager.acl_enabled')) {
            return true;
        }

        return $file->custom_properties['user_id'] === $user->id;
    }
}
