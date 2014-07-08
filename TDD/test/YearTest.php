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

    public function testToJpYear() {
        $years = array(
            1868 => '明治元年',
            1911 => '明治44年',
            1912 => '大正元年',
            1925 => '大正14年',
            1926 => '昭和元年',
            1988 => '昭和63年',
            1989 => '平成元年'
        );

        foreach ($years as $value => $jp_year) {
            $year = new Year($value);
            $this->assertEquals($year->toJpYear(), $jp_year);
        }
    }
}
