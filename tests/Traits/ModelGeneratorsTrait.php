<?php

namespace Tests\Traits;

use App\Models\{Role, User};

trait ModelGeneratorsTrait
{
    /**
     * Create user with the simple role
     *
     * @return User
     */
    protected function generateUser() : User
    {
        $role = $this->getRole();
        return User::factory()->create(['role_id' => $role->id]);
    }

    /**
     * Get or create fake role
     *
     * @return Role
     */
    protected function getRole(): Role
    {
        return Role::where('level', '>', 127)->where('level', '<', 255)->count()
            ? Role::where('level', '>', 127)->where('level', '<', 255)->inRandomOrder()->first()
            : Role::factory()->create();
    }

    /**
     * Get or create fake user
     *
     * @return User
     */
    protected function getUser(): User
    {
        $roles = Role::whereIn('slug', ['coach', 'administrator', 'superuser'])->pluck('id')->toArray();
        return User::whereNotIn('role_id', $roles)->count()
            ? User::whereNotIn('role_id', $roles)->inRandomOrder()->first()
            : $this->generateUser();
    }
}