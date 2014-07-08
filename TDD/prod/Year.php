<?php

class Year {
    private $_year;

    public  function __construct($year) {
        if (!is_numeric($year)) throw new Exception('Year is NAN...');

        $this->_year = $year;
    }

    public function getYear() {
        return $this->_year;
    }

    public function isLeapYear() {
        return $this->_year % 4 === 0 && $this->_year % 100 !== 0;
    }

    public function toJpYear() {
        return '';
    }
}
