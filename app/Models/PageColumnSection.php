<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageColumnSection extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'page',
        'icon',
        'position'
    ];

    /**
     * @return HasMany
     */
    public function columns(): HasMany
    {
        return $this->hasMany(PageColumn::class, 'section_id', 'id');
    }

    /**
     * Extend model behavior
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            // Remove columns
            $model->columns()->each(fn($ent) => $ent->delete());
        });
    }
}
