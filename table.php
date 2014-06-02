<?php

class Table {
    private $_column_list;
    private $_record_list;//カラム*レコード

    function __construct($column_list, $record_list) {
        $this->_column_list = $column_list;
        $this->_record_list = $record_list;
    }

    public function construct() {
        print '<table>';

        print '<tr>';
        foreach ($this->_column_list as $column) {
            printf("<th>%s</th>", $column);
        }
        print '</tr>';

        foreach ($this->_record_list as $record) {
            print '<tr>';
            foreach($record as $data) {
                printf("<td>%s</td>", $data);
            }
        }

        print '</table>';
    }
}
