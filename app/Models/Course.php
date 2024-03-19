<?php

namespace App\Models;

use App\Services\FileHelper;
use App\Enums\{CourseStatus, CourseTracking};
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, MorphToMany};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * |-------------------------------------------------------------------
 * | Status field values (status)
 * |-------------------------------------------------------------------
 * | 0 - Active
 * | 1 - Coming soon
 * | 2 - Draft
 * |
 * |-------------------------------------------------------------------
 * | Tracking types values (tracking_type). See config/enums.php
 * |-------------------------------------------------------------------
 * | 0 - Enable Auto Approve
 * | 1 - Enable for every submission
 * | 2 - Enable for first submission
 * |
 */
class Course extends Model
{
    use HasFactory, ModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'url',
        'description',
        'img_url',
        'lang',
        'sale_page_url',
        'expire_url',
        'status',
        'invitation_email',
        'position',

        'tracking_type',
        'tracking_status',

        'optional_duration',
        'optional_expire_page',
        'instructor_id',
        'published_at',
        'terms_conditions_enable',
        'terms_conditions_text',
        'signature_enable',
        'certificate_enable',
        'certificate_img_url',
        'certificate_coordinates',
        'free_trial_enable',
        'free_trial_upgrade_url',
        'free_trial_upgrade_title'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'invitation_email' => 'boolean',
        'published_at' => 'datetime',
        'terms_conditions_enable' => 'boolean',
        'signature_enable' => 'boolean',
        'certificate_enable' => 'boolean',
        'free_trial_enable' => 'boolean',
        'certificate_coordinates' => 'array'
    ];

    /**
     * Set course preview image
     *
     * @return Attribute
     */
    protected function imgUrl(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => $this->getThumbs($value),
            set: fn(mixed $value, array $attributes) => $value instanceof UploadedFile && $value->isWritable()
                ? $this->saveImage($value, 'images/courses/' . $this->getEntityId(), 'course_img_processing')
                : (is_string($value) ? $value : $attributes['img_url'] ?? null)
        );
    }

    /**
     * Set course certificate image
     *
     * @return Attribute
     */
    protected function certificateImgUrl(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => $value,
            set: function (mixed $value, array $attributes) {
                return $value instanceof UploadedFile && $value->isWritable()
                    ? $this->saveImage($value, 'images/courses/' . $this->getEntityId())
                    : (is_string($value) ? $value : $attributes['certificate_img_url'] ?? null);
            }
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
            get: fn(int $val) => CourseStatus::fromValue($val),
            set: fn(string $val) => is_numeric($val) && isset(CourseStatus::forSelect()[$val])
                ? $val
                : CourseStatus::fromName($val)
        );
    }

    /**
     * Get or set the course tracking type
     *
     * @return Attribute
     */
    protected function trackingType(): Attribute
    {
        return Attribute::make(
            get: fn(int $val) => CourseTracking::fromValue($val),
            set: fn(string $val) => is_numeric($val) && isset(CourseTracking::forSelect()[$val])
                ? $val
                : CourseTracking::fromName($val)
        );
    }

    /**
     * Get the categories that are assigned to this course.
     *
     * @return MorphToMany
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'entity', 'category_relation', 'entity_id', 'category_id');
    }

    /**
     * Get courses relationship
     *
     * @return BelongsToMany
     */
    public function courseRelation(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'course_relation', 'course_id', 'related_id')->withPivot([
            'relation_type'
        ]);
    }

    /**
     * Get related instructor entity
     *
     * @return BelongsTo
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }

    /**
     * Get course products
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(CourseProduct::class, 'course_id', 'id');
    }

    /**
     * Related users
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_courses', 'course_id', 'user_id')->withPivot([
            'certificated_at',
            'enrolled_at',
            'expires_at',
            'created_at',
            'progress'
        ]);
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
            // Remove the course to the categories relation
            $model->categories()->detach();
            // Remove course to user relations
            $model->users()->detach();
            // Remove related products
            $model->products()->each(fn($q) => $q->delete());
            // Remove course relations
            DB::table('course_relation')
                ->where('course_id', $model->id)
                ->orWhere('related_id', $model->id)
                ->delete();

            FileHelper::recursiveRemove(public_path('images/courses/' . $model->id));
        });
    }
}
