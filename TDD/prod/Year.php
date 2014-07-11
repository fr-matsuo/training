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
        if ($this->_year < 1911 || $this->_year > 2014) return 'out of term';

        $first_years = array(
            1911 => '大正',
            1926 => '昭和',
            1989 => '平成'
        );

        foreach ($first_years as $year => $era) {
            if ($this->_year == $year) {
                return $era . '元年';
            }
        }

        if ($this->_year <= 1925) return '大正'.($this->_year - 1911).'年';
        if ($this->_year <= 1988) return '昭和'.($this->_year - 1925).'年';
        if ($this->_year >= 1990) return '平成'.($this->_year - 1988).'年';
    }
}
