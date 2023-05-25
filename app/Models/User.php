<?php

namespace App\Models;

use App\Classes\FileHelper;
use App\Traits\ThumbnailsGenerationTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ThumbnailsGenerationTrait;

    /**
     * Thumb images settings
     *
     * @var array
     */
    protected array $thumbnail = [
        'folder' => '/images/users/',
        'key'    => 'user_img_processing'
    ];

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
        'activated_at'      => 'datetime',
        'last_activity'     => 'datetime',
    ];

    /**
     * Get the user's first name.
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(get: fn(string $value) => mb_convert_case($value, MB_CASE_TITLE));
    }

    /**
     * Get the user's last name.
     */
    protected function lastName(): Attribute
    {
        return Attribute::make(get: fn(?string $value) => empty($value) ? '' : mb_convert_case($value, MB_CASE_TITLE));
    }

    /**
     * Get user's full name
     *
     * @return Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value, array $attributes) => mb_convert_case(
                $attributes['first_name'] . ' ' . $attributes['last_name'], MB_CASE_TITLE
            )
        );
    }

    /**
     * Get or Set email value
     *
     * @return Attribute
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => mb_strtolower($value),
            set: fn(string $value) => mb_strtolower($value)
        );
    }

    /**
     * Set password value
     *
     * @return Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(set: fn(string $value) => bcrypt($value));
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
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            // Remove info
            $model->info()->delete();
            // Remove login history
            $model->loginHistory()->each(fn($ent) => $ent->delete());
            // Remove user files
            FileHelper::recursiveRemove(public_path('images/users/' . $model->id));
        });
    }
}
