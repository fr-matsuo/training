<?php

require_once('DB_connection.php');

function sendPOST2DB($post_data) {
    $dsn = 'mysql:dbname=firstDB;host=127.0.0.1';
    $user = 'root';
    $db_connection = DB_Connection::getInstance($dsn,$user);
    $pdo = $db_connection->getPDO();

    //登録
    try {
        sendDBModule($pdo, $post_data);
    } catch (PDOException $e) {
        print('Add failed:'.$e->getMessage());
        var_dump($e->getMessage());
    }
}

function sendDBModule($pdo, $post_data) {
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

    $first_name = $post_data['name_first'];
    $last_name  = $post_data['name_last'];
    $email      = $post_data['mail_address'];
    $pref_id    = getPrefectureID($pdo, $post_data['prefecture']);

    $query->bindParam(':first_name', $first_name);
    $query->bindParam(':last_name' , $last_name);
    $query->bindParam(':email'     , $email);
    $query->bindParam(':pref_id'   , $pref_id);

    $result = $query->execute();
}

function getPrefectureID($pdo, $pref_name) {
    $sql = "
        SELECT
            pref_id
        FROM
            prefecture_info
        WHERE
            pref_name = :pref_name
        ";
    $query = $pdo->prepare($sql);

    $query->bindParam(':pref_name', $pref_name);
    $query->execute();

    $pref_id = intval($query->fetch()[0]);
    $query->closeCursor();

    return $pref_id;
}

sendPOST2DB($_POST);

include('finish.html.php');
