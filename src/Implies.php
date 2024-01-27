<?php

namespace Math\Implies;

class Implies
{
    /**
     * @var int $operators This is count of operators in the sentence
     */
    protected int $operators = 0;

    /**
     * @var array $negatives List of negative words
     */
    protected array $negatives = [];

    /**
     * @var array $words List of available words
     */
    protected array $words = [];

    /**
     * @var array $rows of the table
     */
    protected array $rows;

    /**
     * @var array $columns what column exists in the table
     */
    protected array $columns;

    /**
     * @var array $minterm List of numbers in the table when final result is True
     */
    public array $minterm;

    /**
     * @var array $maxterm List of numbers in the table when final result is False
     */
    public array $maxterm;

    /**
     * @var array $pdnf
     */
    public array $pdnf;

    /**
     * @var array $pcnf
     */
    public array $pcnf;

    /**
     * @var string $prefix of the sentence to calculate content
     */
    protected string $prefix;

    /**
     * @throws Exceptions\StackException
     */
    public function __construct(protected string $sentence)
    {
        $this->sentence = self::sentence_convertor($this->sentence);
        $this->compiler();
        $this->minterm();
        $this->maxterm();
    }

    public static function sentence_convertor(string $sentence): string
    {
        $sentence = str_replace(' ', '', $sentence);
        $sentence = str_replace('.', '', $sentence);
        $sentence = str_replace('!', '~', $sentence);
        return str_replace(array_keys(Operator::TYPES), Operator::TYPES, $sentence);
    }

    /**
     * @throws Exceptions\StackException
     */
    public function prefix(): string
    {
        if (isset($this->prefix)) {
            return $this->prefix;
        }
        $stack = new Stack();
        $data = "";
        $chars = str_split($this->sentence);

        for ($i = 0; $i < count($chars); $i++) {
            $char = $chars[$i];
            if ($char === '(' || preg_match('/\d/', $char)) {
                $stack->push($char);
            }
            elseif (preg_match('/[a-zA-Z]/', $char)) {
                if ($i > 0 && $chars[$i-1] === '~')
                    $data .= "~{$char}";
                else
                    $data .= $char;
            }
            elseif ($char === ')') {
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

        $this->prefix = $data;

        return $data;
    }

    /**
     * @throws Exceptions\StackException
     */
    public function rows(): array
    {
        if (isset($this->rows)) {
            return $this->rows;
        }
        $binaries = Binary::binariesTillNumber(2** count($this->words)-1);
        $result = [];
        $prefix = $this->prefix;
        $stack = new Stack();

        foreach ($binaries as $binary) {
            $binary_chars = str_split($binary);

            $binary_chars = $this->negatives_in_row($binary_chars);

            foreach (str_split($prefix) as $index => $c) {
                if ($c === '~') continue;
                if (preg_match('/[a-zA-Z]/', $c)) {
                    if ($index > 0 && $prefix[$index - 1] == '~') {
                        $stack->push("~{$c}");
                    }
                    else {
                        $stack->push($c);
                    }
                }
                else {
                    $c2 = $stack->pop();
                    $c1 = $stack->pop();

                    $word1 = $this->getIndexOfWord(str_replace('~', '', $c1));
                    $word2 = $this->getIndexOfWord(str_replace('~', '', $c2));

                    $bin1 = $binary_chars[$word1] == $this->checkIsNegative($c1) ? "0" : "1";
                    $bin2 = $binary_chars[$word2] == $this->checkIsNegative($c2) ? "0" : "1";

                    $res = Operator::proposition($bin1, $bin2, $c);

                   $binary_chars[] = strval(intval($res));
                   $stack->push(count($binary_chars) - 1);
                }
            }
            $result[] = implode('', $binary_chars);
        }

        $this->rows = $result;

        return $result;
    }

    public function minterm(): string
    {
        if (!isset($this->rows)) {
            $this->rows();
        }
        if (isset($this->minterm)) {
            $minterm = implode(',', $this->minterm);
            return "Σ($minterm)";
        }
        $minterm = [];

        $cursor = strlen($this->rows[0]);

        foreach ($this->rows as $row) {
            if ($row[$cursor - 1] === "1") {
                $str_split = str_split($row);
                $fields = array_splice($str_split, 0, count($this->words));
                $minterm[] = Binary::getnumber(implode('', $fields));
            }
        }
        $this->minterm = $minterm;

        $minterm = implode( ',', $minterm);
        return "Σ($minterm)";
    }

    public function maxterm(): string
    {
        if (!isset($this->rows)) {
            $this->rows();
        }
        if (isset($this->maxterm)) {
            $maxterm = implode(',', $this->maxterm);
            return "π($maxterm)";
        }
        $maxterm = [];

        $cursor = strlen($this->rows[0]);

        foreach ($this->rows as $row) {
            if ($row[$cursor - 1] === "0") {
                $str_split = str_split($row);
                $fields = array_splice($str_split, 0, count($this->words));
                $maxterm[] = Binary::getnumber(implode('', $fields));
            }
        }
        $this->maxterm = $maxterm;

        $maxterm = implode(',', $maxterm);
        return "π($maxterm)";
    }

    protected function getIndexOfWord(string|int $item): bool|int|string
    {
        if (preg_match('/\d/', $item)) {
            return intval($item);
        }
        return array_search($item, $this->words);
    }

    protected function checkIsNegative(string $item): bool
    {
        return str_contains($item, '~');
    }

    private function negatives_in_row(array $binary_chars): array
    {
        // Add negatives
        foreach ($this->negatives as $negative) {
            $wordIndex = $this->getIndexOfWord($negative);
            $res = $binary_chars[$wordIndex] === "0";
            $binary_chars[] = intval($res);
        }
        return $binary_chars;
    }

    /**
     * Compile sentence to generate content and throw exception if it's wrong!
     * @throws Exceptions\StackException
     */
    private function compiler() {
        $this->words_compiler();
        $this->prefix();
    }

    private function words_compiler() {
        $sentence = $this->sentence;

        for ($i = 0; $i < strlen($sentence); $i++) {
            $char = $sentence[$i];
            if ($char === '~') continue;
            if (preg_match('/[a-zA-Z]/', $char)) {
                if (!in_array($char, $this->words)) {
                    $this->words[] = $char;
                }
                if ($i > 0 && $sentence[$i - 1] === '~') {
                    if (!in_array($char, $this->negatives)) {
                        $this->negatives[] = $char;
                    }
                }

            }
            elseif (in_array($char, Operator::TYPES)) {
                $this->operators++;
            }
        }
    }
}
