<?php

namespace LivewireFilemanager\Filemanager\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Folder extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $with = ['children'];

    public $registerMediaConversionsUsingModelInstance = true;

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($folder) {
            if ($folder->isHomeFolder()) {
                return false;
            }
        });
    }

    public function getChildrenCountAttribute()
    {
        return $this->children()->count();
    }

    public function isHomeFolder()
    {
        return $this->id === 1;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function elements()
    {
        if ($this->children_count + $this->getMedia('medialibrary')->count() > 1) {
            return $this->children_count + $this->getMedia('medialibrary')->count() . ' éléments';
        } elseif ($this->children_count + $this->getMedia('medialibrary')->count() == 1) {
            return '1 élément';
        }

        return 'Aucun élément';
    }

    /**
     * Some media conversions for all models
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')->format('webp')->width(100)->performOnCollections('filemanager');
    }
}
