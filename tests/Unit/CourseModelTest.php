<?php

namespace Tests\Unit;

use App\Models\{Course, User};
use Tests\ModelTestCase;

class CourseModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = Course::class;
    }

    public function testCreate(): void
    {
        $this->assertModelExists(self::$class::factory()->create());
    }

    public function testModify(): void
    {
        $this->modelModificationTest(['name', 'url', 'description']);
    }

    public function testRelationToCourse()
    {
        $course = self::$class::factory()->create();
        $user = User::factory()->create();

        $user->courses()->attach($course);

        $this->assertDatabaseHas('user_courses', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest(fn($model) => $this->assertDatabaseMissing('user_courses', [
            'course_id' => $model->id
        ]));
    }
}