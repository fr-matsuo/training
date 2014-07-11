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
            array(-1234),
            array(''),
            array('aaaaa'),
            array(null)
        );
    }
}
