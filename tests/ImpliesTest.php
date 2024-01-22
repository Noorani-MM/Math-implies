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

    public function testTable() {
        $case1 = new Implies('(p->q)^(!q->r)');
        $table1 =  ["0001001","0011111","0101110","0111110","1000001","1010101","1101110","1111110"];

        $case2 = new Implies('p->q');
        $table2 =  ["001","011","100","111"];

        $case3 = new Implies('p->!q');
        $table3 =  ["0011", "0110", "1011", "1100"];

        $this->assertEquals($table1, $case1->table());
        $this->assertEquals($table2, $case2->table());
        $this->assertEquals($table3, $case3->table());
    }
}