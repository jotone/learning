<?php

namespace App\Http\Controllers\GraphQL\Course;

use App\Models\Course;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;

class MutationUpdate extends CourseMutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'update'
    ];

    /**
     * @return array
     */
    public function args(): array
    {
        return array_merge([
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:courses,id']
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ]
        ], $this->mutationDefaultArgs());
    }

    /**
     * Update Course
     *
     * @param $root
     * @param array $input
     * @return Course|Error
     */
    public function resolve($root, array $input): Course|Error
    {
        // Find model
        $course = Course::findOrFail($input['id']);

        DB::beginTransaction();

        try {
            foreach ($input as $key => $val) {
                if ('name' === $key) {
                    $val = mb_substr($val, 0, 90);
                }

                if ('description' === $key) {
                    $val = mb_substr($val, 0, 300);
                }

                if ('categories' !== $key) {
                    $course->$key = $val;
                }
            }
            // Save course if it was changed
            $course->isDirty() && $course->save();
            // Set course categories
            isset($input['categories']) && $this->setCategories($course, $input['categories']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }

        return $course->fresh();
    }
}