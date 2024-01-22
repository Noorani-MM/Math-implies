<?php

namespace Math\Implies;

class Binary
{
    public static function getbinary(int $number): string
    {
        $result = "";
        while (($number / 2) >= 1) {
            $result .= $number % 2;
            $number = $number / 2;
        }
        $result .= floor($number);

        return strrev($result);
    }

    public static function getnumber(string $binary): int {
        $binary = strrev($binary);
        $length = strlen($binary);
        $result = 0;
        for ($i = 0; $i < $length; $i++) {
            $result = $binary[$i] === "1" ? $result + 2**$i : $result;
        }

        return $result;
    }

    public static function binariesTillNumber(int $max, array $list = []): array
    {
        if (count($list) === $max + 1) {
            return $list;
        }
        $currentNumber = count($list);
        $binary = self::getbinary($currentNumber);
        $length = 2;
        while (2**$length <= $max) {
            $length++;
        }
        $list[] = str_pad($binary, $length, '0', STR_PAD_LEFT);

        return self::binariesTillNumber($max, $list);
    }
}