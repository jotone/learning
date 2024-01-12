<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\Role;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\{Type, ResolveInfo};
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MutationStore extends RoleMutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'create'
    ];

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
            'level' => [
                'name' => 'level',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'numeric', 'min:0', 'max:255']
            ],
            'permissions' => [
                'name' => 'permissions',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string']
            ]
        ];
    }

    /**
     * Store role
     *
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @param Closure $getSelectFields
     * @return Role|Error
     */
    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Role|Error
    {
        if ($this->checkUserRoleLevel($args['level'])) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        $args['slug'] = generateUrl($args['name']);

        DB::beginTransaction();

        try {
            $role = Role::create($args);

            $this->savePermissions($role, json_decode(base64_decode($args['permissions']), true));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(
                response()->json(['errors' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
        return $role;
    }
}