<?php

namespace Math\Implies;

class Implies
{
    protected int $operator, $words, $proposition;
    protected array $propositionList, $wordList;

    public function __construct(protected string $sentence)
    {
        $this->sentence = self::sentence_convertor($this->sentence);
        $this->detector();
    }

    public static function sentence_convertor(string $sentence): string
    {
        $sentence = str_replace(' ', '', $sentence);
        $sentence = str_replace('.', '', $sentence);
        $sentence = str_replace('~', '!', $sentence);
        return str_replace(array_keys(Operator::TYPES), Operator::TYPES, $sentence);
    }

    private function detector() {
        $sentence = $this->sentence;
        $wordsList = [];
        $propositionList = [];

        for ($i =0; $i < strlen($sentence); $i++) {
            $char = $sentence[$i];
            if (preg_match('/[a-zA-z]', $char)) {
                if (!in_array($char, $wordsList)) {
                    $wordsList[] = $char;
                    $propositionList[] = $char;
                }
            }
            elseif ($char === "!") {
                $char = '!'.$sentence[$i+1];
                if (!in_array($char, $propositionList)) {
                    $propositionList[] = $char;
                }
            }
            elseif (preg_match('/\d', $char)) {
                $this->operator++;
            }
        }
        $this->words = count($wordsList);
        $this->proposition = count($propositionList);
        $this->wordList = $wordsList;
        $this->propositionList = $propositionList;
    }
}
