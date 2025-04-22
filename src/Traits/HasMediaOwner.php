<?php

namespace LivewireFilemanager\Filemanager\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasMediaOwner
{
    protected static function booted()
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            if (! config('livewire-fileuploader.acl_enabled')) {
                return;
            }

			$user = null;

			try {
				$user = auth()->getUser();
			} catch (\Exception $e) {
				$user = auth()->user();
			}

			if ($user) {
                $builder->where(
                    'custom_properties->user_id',
                    $user->id
                );
            }
        });
    }
}
