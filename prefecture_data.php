<?php

require_once('DB_connection.php');
require_once('select_box.php');

//非汎用的実装
class Prefecture_Data {
    private static $_PREFECTURES = array(0 => '--');
    private static $_isLoaded = false;
    
    //DBのdsn・userを設定
    private static $_dsn  = 'mysql:dbname=firstDB;host=127.0.0.1';
    private static $_user = 'root';

    private static function _loadPrefectures() {
        $connection = DB_Connection::getInstance(self::$_dsn, self::$_user);
        $pdo        = $connection->getPDO();

        $sql = "
            SELECT
                pref_id, pref_name
            FROM
                prefecture_info
            ";

        $query = $pdo->prepare($sql);
        $query->execute();

        self::_createPrefectureList($query);

        self::$_isLoaded = true;
    }

    //ロード用モジュール
    private static function _createPrefectureList($query) {
        $record;
        while(($record = $query->fetch()) != false) {
            $pref_id   = intval($record[0]);
            $pref_name = $record[1];

            $add = array($pref_id => $pref_name);
            self::$_PREFECTURES += $add;
        }
    }

    //htmlの要素のパラメータを設定
    public static function constructSelectBox($value) {
        if(self::$_isLoaded == false) self::_loadPrefectures();
        
        $name  = 'prefecture';
        $id    = 'prefecture';
        $size  = 1;

        $prefecture_box = new Select_Box($name, $id, $size, self::$_PREFECTURES, $value);
        $prefecture_box->construct($value);
    }

    public static function getPrefectureID($pref_name) {
        if(self::$_isLoaded == false) self::_loadPrefectures();
        
        $pref_id = array_keys(self::$_PREFECTURES, $pref_name)[0];

        return $pref_id;
    }
}
