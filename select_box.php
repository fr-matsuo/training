<?php

class Select_Box {
    private $_name;
    private $_id;
    private $_size;
    private $_value_list;
    private $_default_value;

    function __construct($name, $id, $size, $value_list, $default_value = '') {
        $this->_name       = $name;
        $this->_id         = $id;
        $this->_size       = $size;
        $this->_value_list = $value_list;
        $this->_default_value = $default_value;

        if(empty($this->_default_value)) {
            $this->_default_value = $this->_value_list[0];
        }
    }

    public function construct() {
        printf("<select name='%s' id ='%s' size=%d value='%s'>",
               $this->_name,
               $this->_id,
               $this->_size,
               $this->_default_value);

        foreach($this->_value_list as $val_elm) {
            $selected = ($this->_default_value == $val_elm) ? 'selected' : '';
            printf("<option value='%s' %s>%s</option>", $val_elm, $selected, $val_elm);
        }
        print '</select>';
    }
}
