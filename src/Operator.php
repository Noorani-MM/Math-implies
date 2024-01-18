<?php

namespace Math\Implies;

class Operator
{
    const TYPES = [
        '^'     => 1,
        '<->'   => 2,
        '->'    => 3,
        'v'     => 4,
    ];

    public static function getOperatorByValue(int $value): bool|int|string
    {
        return array_search($value, self::TYPES);
    }
}