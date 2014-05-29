<?php
require_once('error_message.php');

//チェック関数一回実行分の情報を保持・実行し、ErrorInfoを生成する.
class Check_Function_Data
{
    private $_name;     //Error_InfoのNAMEに対応する項目名
    private $_data;     //入力値
    private $_function; //チェックする関数
    private $_turn;     //チェックする順番,郵便番号の前後が無いなどのエラー重複排除用
    private $_limit;    //閾値や空文字など

    public function __construct($name, $data, $function, $turn, $limit = 0) {
        $this->_name     = $name;
        $this->_data     = $data;
        $this->_function = $function;
        $this->_turn     = $turn;
        $this->_limit    = $limit;
    }

    //チェック関数に配列を渡すと、エラーの場合のみError_Infoのインスタンスを生成、渡された配列に追加
    //引数は　格納されるエラーの配列, 入力値, 要素のname, 閾値などの値
    public static function checkIsNoText(&$error_array, $data, $name) {           //必須入力チェック
        if (empty($data)) {
            array_push($error_array, new Error_Message($name, 'noText', ''));
            return true;
        }
        return false;
    }

    public static function checkIsEmptyValue(&$error_array, $data, $name, $empty_value) {//必須入力チェック(空を表す文字列と一致しないか)
        if ($data == $empty_value) {
            array_push($error_array, new Error_Message($name, 'noChoise', ''));
            return true;
        }
        return false;
    }

    public static function checkIsNoChoise(&$error_array, $data, $name) {      //必須選択チェック
       if (empty($data)) {
           array_push($error_array, new Error_Message($name, 'noChoise', ''));
           return true;
       }
       return false;
    }

    public static function checkIsOverText(&$error_array, $data, $name, $value) {      //字数チェック
        if (mb_strlen($data) > $value) {
            array_push($error_array, new Error_Message($name, 'overText', strval($value)));
            return true;
        }
    }

    public static function checkIsOverLap(&$error_array, $data, $name) {
        $dsn           = 'mysql:dbname=firstDB;host=127.0.0.1';
        $user          = 'root';
        $db_connection = new DB_Connection($dsn,$user);
        $pdo           = $db_connection->getPDO();
        
        try {
            $query = $pdo->prepare("SELECT email FROM account_info WHERE email = :email");

            $email =  getFormatedText($data, 0);
            $query->bindParam(':email', $email);
            $query->execute();
        } catch (Exception $e) {
            printf('Get Data Failed:%s', $e->getMessage());
            exit();
        }

        if (empty($query->fetch())) {
            return false;
        }

        array_push($error_array, new Error_Message($name, 'overLap', ''));
        return true;
    }



    public static function checkIsIllegal(&$error_array, $data, $name) {       //文法チェック
        $pattern = '';

        switch ($name) {
        case 'mail_address'://メールアドレス
            $pattern = '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@[a-zA-Z0-9_-]+([a-zA-Z0-9\._-]+)+$/';
            break;

        case 'post_first'://郵便番号前半
            $pattern = "/[0-9]{3}/";
            break;

        case 'post_last'://郵便番号後半
            $pattern = "/[0-9]{4}/";
            break;

        default:
            return false;
        }

        if (preg_match($pattern, $data) == false) {
            array_push($error_array, new Error_Message($name, 'illegal', ''));
            return true;
        }
        return false;
    }

    //ターンが一致するか
    public function isTurn($turn) {
        return $turn == $this->_turn;
    }

    //turnが_turnと一致するなら、チェックを行いエラーの有無を返す。エラーはerrorArrayにプッシュされる。
    public function check(&$error_array, $turn) {
        $func = $this->_function;

        switch ($this->_function) {
        case 'checkIsNoText':
        case 'checkIsNoChoise':
        case 'checkIsIllegal':
        case 'checkIsOverLap';
            return Check_Function_Data::$func($error_array, $this->_data, $this->_name);

        case 'checkIsOverText':
        case 'checkIsEmptyValue':
            return Check_Function_Data::$func($error_array, $this->_data, $this->_name, $this->_limit);

        default: return false;
        }
    }
}
