<?php

require_once('DB_connection.php');
require_once('table.php');

define('PAGE_RECORD_NUM', 10);

$column_list = null;
$record_list = null;
$record_num  = 0;

$page_num = isset($_POST['page_num']) ? $_POST['page_num'] : 1;

function setDBData(&$column_list, &$record_list, &$record_num, $page_num) {
    $db_user = 'root';
    $db_dsn  = 'mysql:dbname=firstDB;host=127.0.0.1';

    $connection = DB_Connection::getInstance($db_dsn, $db_user);
    $pdo        = $connection->getPDO();
    
    setFormData($column_list, $record_list, $page_num, $pdo);
    $record_num = getRecordNum($pdo);
}

function setFormData(&$column_list, &$record_list, $page_num, $pdo) {
    $start = ($page_num - 1) * PAGE_RECORD_NUM;
    $sql   = sprintf("
        SELECT *
        FROM   account_info
        LIMIT  %d,%d ",
        $start, PAGE_RECORD_NUM);
    $query = getQuery($sql, $pdo);

    $record_list = $query->fetchAll(PDO::FETCH_ASSOC);
    $column_list = getColumnNames($query);        //仕様変更の可能性がある関数を使用する実装
    //$column_list = array_keys($record_list[0]); //1行目のレコードが存在し、nullが一つもない場合のみ有効
}

function getRecordNum($pdo) {
    $sql   = "
        SELECT COUNT(*)
        FROM   account_info
        ";
    $query = getQuery($sql, $pdo);

    return intval($query->fetch()[0]);
}

function showTable($column_list, $record_list ,$page_num) {
    $table = new Table($column_list, $record_list);
    $table->construct();
}

function getQuery($sql, $pdo) {
    $query = $pdo->prepare($sql);
    $query->execute();

    return $query;
}

function getColumnNames($query) {
    $names      = array();
    $column_num = $query->columnCount();
    
    for ($i = 0; $i < $column_num; $i++) {
        $column_meta = $query->getColumnMeta($i); //仕様変更の可能性あり
        array_push($names, $column_meta['name']);
    }

    return $names;
}

function showPagingNavi($record_num) {
    if ($record_num < PAGE_RECORD_NUM) return;

    $page_num = intval(ceil($record_num / PAGE_RECORD_NUM));

    for ($i = 1; $i <= $page_num; $i++) {
        printf("
            <a href='#'
            onClick='jumpPage(%d);'>%d</a>",
            $i, $i);
        print ' ';
    }
}

setDBData($column_list, $record_list, $record_num, $page_num);
include('show_form.html.php');
