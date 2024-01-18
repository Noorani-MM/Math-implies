<?php

use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    public function testIndex() {
        $this->assertEquals("hello", "hello");
    }
}