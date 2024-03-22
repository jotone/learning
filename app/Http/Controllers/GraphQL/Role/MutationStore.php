<?php

namespace App\Http\Controllers\GraphQL\Role;

use App\Services\Str;
use App\Models\Role;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;

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
            'slug' => [
                'name' => 'slug',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
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
     * @param array $input
     * @return Role|Error
     */
    public function resolve($root, array $input): Role|Error
    {
        if ($this->checkUserRoleLevel($input['level'])) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        $input['slug'] = Str::generateUrl(empty($input['slug']) ? $input['name'] : $input['slug']);

        DB::beginTransaction();

        try {
            // Create the role
            $role = Role::create($input);

            $this->savePermissions($role, json_decode(base64_decode($input['permissions']), true));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }
        return $role;
    }
}