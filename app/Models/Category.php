<?php

namespace App\Models;

use App\Classes\FileHelper;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;

class Category extends Model
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
        'img_url',
        'description',
        'learn_more_link',
        'position'
    ];

    /**
     * Set course avatar image
     *
     * @return Attribute
     */
    protected function imgUrl(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => $this->getThumbs($value),
            set: fn(mixed $value, array $attributes) => $value instanceof UploadedFile && $value->isWritable()
                ? $this->saveImage($value, 'images/categories/' . $this->getEntityId(), 'week_img_processing')
                : (is_string($value) ? $value : $attributes['img_url'] ?? null)
        );
    }

    /**
     * Get category courses
     *
     * @return MorphToMany
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'entity', 'category_relations');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            // Remove category images
            FileHelper::recursiveRemove(public_path('images/categories/' . $model->id));
            // Detach category courses
            $model->courses()->detach();
        });
    }
}
