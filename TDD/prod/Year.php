<?php

class Year {
    public function __construct($value) {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('NAN Year...');
        }
    }
}
