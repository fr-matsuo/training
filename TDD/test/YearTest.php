<?php

require_once dirname(__FILE__).'/../prod/Year.php';

class YearTest extends PHPUnit_Framework_TestCase {
    public function testSetYear() {
        $this->_testSetYearModule(2000);
        $this->_testSetYearModule(2001);

    }
    private function _testSetYearModule($value) {
        $year = new Year($value);
        $this->assertEquals($year->getYear(), $value);
    }
}
