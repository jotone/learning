<?php

namespace Tests\Feature;

use App\Models\Role;
use Tests\TestCase;
use Tests\Traits\ModelGeneratorsTrait;

class AuthApiTest extends TestCase
{
    use ModelGeneratorsTrait;

    /**
     * Test access to the login, dashboard and homepage access
     * @return void
     */
    public function testPagesAccess(): void
    {
        // Assert the login page is accessible
        $this->get(route('auth.index'))->assertOk();
        // Assert the dashboard is not reachable
        $this->get(route('dashboard.index'))->assertRedirect(route('auth.index'));
        // Assert the home page is not reachable
        $this->get(route('home.index'))->assertRedirect(route('auth.index'));
    }

    /**
     * Test to log in as a student
     *
     * @return void
     */
    public function testStudentLoginRequest(): void
    {
        $user = $this->getUser();
        $user->update([
            'role_id'  => Role::where('slug', 'student')->value('id'),
            'password' => 'password'
        ]);

        // Login as student. Check the user has been redirected to the home page
        $this->post(route('auth.login'), [
            'email'    => $user->email,
            'password' => 'password'
        ])->assertRedirect(route('home.index'));
    }

    /**
     * Test to log in as an admin user
     *
     * @return void
     */
    public function testAdminLoginRequest(): void
    {
        $user = $this->getUser();
        $user->update([
            'role_id'  => Role::whereIn('slug', ['admin', 'coach'])->inRandomOrder()->value('id'),
            'password' => 'password'
        ]);

        // Login as admin. Check the user has been redirected to the dashboard page
        $this->post(route('auth.login'), [
            'email'    => $user->email,
            'password' => 'password'
        ])->assertRedirect(route('dashboard.index'));
    }

    /**
     * @return void
     */
    public function testAccessApiWithoutToken(): void
    {
        // Access api without token
        $this->getJson(route('api.users.index'))->assertUnauthorized();
        // Access without Content-Type: application/json header
        $this->get(route('api.users.index'))->assertRedirect(route('auth.index'));
    }
}