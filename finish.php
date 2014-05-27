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

    function &getPDOReference() {
        return $this->_pdo;
    }
}

function sendPOST2DB() {
    $dsn = 'mysql:dbname=firstDB;host=127.0.0.1';
    $user = 'root';
    $db_connection = new DB_Connection($dsn,$user);
    $pdo = $db_connection->getPDOReference();

    //登録
    try {
        sendDBModule($pdo);
    } catch (PDOException $e) {
        print('Add failed:'.$e->getMessage());
        var_dump($e->getMessage());
    }
}

function sendDBModule($pdo) {
    $query = $pdo->prepare("
        INSERT INTO account_info(
            first_name,
            last_name,
            email,
            pref_id,
            created_at,
            updated_at
        ) VALUES (
            :first_name,
            :last_name,
            :email,
            :pref_id,
            NOW(),
            NOW()
        )
        ");

    $first_name = $_POST['name_first'];
    $last_name  = $_POST['name_last'];
    $email      = $_POST['mail_address'];
    $pref_id    = getPrefectureID($pdo);

    $query->bindParam(':first_name', $first_name);
    $query->bindParam(':last_name' , $last_name);
    $query->bindParam(':email'     , $email);
    $query->bindParam(':pref_id'   , $pref_id);

    $result = $query->execute();
}

function getPrefectureID($pdo) {
    $sql = "
        SELECT
            pref_id
        FROM
            prefecture_info
        WHERE
            pref_name = :pref_name
        ";
    $query = $pdo->prepare($sql);

    $pref_name = $_POST['prefecture'];
    $query->bindParam(':pref_name', $pref_name);
    $query->execute();

    $pref_id = intval($query->fetch()[0]);
    $query->closeCursor();

    return $pref_id;
}

sendPOST2DB();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" type="text/css" href="common.css">
  <link rel="stylesheet" type="text/css" href="finish.css">
  <title>完了</title>
</head>

<body>
  <header>
    <h1>フォーム>完了</h1>
  </header>

  <nav>
    応募しました
    <p><a href="index.php">TOPページへ</a><p>
  </nav>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
