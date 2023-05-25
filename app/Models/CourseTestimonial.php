<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseTestimonial extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'enable',
        'min_progress',
        'show_on_skip',
        'testimonial_text',
        'description_enable',
        'description_required',
        'video_enable',
        'video_required',
        'lessons_description_enable',
        'lessons_description_required',
        'lessons_description_text',
        'lessons_video_enable',
        'lessons_video_required'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enable' => 'boolean',
        'show_on_skip' => 'boolean',
        'description_enable' => 'boolean',
        'description_required' => 'boolean',
        'video_enable' => 'boolean',
        'video_required' => 'boolean',
        'lessons_description_enable' => 'boolean',
        'lessons_description_required' => 'boolean',
        'lessons_video_enable' => 'boolean',
        'lessons_video_required' => 'boolean'
    ];

    /**
     * Related course model
     *
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
