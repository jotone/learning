<?php

namespace App\Http\Controllers\GraphQL\Category;

use App\Models\Category;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MutationSort extends Mutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'sort'
    ];

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('Category');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'list' => [
                'name' => 'list',
                'type' => Type::listOf(Type::int()),
                'rules' => ['required', 'array']
            ]
        ];
    }

    /**
     * Sort categories
     *
     * @param $root
     * @param array $input
     * @return Category|Error
     */
    public function resolve($root, array $input): Category|Error
    {
        if (empty($input['list'])) {
            return new Error('Empty categories list.');
        }
        foreach ($input['list'] as $position => $id) {
            $category = Category::findOrFail($id);
            $category->position = $position;
            $category->save();
        }

        return $category;
    }
}