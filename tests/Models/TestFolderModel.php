<?php

namespace LivewireFilemanager\Filemanager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LivewireFilemanager\Filemanager\Models\Folder;

class TestFolderModel extends Model
{
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
