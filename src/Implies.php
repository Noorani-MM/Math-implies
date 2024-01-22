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
                    $this->words[] = $char;
                    if ($i > 0 && $sentence[$i - 1] !== '~') {
                        $this->propositions[] = $char;
                    }
                }
            }
            elseif ($char === '~') {
                $char = "~{$sentence[$i+1]}";
                if (!in_array($char, $this->propositions)) {
                    $this->propositions[] = $char;
                }
            }
            elseif (in_array($char, Operator::TYPES)) {
                $this->operators++;
            }
        }
    }

    /**
     * @throws Exceptions\StackException
     */
    public function prefix(): string
    {
        $stack = new Stack();
        $data = "";
        $chars = str_split($this->sentence);

        for ($i = 0; $i < count($chars); $i++) {
            $char = $chars[$i];
            if ($char === '(' || preg_match('/\d/', $char)) {
                # posh {$char} and TopOfStack is : {$stack->topOfStack()}<br />
                $stack->push($char);
            }
            elseif (preg_match('/[a-zA-Z]/', $char) && $i > 0 && $chars[$i-1] != '~') {
                # append to {$data}<br />
                $data .= $char;
            }
            elseif (preg_match('/[a-zA-Z]/', $char) || ($i > 0 && $chars[$i-1] == '~')) {
                $data .= "~{$char}";
            }
            elseif ($char === ')') {
                # pop {$pop} and TopOfStack is : {$stack->topOfStack()}<br />
                do {
                    $pop = $stack->pop();
                } while ($pop === '(');
                $data .= $pop;
            }
        }
        while ($stack->topOfStack() > -1) {
            $pop = $stack->pop();
            if ($pop === '(') {
                continue;
            }
            $data .= $pop;
        }

        return $data;
    }
}
