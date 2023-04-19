<?php

namespace App\Models;

use App\Traits\ThumbnailsGenerationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ThumbnailsGenerationTrait;

    /**
     * Image thumbs settings key
     *
     * @var string
     */
    protected string $thumbnail_setup = 'user_img_processing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'img_url',
        'about',
        'status',
        'activated_at',
        'last_activity',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activated_at' => 'datetime',
        'last_activity' => 'datetime',
    ];

    /**
     * @return string
     */
    public function getFirstNameAttribute(): string
    {
        return Str::ucfirst(mb_strtolower($this->attributes['first_name']));
    }

    /**
     * @return string
     */
    public function getLastNameAttribute(): string
    {
        return Str::ucfirst(mb_strtolower($this->attributes['last_name']));
    }

    /**
     * Set email value
     *
     * @param $value
     */
    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = mb_strtolower($value);
    }

    /**
     * Set password value
     *
     * @param string $value
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Related user info
     *
     * @return HasOne
     */
    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id');
    }

    /**
     * Related login history
     *
     * @return HasMany
     */
    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * Related role
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Extend model behavior
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Remove info
            $model->info()->delete();
            // Remove login history
            $model->loginHistory()->each(fn($entity) => $entity->delete());
        });
    }
}
