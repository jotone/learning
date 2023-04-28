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
        'about'
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
                'boolean'   => (bool)(int)$attributes['value'],
                'array'     => json_decode($attributes['value']),
                'timestamp' => !empty($attributes['value'])
                    ? Carbon::createFromFormat('Y-m-d H:i:s', $attributes['value'])
                    : null,
                default => $attributes['value'],
            }
        );
    }

    /**
     * Set setting value
     *
     * @param $value
     */
    public function setValueAttribute($value): void
    {
        $value = is_null($value) ? '' : $value;
        $this->attributes['data_type'] = gettype($value);
        if ($this->attributes['data_type'] === 'object' && $value instanceof Carbon) {
            $this->attributes['data_type'] = 'timestamp';
            $value = $value->format('Y-m-d H:i:s');
        }
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }
}
