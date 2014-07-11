<?php

require_once dirname(__FILE__).'/../prod/Year.php';

class YearTest extends PHPUnit_Framework_TestCase {
    /**
    * @dataProvider constructYearProvider
    */
    public function testConstructYear($value) {
        try {
            $year = new Year($value);
        } catch (Exception $error) {
            $this->fail('Fail construct Year');
        }
    }

    public function constructYearProvider() {
        return array(
            array(1234),
        );
    }

    /**
    * @dataProvider exceptionConstructYearProvider
    * @expectedException InvalidArgumentException
    */
    public function testExceptionConstructYear($value) {
        $year = new Year($value);
    }

    public function exceptionConstructYearProvider() {
        return array(
            array(''),
            array(null),
            array('a'),
            array(array(123,234))
        );
    }
    
    /**
    * @dataProvider getYearProvider
    */
    public function testGetYear($value) {
        $year = new Year($value);
        $this->assertEquals($year->getYear(), $value);
    }
    
    public function getYearProvider() {
        return array(
            array(1234),
            array(5678),
            array(9012)
        );
    }

    /**
    * @dataProvider isLeapYearProvider
    */
    public function testIsLeapYear($value) {
        $year = new Year($value);
        $this->assertTrue($year->isLeapYear());
    }
    
    public function isLeapYearProvider() {
        return array(
            array(3456),
            array(9876),
            array(5432)
        );
    }

    /**
    * @dataProvider isNotLeapYearProvider
    */
    public function testIsNotLeapYear($value) {
        $year = new Year($value);
        $this->assertFalse($year->isLeapYear());
    }
    
    public function isNotLeapYearProvider() {
        return array(
            array(1111),
            array(1234),
            array(7654),
            array(100),
            array(1500),
            array(2000)
        );
    }

    /**
    * @dataProvider jpYearProvider
    */
    public function testJpYear($value, $jp_year) {
        $year = new Year($value);
        $this->assertEquals($year->toJpYear(), $jp_year);
    }

    public function jpYearProvider() {
        return array(
            array(2014, '平成26年'),
            array(2002, '平成14年'),
            array(1993, '平成5年'),
            array(1989, '平成元年'),
            array(1988, '昭和63年'),
            array(1970, '昭和45年'),
            array(1926, '昭和元年'),
            array(1925, '大正14年'),
            array(1916, '大正5年'),
            array(1911, '大正元年')
        );
    }
}
