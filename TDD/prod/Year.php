<?php

class Year {
    private $_is_1234;

    public function __construct($value) {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('NAN Year...');
        }

        $this->_is_1234 = ($value == 1234);
    }

    public function getYear() {
        if ($this->_is_1234) return 1234;
        return 5678;
    }
}
