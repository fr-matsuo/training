<?php

require_once('DB_connection.php');
require_once('table.php');

function showFormData() {
    $db_user = 'root';
    $db_dsn  = 'mysql:dbname=firstDB;host=127.0.0.1';

    $connection = DB_Connection::getInstance($db_dsn, $db_user);
    $pdo        = $connection->getPDO();
    $query      = getQuery($pdo);

    $record_list = $query->fetchAll(PDO::FETCH_ASSOC);
    $column_list = getColumnNames($query);        //仕様変更の可能性がある関数を使用する実装
    //$column_list = array_keys($record_list[0]); //1行目のレコードが存在し、nullが一つもない場合のみ有効
    $table = new Table($column_list, $record_list);
    $table->construct();
}

function getQuery($pdo) {
    $sql = "
        SELECT
            *
        FROM
            account_info
        ";
    $query = $pdo->prepare($sql);
    $query->execute();

    return $query;
}

function getColumnNames($query) {
    $names     = array();
    $column_num = $query->columnCount();
    
    for ($i = 0; $i < $column_num; $i++) {
        $column_meta = $query->getColumnMeta($i);
        array_push($names, $column_meta['name']);
    }

    return $names;
}

include('show_form.html.php');
