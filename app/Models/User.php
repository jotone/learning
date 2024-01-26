<?php

namespace App\Models;

use App\Classes\FileHelper;
use App\Enums\{ShirtSize, UserStatus};
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, ModelTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * Status
     *  0 - active,
     *  1 - missing-details,
     *  2 - inactive,
     *  3 - suspended
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
        'time_online',
        'role_id',
        'timezone',
        'country',
        'city',
        'region',
        'address',
        'ext_addr',
        'zip',
        'phone',
        'shirt_size',
        'signature',
        'signature_ip',
        'signature_date'
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
        'password' => 'hashed',
        'last_activity' => 'datetime',
        'activated_at' => 'datetime',
        'signature_date' => 'datetime',
    ];

    /**
     * Get the user's first name.
     *
     * @return Attribute
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => mb_convert_case($value, MB_CASE_TITLE)
        );
    }

    /**
     * Get the user's last name.
     *
     * @return Attribute
     */
    protected function lastName(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => empty($value) ? '' : mb_convert_case($value, MB_CASE_TITLE)
        );
    }

    /**
     * Set user's email
     *
     * @return Attribute
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => mb_strtolower($value)
        );
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
     * Set the avatar image
     *
     * @return Attribute
     */
    protected function imgUrl(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => $this->getThumbs($value),
            set: fn(mixed $value, array $attributes) => $value instanceof UploadedFile && $value->isWritable()
                ? $this->saveImage($value, 'images/users/' . $this->getEntityId(), 'user_img_processing')
                : (is_string($value) ? $value : $attributes['img_url'] ?? null)
        );
    }

    /**
     * Get or set User's shirt size
     *
     * @return Attribute
     */
    protected function shirtSize(): Attribute
    {
        return Attribute::make(
            get: fn(?int $val) => ShirtSize::fromValue($val),
            set: fn(string $size) => ShirtSize::fromName($size)
        );
    }

    /**
     * Set user's signature image
     *
     * @return Attribute
     */
    protected function signature(): Attribute
    {
        return Attribute::make(
            set: fn(mixed $value, array $attributes) => $value instanceof UploadedFile && $value->isWritable()
                ? $this->saveImage($value, 'images/users/' . $this->getEntityId())
                : (is_string($value) ? $value : $attributes['signature'] ?? null)
        );
    }

    /**
     * Get or set User's status
     *
     * @return Attribute
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn(?int $val) => UserStatus::fromValue($val),
            set: fn(string $val) => UserStatus::fromName($val)
        );
    }

    /**
     * Related courses
     *
     * @return BelongsToMany
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'user_courses', 'user_id', 'course_id')->withPivot([
            'enrolled_at',
            'expires_at',
            'created_at',
            'certificated_at',
            'progress'
        ]);
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
     * Get only admins
     *
     * @param $query
     * @return mixed
     */
    public function scopeAdmin($query): mixed
    {
        return $query->where('role_id', Role::where('slug', 'admin')->value('id'));
    }

    /**
     * Get only coaches
     *
     * @param $query
     * @return mixed
     */
    public function scopeCoach($query): mixed
    {
        return $query->where('role_id', Role::where('slug', 'coach')->value('id'));
    }

    /**
     * Get only students
     *
     * @param $query
     * @return mixed
     */
    public function scopeStudent($query): mixed
    {
        return $query->where('role_id', Role::where('slug', 'student')->value('id'));
    }

    /**
     * Get only superusers
     *
     * @param $query
     * @return mixed
     */
    public function scopeSuperuser($query): mixed
    {
        return $query->where('role_id', Role::where('slug', 'superuser')->value('id'));
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
            FileHelper::recursiveRemove(public_path('images/users/' . $model->id));
            // Remove course relation
            $model->courses()->detach();
        });
    }
}
