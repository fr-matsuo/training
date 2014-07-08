<?php

require_once dirname(__FILE__).'/../prod/Year.php';

class YearTest extends PHPUnit_Framework_TestCase {
    public function testSetYear() {
        $value = 2000;
        $year = new Year($value);
        $this->assertEquals($year->getYear(), $value);
    }
}
