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

    public function testSetIllegalYear() {
        $this->_testIllegalYearModule('aaa');
    }

    private function _testIllegalYearModule($value) {
        try{
            $year = new Year($value);
        } catch (Exception $expect) {
            return;
        }
        $this->fail('asNormalYearTestIllegalYear');
    }

    public function testLeapYearJudge() {
        $leap_years  = array(4, 1996, 2020);
        $other_years = array(3, 1998, 2000);

        foreach($leap_years as $value) {
            $year = new Year($value);
            $this->assertTrue($year->isLeapYear());
        }
        foreach($other_years as $value) {
            $year = new Year($value);
            $this->assertFalse($year->isLeapYear());
        }
    }
}
