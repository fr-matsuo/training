<?php

class Year {
    private $_year;

    public  function __construct($year) {
        $this->_year = $year;
    }

    public function getYear(){
        return $this->_year;
    }
}
