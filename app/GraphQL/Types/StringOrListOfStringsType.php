<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ScalarType;
use GraphQL\Language\AST\StringValueNode;

class StringOrListOfStringsType extends ScalarType
{
    public string $name = 'StringOrListOfStrings';

    public static function toType(): self
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    public function serialize($value)
    {
        // Serialize the value when sending it to the client
        return $value;
    }

    public function parseValue($value)
    {
        // Parse the value when receiving it from the client
        return is_array($value) ? $value : [$value];
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        // Parse the literal value from GraphQL query
        if (!$valueNode instanceof StringValueNode) {
            throw new \GraphQL\Error\Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
        }
        return $valueNode->value;
    }
}