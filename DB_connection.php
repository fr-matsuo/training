<?php

class DB_Connection {
    private static $_instance_list = array();
 
    private $_pdo;

    private function __construct($dsn, $user, $pass = '') {
        try {
            if (empty($pass)) {
                $this->_pdo = new PDO($dsn, $user);
            } else {
                $this->_pdo = new PDO($dsn, $user, $pass);
            }
        } catch (PDOException $e) {
            printf("Connection failed:%s", $e->getMessage());
        }
    }

    public static function getInstance($dsn, $user, $pass) {
        if (empty(self::$_instance_list[$dsn][$user])) {
            self::$_instance_list[$dsn][$user] = new self($dsn, $user, $pass);
        }
        return self::$_instance_list[$dsn][$user];
    }

    function __destruct() {
        $this->_pdo = null;
    }

    function getPDO() {
        return $this->_pdo;
    }
}
