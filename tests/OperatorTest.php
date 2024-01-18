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
}