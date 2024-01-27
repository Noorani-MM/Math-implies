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
    public array $columns;

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

    public function columns(): array
    {
        if (isset($this->columns)) {
            return $this->columns;
        }
        $stack = new Stack();
        $prefix = str_split($this->prefix);

        foreach ($this->words as $word) {
            $this->columns[] = $word;
        }
        foreach ($this->negatives as $negative) {
            $this->columns[] = "~{$negative}";
        }
        foreach ($prefix as $index => $char) {
            if ($char === '~') continue;
            if (preg_match('/[a-zA-Z]/', $char)) {
                if ($index > 0 && $prefix[$index - 1] == '~') {
                    $stack->push("~{$char}");
                }
                else {
                    $stack->push($char);
                }
            }
            elseif (preg_match('/\d/', $char)) {
                $item1 = $stack->pop();
                $item2 = $stack->pop();
                $char = str_replace(Operator::TYPES, array_keys(Operator::TYPES), $char);
                $sentence = "({$item2} {$char} {$item1})";
                $this->columns[] = $sentence;
                $stack->push($sentence);
            }
        }

        while ($stack->topOfStack() !== -1) {
            $pop = $stack->pop();
            if (!in_array($pop, $this->columns)) {
                $this->columns[] = $pop;
            }
        }

        return $this->columns;
    }

    public function minterm(): string
    {
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
        // Sorted by priority Step 1
        $this->words_compiler();
        $this->prefix();

        // Step 2
        $this->rows();
        $this->columns();

        // Step 3
        $this->minterm();
        $this->maxterm();

        // Step 4
        $this->pdnf();
        $this->pcnf();
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

    public function pdnf(): string
    {
        if (isset($this->pdnf)) {
            return join('v', $this->pdnf);
        }
        $words = $this->words;
        $count = count($words);

        foreach ($this->minterm as $item) {
            $value = Binary::getbinary($item);
            $value = str_pad($value, $count, '0', STR_PAD_LEFT);
            $value = str_split($value);
            $row = [];

            foreach ($value as $key => $v) {
                $row[] = $v === "1" ? $this->words[$key] : '~'.$this->words[$key];
            }
            $row = join('^', $row);
            $this->pdnf[] = "({$row})";
        }
        return join('v', $this->pdnf);
    }

    public function pcnf(): string
    {
        if (isset($this->pcnf)) {
            return join('v', $this->pcnf);
        }
        $words = $this->words;
        $count = count($words);

        foreach ($this->maxterm as $item) {
            $value = Binary::getbinary($item);
            $value = str_pad($value, $count, '0', STR_PAD_LEFT);
            $value = str_split($value);
            $row = [];

            foreach ($value as $key => $v) {
                $row[] = $v === "1" ? $this->words[$key] : '~'.$this->words[$key];
            }
            $row = join('v', $row);
            $this->pcnf[] = "({$row})";
        }
        return join('^', $this->pcnf);
    }
}
