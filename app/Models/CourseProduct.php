<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProduct extends Model
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
        'product',
        'driver'
    ];

    /**
     * Related course
     *
     * @return BelongsTo
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }


    /**
     * Get only CopeCart products
     * @param $query
     * @return mixed
     */
    public function scopeCopeCart($query)
    {
        return $query->where('driver', 'copecart');
    }

    /**
     * Get only digistore24 products
     * @param $query
     * @return mixed
     */
    public function scopeDigistore24($query)
    {
        return $query->where('driver', 'digistore24');
    }
}
