<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
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
        'key',
        'value',
        'data_type',
        'section',
        'extra_data'
    ];

    /**
     * Get settings value
     *
     * @return mixed
     */
    protected function val(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => match ($attributes['data_type']) {
                // Variable is type of boolean
                'boolean' => (bool)(int)$attributes['value'],
                // Variable is a JSON object
                'array' => json_decode($attributes['value']),
                // Variable is a datetime
                'timestamp' => !empty($attributes['value'])
                    ? Carbon::createFromFormat('Y-m-d H:i:s', $attributes['value'])
                    : null,
                // String variable
                default => $attributes['value'],
            }
        );
    }

    /**
     * Set setting value
     *
     * @param $value
     * @return void
     */
    public function setValueAttribute($value): void
    {
        $this->attributes['data_type'] = gettype($value);

        if (is_null($value)) {
            $this->attributes['data_type'] = 'string';
            $value = '';
        } else if (is_array($value)) { // Check value is array
            $this->attributes['data_type'] = 'array';
            $value = json_encode($value);
        } else if ($value === 'true' || $value === 'false' || is_bool($value)) { // Check value is boolean
            $this->attributes['data_type'] = 'boolean';
            $value = is_string($value) ? $value === 'true' : $value;
        } else if ($value instanceof Carbon) { // Check if value is instance of Carbon class
            $this->attributes['data_type'] = 'timestamp';
            $value = $value->format('Y-m-d H:i:s');
        } else if (is_string($value)) {
            // Check if value is a datetime string
            $this->attributes['data_type'] = \DateTime::createFromFormat('Y-m-d H:i:s', $value) !== false
                ? 'timestamp'
                : 'string';
        }
        // Set setting value
        $this->attributes['value'] = $value;
    }
}
