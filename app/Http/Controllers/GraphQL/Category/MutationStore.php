<?php

namespace App\Http\Controllers\GraphQL\Category;

use App\Models\Category;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
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
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'learn_more_link' => [
                'name' => 'learn_more_link',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
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

        $input['url'] = generateUrl(empty($input['url']) ? $input['name'] : $input['url']);
        if (Category::where('url', $input['url'])->count()) {
            $input['url'] .= '-' . uniqid();
        }

        $input['position'] = Category::count();

        try {
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