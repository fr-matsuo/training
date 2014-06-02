<?php

require_once('DB_connection.php');
require_once('table.php');

define('PAGE_RECORD_NUM', 10);

$record_list;
$column_list;

$page_num = isset($_POST['page_num']) ? $_POST['page_num'] : 1;


function setFormData() {
    $db_user = 'root';
    $db_dsn  = 'mysql:dbname=firstDB;host=127.0.0.1';

    $connection = DB_Connection::getInstance($db_dsn, $db_user);
    $pdo        = $connection->getPDO();
    $query      = getQuery($pdo);

    global $record_list;
    global $column_list;
    $record_list = $query->fetchAll(PDO::FETCH_ASSOC);
    $column_list = getColumnNames($query);        //仕様変更の可能性がある関数を使用する実装
    //$column_list = array_keys($record_list[0]); //1行目のレコードが存在し、nullが一つもない場合のみ有効
}

function showTable($column_list, $record_list, $page_num) {
    $show_records = array();

    $record_num = count($record_list);
    $first      = ($page_num - 1) * PAGE_RECORD_NUM;
    $last       = $first + PAGE_RECORD_NUM - 1;

    for ($i = $first; $i <= $last; $i++) {
        if ($i >= $record_num) break;

        array_push($show_records, $record_list[$i]);
    }

    $table = new Table($column_list, $show_records);
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
        $column_meta = $query->getColumnMeta($i); //仕様変更の可能性あり
        array_push($names, $column_meta['name']);
    }

    return $names;
}

function showPagingNavi($record_list) {
    $record_num = count($record_list);
    if ($record_num < PAGE_RECORD_NUM) return;

    $page_num   = intval(ceil($record_num / PAGE_RECORD_NUM));

    for ($i = 1; $i <= $page_num; $i++) {
        printf("
            <a href='#'
            onClick='jumpPage(%d);'>%d</a>",
            $i, $i);
        print ' ';
    }
}

setFormData();
include('show_form.html.php');
