<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Arr;
use LaravelLang\Publisher\Facades\Helpers\Locales as LocaleHelper;
use Tests\TestCase;

class LanguageApiTest extends TestCase
{
    /**
     * User with access to api
     *
     * @var User
     */
    protected static User $actor;

    protected function setUp(): void
    {
        parent::setUp();

        self::$actor = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')->firstWhere('roles.slug', 'superuser');
    }

    /**
     * Test getting the language file content
     *
     * @return void
     */
    public function testGetTranslationsList()
    {
        $folder = Arr::random(glob(lang_path('/*'), GLOB_ONLYDIR));

        $file = pathinfo(Arr::random(glob($folder . '/*.php')), PATHINFO_FILENAME);

        $lang = substr($folder, strrpos($folder, '/') + 1);

        $this->actingAs(self::$actor)
            ->get(route('api.language.show', [$lang, $file]))
            ->assertOk()
            ->assertJsonStructure([
                'list',
                'origin'
            ]);
    }

    /**
     * Language installation test
     *
     * @return void
     */
    public function testInstallLanguage(): void
    {
        $lang_to_install = Arr::random(LocaleHelper::available());

        $response = $this->actingAs(self::$actor)
            ->postJson(route('api.language.store', ['lang' => $lang_to_install]))
            ->assertCreated();

        $content = json_decode($response->content());
        $this->assertTrue($lang_to_install == $content);

        $this->assertTrue(is_dir(lang_path($lang_to_install)));
        $this->assertTrue(file_exists(lang_path($lang_to_install) . '/auth.php'));
        $this->assertTrue(file_exists(lang_path($lang_to_install) . '/pagination.php'));
        $this->assertTrue(file_exists(lang_path($lang_to_install) . '/passwords.php'));
        $this->assertTrue(file_exists(lang_path($lang_to_install) . '/validation.php'));
    }

    /**
     * Test updating the language file content
     *
     * @return void
     */
    public function testUpdateTranslation()
    {
        $path = lang_path('en/auth.php');
        $origin_translations = require $path;

        $key = Arr::random(array_keys($origin_translations));
        $value = $this->faker->text(50);

        $this->actingAs(self::$actor)
            ->patch(route('api.language.update'), [
                'lang' => 'en',
                'file' => 'auth',
                'key' => $key,
                'value' => $value
            ])
            ->assertOk();

        $this->assertStringContainsString($value, file_get_contents($path));
    }

    /**
     * Language remove test
     *
     * @return void
     */
    public function testRemoveLanguage(): void
    {
        $lang_to_remove = Arr::random(array_values(array_map(
            fn ($item) => str_replace(lang_path() . '/', '', $item),
            array_filter(glob(lang_path() . '/*', GLOB_ONLYDIR), fn($item) => $item !== lang_path('en'))
        )));

        $this->actingAs(self::$actor)
            ->delete(route('api.language.destroy', $lang_to_remove))
            ->assertNoContent();

        $this->assertTrue(!is_dir(lang_path($lang_to_remove)));
    }
}