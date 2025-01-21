<?php

namespace LivewireFilemanager\Filemanager\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use LivewireFilemanager\Filemanager\Models\Folder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestMediaModel extends Model
{
    protected $table = 'folders';
    protected $with = ['children'];
    protected $fillable = [
        'parent_id',
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
