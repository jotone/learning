<?php

namespace App\Http\Controllers\GraphQL\User;

use App\Models\Role;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

abstract class UserMutation extends Mutation
{
    const ACCESS_FORBIDDEN_MESSAGE = 'Operation is forbidden.';

    const STUDENT_LIMIT_MESSAGE = 'Your current plan only supports up to %s students with no free trial funnel. If you want to upgrade your plan please contact <a href="mailto:info@softwarecy.com!">info@softwarecy.com</a>!';

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('User');
    }

    /**
     * Check if the authenticated user is allowed to create a user
     *
     * @param Role $role
     * @return bool
     */
    protected function checkUserRole(Role $role): bool
    {
        return 255 === auth()->user()->role->level || auth()->user()->role->level > $role->level;
    }
}