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
}
