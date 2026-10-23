<?php

namespace LivewireFilemanager\Filemanager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LivewireFilemanager\Filemanager\Models\Media as FilemanagerMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Folder extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $with = ['children'];

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];

    public $registerMediaConversionsUsingModelInstance = true;

    protected static function booted(): void
    {
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
        // Respect a value already batched in via ->withCount('children')
        // instead of always re-querying live (which causes a query per
        // folder when rendering a list).
        if (array_key_exists('children_count', $this->attributes)) {
            return (int) $this->attributes['children_count'];
        }

        return $this->children()->count();
    }

    public function isHomeFolder(): bool
    {
        return is_null($this->parent_id);
    }

    public function getDepth(): int
    {
        $depth = 0;
        $current = $this;

        while ($current->parent_id !== null) {
            $depth++;
            $current = $current->parent;
        }

        return $depth;
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
        // media_count falls back to a live query when not batched in via
        // ->withCount(); children_count's own accessor does the same.
        $totalCount = $this->children_count + ($this->media_count ?? $this->getMedia('medialibrary')->count());

        return trans_choice('livewire-filemanager::filemanager.elements', $totalCount, ['value' => $totalCount]);
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

    public function getMediaModel(): string
    {
        return FilemanagerMedia::class;
    }
}
