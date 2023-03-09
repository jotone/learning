<?php

namespace Tests\Feature;

use App\Models\{Role, User};
use Illuminate\Support\Arr;
use Tests\ApiTestCase;
use Tests\Traits\ModelGeneratorsTrait;

class UserApiTest extends ApiTestCase
{
    use ModelGeneratorsTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $superuser = Role::firstWhere(['level' => 0]);

        self::$class = User::class;
        self::$route_prefix = 'api.users.';
        self::$uri_request = '&where_not[role_id]=' . $superuser->id;
    }

    /**
     * Test user list
     *
     * @return void
     */
    public function testUserList(): void
    {
        $this->runListTest(function ($content, $models) {
            $item = Arr::random($content->data);
            $this
                // Check total items equals database records
                ->assertDatabaseCount($models[0]->getTable(), $content->meta->total)
                // Check response random item exists on the database
                ->assertDatabaseHas($models[0]->getTable(), [
                    'id'         => $item->id,
                    'first_name' => $item->first_name ?? '',
                    'last_name'  => empty($item->last_name) ? null : $item->last_name,
                    'email'      => $item->email
                ])
                // Check per_page value equals response collection
                ->assertCount($content->meta->per_page, $content->data);
        });
    }

    /**
     * Test user show
     *
     * @return void
     */
    public function testUserShow(): void
    {
        $this->runShowTest($this->getUser(), [
            'id',
            'first_name',
            'last_name',
            'email'
        ]);
    }
}