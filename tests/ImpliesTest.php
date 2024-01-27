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

        $this->assertEquals($except1, $case1->prefix);
        $this->assertEquals($except2, $case2->prefix);
        $this->assertNotEquals($except1, $case2->prefix);
        $this->assertNotEquals($except2, $case1->prefix);
    }

    /**
     * @throws \Math\Implies\Exceptions\StackException
     * @throws \Math\Implies\Exceptions\OperatorNotFoundException
     */
    public function testRows() {
        $case1 = new Implies('(p->q)^(!q->r)');
        $case2 = new Implies('p->q');
        $case3 = new Implies('p->!q');
        $case4 = new Implies('(p->q)^(p<->r)');
        $case5 = new Implies('((pvq)->~r)');

        $assert1 =  ["0001100","0011111","0100111","0110111","1001000","1011010","1100111","1110111"];
        $assert2 =  ["001","011","100","111"];
        $assert3 =  ["0011","0101","1011","1100"];
        $assert4 = ["000111","001100","010111","011100","100000","101010","110100","111111"];
        $assert5 = ["000101","001001","010111","011010","100111","101010","110111","111010"];

        $this->assertEquals($assert1, $case1->rows);
        $this->assertEquals($assert2, $case2->rows);
        $this->assertEquals($assert3, $case3->rows);
        $this->assertEquals($assert4, $case4->rows);
        $this->assertEquals($assert5, $case5->rows);
    }

    public function testPDNF() {
        $case1 = new Implies('(p->q)^(!q->r)');
        $case2 = new Implies('p->q');
        $case3 = new Implies('p->!q');
        $case4 = new Implies('(p->q)^(p<->r)');
        $case5 = new Implies('((pvq)->~r)');

        $assert1 = '(~p^~q^r)v(~p^q^~r)v(~p^q^r)v(p^q^~r)v(p^q^r)';
        $assert2 = '(~p^~q)v(~p^q)v(p^q)';
        $assert3 = '(~p^~q)v(~p^q)v(p^~q)';
        $assert4 = '(~p^~q^~r)v(~p^q^~r)v(p^q^r)';
        $assert5 = '(~p^~q^~r)v(~p^~q^r)v(~p^q^~r)v(p^~q^~r)v(p^q^~r)';

        $this->assertEquals($assert1, $case1->pdnf());
        $this->assertEquals($assert2, $case2->pdnf());
        $this->assertEquals($assert3, $case3->pdnf());
        $this->assertEquals($assert4, $case4->pdnf());
        $this->assertEquals($assert5, $case5->pdnf());
    }

    public function testPCNF() {
        $case1 = new Implies('(p->q)^(!q->r)');
        $case2 = new Implies('p->q');
        $case3 = new Implies('p->!q');
        $case4 = new Implies('(p->q)^(p<->r)');
        $case5 = new Implies('((pvq)->~r)');

        $assert1 = '(~pv~qv~r)v(pv~qv~r)v(pv~qvr)';
        $assert2 = '(pv~q)';
        $assert3 = '(pvq)';
        $assert4 = '(~pv~qvr)v(~pvqvr)v(pv~qv~r)v(pv~qvr)v(pvqv~r)';
        $assert5 = '(~pvqvr)v(pv~qvr)v(pvqvr)';

        $this->assertEquals($assert1, $case1->pcnf());
        $this->assertEquals($assert2, $case2->pcnf());
        $this->assertEquals($assert3, $case3->pcnf());
        $this->assertEquals($assert4, $case4->pcnf());
        $this->assertEquals($assert5, $case5->pcnf());
    }

    public function testMinterm() {
        $case1 = new Implies('(p->q)^(!q->r)');
        $case2 = new Implies('p->q');
        $case3 = new Implies('p->!q');
        $case4 = new Implies('(p->q)^(p<->r)');
        $case5 = new Implies('((pvq)->~r)');

        $assert1 = 'Σ(1,2,3,6,7)';
        $assert2 = 'Σ(0,1,3)';
        $assert3 = 'Σ(0,1,2)';
        $assert4 = 'Σ(0,2,7)';
        $assert5 = 'Σ(0,1,2,4,6)';

        $this->assertEquals($assert1, $case1->minterm());
        $this->assertEquals($assert2, $case2->minterm());
        $this->assertEquals($assert3, $case3->minterm());
        $this->assertEquals($assert4, $case4->minterm());
        $this->assertEquals($assert5, $case5->minterm());
    }

    public function testMaxterm() {
        $case1 = new Implies('(p->q)^(!q->r)');
        $case2 = new Implies('p->q');
        $case3 = new Implies('p->!q');
        $case4 = new Implies('(p->q)^(p<->r)');
        $case5 = new Implies('((pvq)->~r)');

        $assert1 = 'π(0,4,5)';
        $assert2 = 'π(2)';
        $assert3 = 'π(3)';
        $assert4 = 'π(1,3,4,5,6)';
        $assert5 = 'π(3,5,7)';

        $this->assertEquals($assert1, $case1->maxterm());
        $this->assertEquals($assert2, $case2->maxterm());
        $this->assertEquals($assert3, $case3->maxterm());
        $this->assertEquals($assert4, $case4->maxterm());
        $this->assertEquals($assert5, $case5->maxterm());
    }
}