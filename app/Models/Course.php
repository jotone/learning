<?php

namespace App\Models;

use App\Classes\FileHelper;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};
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
     * Get or set User's status
     *
     * @return Attribute
     */
    protected function status(): Attribute
    {
        return $this->treatStatusField(config('enums.course.statuses'));
    }

    /**
     * Set course avatar image
     *
     * @return Attribute
     */
    protected function imgUrl(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => $this->getThumbs($value),
            set: fn(mixed $value) => $value instanceof UploadedFile && $value->isWritable()
                ? $this->saveImage($value, 'courses', 'course_img_processing')
                : $this->attributes['img_url']
        );
    }

    /**
     * Get courses relationship
     *
     * @return BelongsToMany
     */
    public function courseRelation(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'course_relations', 'course_id', 'related_id')->withPivot([
            'relation_type'
        ]);
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
            // Remove course to user relations
            $model->users()->detach();
            // Remove related products
            $model->products()->each(fn($q) => $q->delete());
            // Remove course relations
            DB::table('course_relations')
                ->where('course_id', $model->id)
                ->orWhere('related_id', $model->id)
                ->delete();

            FileHelper::recursiveRemove(public_path('images/courses/' . $model->id));
        });
    }
}
