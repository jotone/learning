<?php

namespace App\Http\Controllers\GraphQL\User;

use Illuminate\Http\Request;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Enums\{ShirtSize, UserStatus};
use App\Models\{Role, User};
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MutationUpdate extends UserMutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'update'
    ];

    /**
     * Fields allowed to update
     * @var array|string[]
     */
    protected array $update_fields = [
        'first_name',
        'last_name',
        'email',
        'password',
        'img_url',
        'about',
        'status',
        'timezone',
        'country',
        'city',
        'region',
        'address',
        'ext_addr',
        'zip',
        'phone',
        'shirt_size',
        'signature',
        'role_id'
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
                'rules' => ['required', 'integer', 'exists:users,id']
            ],
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'rules' => ['nullable', 'email']
            ],
            'password' => [
                'name' => 'password',
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'min:8']
            ],
            'confirmation' => [
                'name' => 'confirmation',
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'same:password']
            ],
            'about' => [
                'name' => 'about',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'img_url' => [
                'name' => 'img_url',
                'type' => GraphQL::type('Upload'),
                'rules' => ['nullable', 'image']
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'rules' => ['nullable', 'string', Rule::in(array_column(UserStatus::cases(), 'name'))]
            ],
            'role_id' => [
                'name' => 'role_id',
                'type' => Type::int(),
                'rules' => ['nullable', 'exists:roles,id']
            ],
            'timezone' => [
                'name' => 'timezone',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'country' => [
                'name' => 'country',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'city' => [
                'name' => 'city',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'region' => [
                'name' => 'region',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'address' => [
                'name' => 'address',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'ext_addr' => [
                'name' => 'ext_addr',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'zip' => [
                'name' => 'zip',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'phone' => [
                'name' => 'phone',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'shirt_size' => [
                'name' => 'shirt_size',
                'type' => Type::string(),
                'rules' => ['nullable', 'string', Rule::in(array_column(ShirtSize::cases(), 'name'))]
            ],
            'signature' => [
                'name' => 'signature',
                'type' => GraphQL::type('Upload'),
                'rules' => ['nullable', 'image']
            ]
        ];
    }

    /**
     * Update user
     *
     * @param $root
     * @param $args
     * @param $context
     * @param Request $request
     * @return User|Error
     */
    public function resolve($root, $args, $context, Request $request): User|Error
    {
        // Find model
        $user = User::findOrFail($args['id']);

        // Check the user's role gives allowance to update another user
        if (
            !(auth()->id() === $user->id || auth()->user()->role->level < $user->role->level)
            || isset($args['role_id']) && $this->checkUserRole(Role::find($args['role_id']))
        ) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        DB::beginTransaction();

        try {
            foreach ($args as $key => $val) {
                if (in_array($key, $this->update_fields)) {
                    $user->$key = $val;
                } elseif ($key === 'signature') {
                    $user->signature = $val;
                    $user->signature_ip = $request->ip();
                    $user->signature_date = now();
                }
            }
            // Save user if it was changed
            $user->isDirty() && $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }

        return User::find($user->id);
    }
}