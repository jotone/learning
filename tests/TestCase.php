<?php

namespace Tests;

use Faker\{Factory, Generator};
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Faker instance
     *
     * @var Generator
     */
    protected Generator $faker;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->faker = Factory::create();
    }

    /**
     * Get validation rule translation
     *
     * @param string $trans
     * @param string ...$arguments
     * @return string
     */
    protected function lang(string $trans, string ...$arguments): string
    {
        return preg_replace_array('/:[a-z]+/', $arguments, __($trans));
    }
}
