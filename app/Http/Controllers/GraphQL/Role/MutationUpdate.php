<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Models\Role;
use Closure;
use GraphQL\Error\Error;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\{Type, ResolveInfo};
use Symfony\Component\HttpFoundation\Response;

class MutationUpdate extends RoleMutation
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
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'integer', 'exists:roles,id']
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'slug' => [
                'name' => 'slug',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'level' => [
                'name' => 'level',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric', 'min:0', 'max:255']
            ],
            'permissions' => [
                'name' => 'permissions',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ]
        ];
    }

    /**
     * @param $root
     * @param $args
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @param Closure $getSelectFields
     * @return Role|Error
     */
    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields): Role|Error
    {
        // Find model
        $role = Role::findOrFail($args['id']);

        if ($this->checkUserRoleLevel($role->level) || $this->checkUserRoleLevel($args['level'])) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        DB::beginTransaction();

        try {
            // Update selected role fields
            foreach ($args as $key => $val) {
                if ($key === 'permissions') {
                    // Remove current role permissions
                    $role->permissions()->each(fn($entity) => $entity->delete());
                    // Set new permissions
                    $this->savePermissions($role, json_decode(base64_decode($args['permissions']), true));
                } else {
                    $role->{$key} = $val;
                }
            }

            $role->save();

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