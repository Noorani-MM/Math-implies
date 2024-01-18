<?php

namespace Math\Implies;

use Math\Implies\Exceptions\OperatorNotFoundException;

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

    /**
     * @throws OperatorNotFoundException
     */
    public static function proposition(bool $prior, bool $q, string|int $operator): bool
    {
        if (is_int($operator)) {
            $operator = self::getOperatorByValue($operator);
        }
        if (! array_key_exists($operator, self::TYPES)) {
            throw new OperatorNotFoundException("Operator not defined !");
        }

        return match ($operator) {
            '^'     => ($prior && $q) === true,
            '<->'   => $prior === $q,
            '->'    => $prior <= $q,
            'v'     => $prior || $q,
            default => false,
        };
    }
}