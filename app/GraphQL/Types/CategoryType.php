<?php

namespace App\GraphQL\Types;

use App\Models\Category;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CategoryType extends GraphQLType
{
    /**
     * @var string[] $attributes
     */
    protected $attributes = [
        'name' => 'Category',
        'description' => 'A category',
        'model' => Category::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::nonNull(Type::int())],
            'name' => ['type' => Type::string()],
            'url' => [
                'type' => Type::string(),
                'resolve' => fn($category, $input) => generateUrl(empty($category->url) ? $input['name'] : $category->url)
            ],
            'img_url' => ['type' => GraphQL::type('StringOrListOfStrings')],
            'description' => ['type' => Type::string()],
            'learn_more_link' => ['type' => Type::string()],
            'position' => ['type' => Type::int()],
            'created_at' => ['type' => Type::string()],
            'updated_at' => ['type' => Type::string()]
        ];
    }
}