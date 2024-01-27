<?php

namespace Math\Tests;

use Math\Implies\Implies;
use PHPUnit\Framework\TestCase;

class ImpliesTest extends TestCase
{
    public function testPrefix() {
        $case1 = new Implies('((p->q)^!q)->r');
        $except1 = 'pq3~q1r3';

        $case2 = new Implies('(p->q)^(!q->r)');
        $except2 = 'pq3~qr31';

        $this->assertEquals($except1, $case1->prefix());
        $this->assertEquals($except2, $case2->prefix());
        $this->assertNotEquals($except1, $case2->prefix());
        $this->assertNotEquals($except2, $case1->prefix());
    }

    /**
     * @throws \Math\Implies\Exceptions\StackException
     * @throws \Math\Implies\Exceptions\OperatorNotFoundException
     */
    public function testRows() {
        $case1 = new Implies('(p->q)^(!q->r)');
        $table1 =  ["0001100","0011111","0100111","0110111","1001000","1011010","1100111","1110111",];

        $case2 = new Implies('p->q');
        $table2 =  ["001","011","100","111"];

        $case3 = new Implies('p->!q');
        $table3 =  ["0011","0101","1011","1100"];

        $this->assertEquals($table1, $case1->rows);
        $this->assertEquals($table2, $case2->rows);
        $this->assertEquals($table3, $case3->rows);
    }
}