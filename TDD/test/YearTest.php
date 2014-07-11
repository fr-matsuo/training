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
            array(1234)
        );
    }
}
