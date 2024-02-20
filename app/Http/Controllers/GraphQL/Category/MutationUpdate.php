<?php

namespace App\Http\Controllers\GraphQL\Category;

use App\Enums\CategoryType;
use App\Models\Category;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MutationUpdate extends Mutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'update'
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
        $category_enums = CategoryType::forSelect();
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'integer', 'exists:categories,id']
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'img_url' => [
                'name' => 'img_url',
                'type' => GraphQL::type('Upload'),
                'rules' => ['nullable', 'image']
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'learn_more_link' => [
                'name' => 'learn_more_link',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
                'rules' => ['nullable', 'string', Rule::in(array_merge(
                    array_values($category_enums),
                    array_keys($category_enums)
                ))]
            ],
            'position' => [
                'name' => 'position',
                'type' => Type::int()
            ]
        ];
    }

    /**
     * Update category
     *
     * @param $root
     * @param array $input
     * @return Category|Error
     */
    public function resolve($root, array $input): Category|Error
    {
        // Find model
        $category = Category::findOrFail($input['id']);

        DB::beginTransaction();

        try {
            foreach ($input as $key => $val) {
                $category->$key = $val;
            }
            // Save category if it was changed
            $category->isDirty() && $category->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }

        return Category::find($category->id);
    }
}