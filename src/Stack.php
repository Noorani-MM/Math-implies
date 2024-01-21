<?php

namespace Math\Implies;

use Math\Implies\Exceptions\StackException;

class Stack
{
    private array $_stack;
    private int $_topOfStack = -1;

    public function push(string $item) {
        $this->_stack[] = $item;
        $this->_topOfStack++;
    }

    /**
     * @throws StackException
     */
    public function pop(): string {
        if ($this->_topOfStack === -1) {
            throw new StackException('Stack underflow: Stack is empty !');
        }
        $result = $this->_stack[$this->_topOfStack];
        unset($this->_stack[$this->_topOfStack--]);
        return $result;
    }

    public function isEmpty(): bool
    {
        return $this->_topOfStack === -1;
    }

    public function topOfStack(): int
    {
        return $this->_topOfStack;
    }

    public function clear() {
        $this->_topOfStack = -1;
        $this->_stack = [];
    }
}