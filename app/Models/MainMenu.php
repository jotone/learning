<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainMenu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'main_menu';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'img',
        'img_color',
        'parent_id',
        'position',
        'trial_upgrade',
        'courses'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_upgrade' => 'boolean',
        'courses' => 'array'
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
