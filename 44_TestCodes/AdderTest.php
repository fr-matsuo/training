<?php
require_once '/usr/local/bin/phpunit';
require_once 'Adder.php';

class AdderTest extends PHPUnit_Framework_TestCase{
    public function testAdd() {
        $x = 1;
        $y = 2;
        $adder = new Adder();
        $this->assertEquals(3, $adder->add($x, $y));
    }
}
