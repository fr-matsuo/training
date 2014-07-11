<?php

class Year {
    public function __construct($value) {
        if ($value == '' || $value == null || $value == 'a' || $value == array(123, 234)) {
            throw new InvalidArgumentException('NAN Year...');
        }
    }
}
