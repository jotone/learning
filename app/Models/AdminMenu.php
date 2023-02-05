<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminMenu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_menu';

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
        'route',
        'img',
        'parent_id',
        'position',
        'is_top'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_top' => 'boolean',
    ];

    /**
     * Get menu inheritors
     *
     * @return HasMany
     */
    public function subMenu(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * Get menu inheritors with submenus
     *
     * @return HasMany
     */
    public function subMenus(): HasMany
    {
        return $this->subMenu()->with('subMenus');
    }
}
