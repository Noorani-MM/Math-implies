<?php

namespace Math\Implies;

class Implies
{
    public function __construct(protected string $sentence)
    {
        $this->sentence = self::sentence_convertor($this->sentence);
    }

    public static function sentence_convertor(string $sentence): string
    {
        $sentence = str_replace(' ', '', $sentence);
        $sentence = str_replace('.', '', $sentence);
        return str_replace(array_keys(Operator::TYPES), Operator::TYPES, $sentence);
    }
}
