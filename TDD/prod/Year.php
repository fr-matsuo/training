<?php

class Year {
    public function __construct($value) {
        if ($value == '') {
            throw new InvalidArgumentException('NAN Year...');
        }
    }
}
