<?php

class Year {
    private $_year;

    private $_jp_eras = array(
        1868 => '明治',
        1912 => '大正',
        1926 => '昭和',
        1989 => '平成',
        2014 => 'now'   //期間内判定に現在の年が必要
    );

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
        $start_years = array_keys($this->_jp_eras);

        for ($i = 0; $i < count($start_years); $i++) {
             $start_era = $this->_jp_eras[$start_years[$i]];
             $next_era  = $this->_jp_eras[$start_years[$i + 1]];

             if ($start_years[$i]     <= $this->_year
             &&  $start_years[$i + 1] >  $this->_year) {
                 return $this->_createJpYear($start_era, $start_years[$i]);
             }
        }

        return 'out of term'.$this->_year; //対象期間外
    }

    private function _createJpYear($era, $start_year) {
        $year       = $this->_year - $start_year +1;
        if ($year == 1) $year = '元';

        return $era.$year.'年';
    }
}
