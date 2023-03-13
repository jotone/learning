<?php

namespace Tests\Feature;

use App\Models\Settings;
use Tests\ApiTestCase;

class SettingsApiTest extends ApiTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        self::$route_prefix = 'api.settings.';
    }

    public function testUpdateSettings()
    {
        $model = Settings::firstWhere(['key' => 'footer_code']);

        $test_value = mt_rand(0, 1000);
        $response = $this
            ->actingAs(self::$actor)->assertModelExists($model)
            ->putJson(route(static::$route_prefix . 'update'), [
                'footer_code' => $test_value
            ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'footer_code',
            'value' => $test_value,
            'data_type' => 'integer'
        ])->assertDatabaseMissing('settings', [
            'key' => 'footer_code',
            'value' => $model->value
        ]);

        $response = $this//->withHeaders(['Authorization' => 'Bearer ' . self::$jwt])
            ->put(route(static::$route_prefix . 'update'), [
                'footer_code' => ''
            ]);

        $this->assertDatabaseHas('settings', [
            'key' => 'footer_code',
            'value' => '',
            'data_type' => 'string'
        ])->assertDatabaseMissing('settings', [
            'key' => 'footer_code',
            'value' => $test_value
        ]);
    }
}