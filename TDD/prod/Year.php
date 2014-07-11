<?php

class Year {
    private $_year;

    public function __construct($value) {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('NAN Year...');
        }

        $this->_year = $value;
    }

    public function getYear() {
        return $this->_year;
    }

    public function isLeapYear() {
        return ($this->_year % 4 == 0 && $this->_year % 100 != 0);
    }

    public function toJpYear() {
        return '平成'.($this->_year - 1988).'年';
    }
}
