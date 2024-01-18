<?php

namespace Math\Tests;

use Math\Implies\Operator;
use PHPUnit\Framework\TestCase;

class OperatorTest extends TestCase
{
    public function testOperators() {
        $this->assertEquals("^", Operator::getOperatorByValue(1));
        $this->assertEquals("<->", Operator::getOperatorByValue(2));
        $this->assertEquals("->", Operator::getOperatorByValue(3));
        $this->assertEquals("v", Operator::getOperatorByValue(4));
        $this->assertEquals(false, Operator::getOperatorByValue(5));
    }

    /**
     * @throws \Math\Implies\Exceptions\OperatorNotFoundException
     */
    public function testOperatorsResult() {
        $this->assertFalse(Operator::proposition(false, true, 1));
        $this->assertFalse(Operator::proposition(false, true, 2));
        $this->assertFalse(Operator::proposition(true, false, 3));
        $this->assertFalse(Operator::proposition(false, false, 4));
    }
}