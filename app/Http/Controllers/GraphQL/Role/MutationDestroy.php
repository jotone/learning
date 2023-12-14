<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Log;
use GraphQL\Type\Definition\{Type, ResolveInfo};
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MutationDestroy extends Mutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name'  => 'destroy'
    ];

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('Role');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'id' => [
                'name'  => 'id',
                'type'  => Type::nonNull(Type::int()),
                'rules' => ['required', 'numeric', 'exists:roles,id']
            ],
        ];
    }

    /**
     * Remove role
     *
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @param Closure $getSelectFields
     * @return null
     */
    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        Role::destroy($args['id']);

        return null;
    }

}