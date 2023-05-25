<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseInfo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_info';

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
        'enable_terms_conditions',
        'enable_signature',
        'terms_conditions_text',
        'enable_certification',
        'certificate_img_url',
        'certificate_coordinates',
        'enable_free_trial',
        'free_trial_upgrade_url',
        'free_trial_upgrade_title'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enable_terms_conditions' => 'boolean',
        'enable_signature' => 'boolean',
        'enable_certification' => 'boolean',
        'enable_free_trial' => 'boolean',
        'certificate_coordinates' => 'array'
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
