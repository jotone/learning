<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'level'
    ];

    /**
     * Related role permissions
     *
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'role_id', 'id');
    }

    /**
     * Related users
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Extend model behavior
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            // Remove related permissions
            $model->permissions()->each(fn($entity) => $entity->delete());
            // Reset roles for related users
            $model->users()->update([
                'role_id' => self::where('id', '!=', $model->id)->orderBy('level', 'desc')->first()->id
            ]);
        });
    }
}
