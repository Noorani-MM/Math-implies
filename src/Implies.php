<?php

namespace Math\Implies;

class Implies
{
    protected int $operators = 0;
    protected array $propositions = [], $words = [];

    public function __construct(protected string $sentence)
    {
        $this->sentence = self::sentence_convertor($this->sentence);
        $this->detector();
    }

    public static function sentence_convertor(string $sentence): string
    {
        $sentence = str_replace(' ', '', $sentence);
        $sentence = str_replace('.', '', $sentence);
        $sentence = str_replace('!', '~', $sentence);
        return str_replace(array_keys(Operator::TYPES), Operator::TYPES, $sentence);
    }

    private function detector() {
        $sentence = $this->sentence;

        for ($i = 0; $i < strlen($sentence); $i++) {
            $char = $sentence[$i];
            if (preg_match('/[a-zA-Z]/', $char)) {
                if (!array_key_exists($char, $this->words)) {
                    $this->words[$char] = false;
                    $this->propositions[] = $char;
                }
            }
            elseif ($char === "~") {
                $char = $sentence[$i+1];
                $this->words[$char] = true;
                if (!in_array("~{$char}", $this->propositions)) {
                    $this->propositions[] = "~{$char}";
                }
            }
            elseif (in_array($char, Operator::TYPES)) {
                $this->operators++;
            }
        }
    }
}
