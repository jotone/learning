<?php

namespace App\Http\Controllers\GraphQL\Category;

use App\Classes\Str;
use App\Enums\CategoryType;
use App\Models\Category;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MutationStore extends Mutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'create'
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
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string']
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
            'type' => [
                'name' => 'type',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string', Rule::in(array_merge(
                    array_values($category_enums),
                    array_keys($category_enums)
                ))]
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
            'position' => [
                'name' => 'position',
                'type' => Type::int()
            ]
        ];
    }

    /**
     * Store category
     *
     * @param $root
     * @param array $input
     * @return Category|Error
     */
    public function resolve($root, array $input): Category|Error
    {
        DB::beginTransaction();

        // Form the category url value
        $input['url'] = Str::generateUrl(empty($input['url']) ? $input['name'] : $input['url']);
        // Check if a such url already exists and modify it
        if (Category::where('url', $input['url'])->count()) {
            $input['url'] .= '-' . uniqid();
        }
        // Set current category position
        if (!isset($input['position'])) {
            $input['position'] = Category::count();
        }

        try {
            if (isset($input['name'])) {
                $input['name'] = mb_substr($input['name'], 0, 60);
            }
            // Create category
            $category = Category::create($input);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }

        return $category;
    }
}