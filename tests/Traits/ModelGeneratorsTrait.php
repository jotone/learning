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
        return Role::where('level', '>', 127)->count()
            ? Role::where('level', '>', 127)->inRandomOrder()->first()
            : Role::factory()->create();
    }
}