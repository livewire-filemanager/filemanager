<?php

namespace LivewireFilemanager\Filemanager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestFolderModel extends Model
{
    protected $with = ['children'];

    protected $table = 'folders';

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
            return $this->children_count + $this->getMedia('medialibrary')->count().' éléments';
        } elseif ($this->children_count + $this->getMedia('medialibrary')->count() == 1) {
            return '1 élément';
        }

        return 'Aucun élément';
    }
}
