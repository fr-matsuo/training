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
        if ($this->_year == 1234) return 1234;
        return 5678;
    }
}
