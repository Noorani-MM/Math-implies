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
    public static function proposition(bool $prior, bool $q, string $operator): bool
    {
        if (preg_match('/\d/', $operator)) {
            $operator = self::getOperatorByValue($operator);
        }
        if (! in_array($operator, array_keys(self::TYPES))) {
            throw new OperatorNotFoundException("Operator: {$operator} not defined !");
        }

        return match ($operator) {
            '^'     => ($prior && $q) === true,
            '<->'   => $prior === $q,
            '->'    => $prior <= $q,
            'v'     => $prior || $q,
        };
    }
}