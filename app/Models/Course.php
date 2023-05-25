<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany, HasOne};
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    /*
    |-------------------------------------------------------------------
    | Status field values (status)
    |-------------------------------------------------------------------
    | 0 - Active
    | 1 - Coming soon
    |
    |-------------------------------------------------------------------
    | Tracking types values (tracking_type). See config/enums.php
    |-------------------------------------------------------------------
    | 0 - Enable Auto Approve
    | 1 - Enable for every submission
    | 2 - Enable for first submission
    |
    */
    use HasFactory;

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
        'link',
        'expire_link',
        'support_link',
        'fb_link',
        'status',
        'invitation_email',
        'position',

        'tracking_type',
        'tracking_status',

        'optional_duration',
        'optional_expire_page'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'invitation_email' => 'boolean'
    ];

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
     * Get course info
     *
     * @return HasOne
     */
    public function info(): HasOne
    {
        return $this->hasOne(CourseInfo::class, 'course_id', 'id');
    }

    /**
     * Get related course products
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(CourseProduct::class, 'course_id', 'id');
    }

    /**
     * Related testimonial
     *
     * @return HasOne
     */
    public function testimonial(): HasOne
    {
        return $this->hasOne(CourseTestimonial::class, 'course_id', 'id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            // Remove info
            $model->info()->delete();
            // Remove bounded products
            $model->products()->each(fn($ent) => $ent->delete());
            // Remove testimonials
            $model->testimonial()->delete();
            // Remove course relations
            DB::table('course_relations')
                ->where('course_id', $model->id)
                ->orWhere('related_id', $model->id)
                ->delete();
        });
    }
}
