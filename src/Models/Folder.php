<?php

namespace LivewireFilemanager\Filemanager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Folder extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $with = ['children'];

    protected $fillable = [
        'parent_id',
        'user_id',
        'tenant_id',
        'name',
        'slug',
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
            if (!config('livewire-fileuploader.acl_enabled')) {
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
            if (!config('livewire-fileuploader.acl_enabled')) {
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
        return is_null($this->parent_id);
    }

    public function parentWithoutRootFolder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id')->whereNotNull('parent_id');
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

    /**
     * Some media conversions for all models
     *
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')->format('webp')->width(100)->performOnCollections('medialibrary');
    }
}
