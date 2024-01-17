<?php

namespace App\Http\Controllers\GraphQL\User;

use App\Enums\ShirtSize;
use App\Models\{Role, Settings, User};
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class MutationStore extends UserMutation
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
            'first_name' => [
                'name' => 'first_name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string']
            ],
            'last_name' => [
                'name' => 'last_name',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'email', 'unique:users,email']
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
            ]
        ];
    }

    /**
     * Store user
     *
     * @param $root
     * @param $args
     * @return User|Error
     */
    public function resolve($root, $args): User|Error
    {
        // Check server student limit
        $students_limit = Settings::where('key', 'students_max_count')->value('value');
        if ($this->checkStudentLimit($students_limit)) {
            return new Error(sprintf(self::STUDENT_LIMIT_MESSAGE, $students_limit));
        }
        // Get user role model
        if (isset($args['role_id'])) {
            $role = Role::find($args['role_id']);
        } else {
            $role = Role::firstWhere('slug', 'student');
            $args['role_id'] = $role->id;
        }
        // Check if user model can be created
        if ($this->checkUserRole($role)) {
            return new Error(self::ACCESS_FORBIDDEN_MESSAGE);
        }

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create($args);

            if (!empty($user->password)) {
                $user->status = 'active';
                $user->activated_at = now();
                $user->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }

        return $user;
    }

    /**
     * Check students number limit. Returns true if limit was achieved
     *
     * @param int $students_limit
     * @return bool
     */
    protected function checkStudentLimit(int $students_limit): bool
    {
        return $students_limit > 0 && User::students()->count() >= $students_limit;
    }
}