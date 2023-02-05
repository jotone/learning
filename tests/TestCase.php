<?php

namespace Tests;

use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function __construct(?string $name = null, array $data = [], $dataName = '', protected $faker = null)
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = Faker::create();
    }
}
