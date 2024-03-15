<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Tests\ApiTestCase;

class ImageResourceApiTest extends ApiTestCase
{
    protected static string $route = 'api.image.';

    public function testDestroy(): void
    {
        $filename = uniqid() . '.jpg';
        // Fake file data
        $file = UploadedFile::fake()->image($filename);

        // Create a category
        $category = Category::factory()->create([
            'type' => Course::class
        ]);

        $folder = "/images/categories/{$category->id}/";
        $path = public_path($folder);
        // Save file
        $file->move($path, $filename);
        // Check file exists
        $this->assertFileExists($path . $filename);

        $related_path = $folder . $filename;

        // Set image to the category
        $category->img_url = $related_path;
        $category->save();
        // Check image url is saved on the database
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'img_url' => $related_path
        ]);


        // Send request to get rid of the image
        $this->actingAs($this->actor)
            ->delete(route(self::$route . 'destroy'), [
                'path' => $related_path,
                'entity' => Category::class,
                'entity_id' => $category->id,
                'field' => 'img_url'
            ])
            ->assertNoContent();

        $this->assertFileDoesNotExist($path . $filename);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'img_url' => $related_path
        ]);
    }
}