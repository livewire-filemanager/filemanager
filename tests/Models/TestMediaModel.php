<?php

namespace LivewireFilemanager\Filemanager\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LivewireFilemanager\Filemanager\Models\Folder;

class TestMediaModel extends Model
{
    protected $table = 'folders';

    protected $with = ['children'];

    protected $fillable = [
        'uuid',
        'model_type',
        'model_id',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
    ];

    public $registerMediaConversionsUsingModelInstance = true;

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($folder) {
            if ($folder->isHomeFolder()) {
                return false;
            }
        });

        static::creating(function ($folder) {
            if (! config('livewire-filemanager.acl_enabled')) {
                return;
            }

            $user = auth()->getUser();

            if ($user) {
                $folder->user_id = $user->id;
            }
        });
    }

    protected static function booted()
    {
        static::addGlobalScope('user_id', function (Builder $builder) {
            if (! config('livewire-filemanager.acl_enabled')) {
                return;
            }

            $user = auth()->getUser();

            if ($user) {
                $builder->where(
                    'user_id',
                    $user->id
                );
            }
        });
    }

    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }

    public function isHomeFolder(): bool
    {
        return $this->id === 1;
    }

    public function parentWithoutRootFolder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id')->where('id', '!=', 1);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function elements(): string
    {
        return trans_choice('livewire-filemanager::filemanager.elements', $this->children_count + $this->getMedia('medialibrary')->count(), ['value' => $this->children_count + $this->getMedia('medialibrary')->count()]);
    }
}
