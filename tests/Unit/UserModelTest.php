<?php

namespace Tests\Unit;

use App\Models\{Course, Role, User};
use Tests\ModelTestCase;

class UserModelTest extends ModelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::$class = User::class;
    }

    public function testCreate(): void
    {
        $this->assertModelExists(self::$class::factory()->create());
    }

    public function testFields(): void
    {
        $email = mb_strtoupper(uniqid() . $this->faker->safeEmail);
        $first_name = mb_strtolower($this->faker->firstName);
        $last_name = mb_strtolower($this->faker->lastName);
        $user_config = config('enums.user');

        $rand_status = mt_rand(0, count($user_config['statuses']) - 1);
        $rand_size = mt_rand(0, count($user_config['shirt_sizes']) - 1);
        $model = self::$class::factory()->create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'status' => mb_strtoupper($user_config['statuses'][$rand_status]),
            'shirt_size' => mb_strtoupper($user_config['shirt_sizes'][$rand_size])
        ]);

        $this->assertTrue($email !== $model->email);
        $this->assertTrue(mb_strtolower($email) === $model->email);
        $this->assertTrue(ucfirst($first_name) === $model->first_name);
        $this->assertTrue(ucfirst($last_name) === $model->last_name);
        $this->assertTrue($model->status === $user_config['statuses'][$rand_status]);
        $this->assertTrue($model->shirt_size === $user_config['shirt_sizes'][$rand_size]);
    }

    public function testModify(): void
    {
        $this->modelModificationTest([
            'first_name',
            'last_name',
            'email',
            'timezone',
            'country',
            'city',
            'region',
            'address',
            'ext_addr',
            'zip',
            'phone',
        ]);
    }

    public function testRelationToRole(): void
    {
        $role = Role::factory()->create();
        $user = self::$class::factory()->create(['role_id' => $role->id]);

        $this->assertTrue($user->role->slug == $role->slug);
    }

    public function testRelationToCourse()
    {
        $course = Course::factory()->create();
        $user = self::$class::factory()->create();

        $user->courses()->attach($course);

        $this->assertDatabaseHas('user_courses', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);
    }

    public function testRemove(): void
    {
        $this->modelRemovingTest(fn($model) => $this->assertDatabaseMissing('user_courses', ['user_id' => $model->id]));
    }
}
