<?php

namespace App\Http\Controllers\GraphQL\Course;

use App\Models\Course;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MutationDestroy extends Mutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'destroy'
    ];

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('Course');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'numeric', 'exists:courses,id']
            ],
        ];
    }

    /**
     * Remove course
     *
     * @param $root
     * @param array $input
     * @return null|Error
     */
    public function resolve($root, array $input): ?Error
    {
        Course::destroy($input['id']);

        return null;
    }
}