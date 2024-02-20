<?php

namespace App\Traits;

trait EnumTrait
{
    /**
     * Get enum value by its definition
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public static function fromName(string $name): mixed
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return is_numeric($case->value) ? (int)$case->value : $case->value;
            }
        }
        throw new \Exception("Not a valid enum name");
    }

    /**
     * Get enum name by its value
     * @param mixed $val
     * @return null|string
     * @throws \Exception
     */
    public static function fromValue(mixed $val): ?string
    {
        foreach (self::cases() as $case) {
            if ($case->value === $val . '') {
                return $case->name;
            }
        }
        return null;
    }

    /**
     * Retrieve all the names and values
     *
     * @return array
     */
    public static function forSelect(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'name')
        );
    }
}