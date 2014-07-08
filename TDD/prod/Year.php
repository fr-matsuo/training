<?php

class Year {
    private $_year;

    public  function __construct($year) {
        if (!is_numeric($year)) throw new Exception('Year is NAN...');

        $this->_year = $year;
    }

    public function getYear(){
        return $this->_year;
    }
}
