<?php

class DB_Connection {
    private $_pdo;

    function __construct($dsn, $user, $pass = '') {
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

    function __destruct() {
        $this->_pdo = null;
    }

    function getPDOReference() {
        return $this->_pdo;
    }
}

