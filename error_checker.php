<?php 
class Error_Message
{
    //エラー項目の名前
    private static $_name_list = array(
         'name_first'     => '姓',
         'name_last'      => '名',
         'sex'            => '性別',
         'post_first'     => '郵便番号',
         'post_last'      => '郵便番号',
         'prefecture'     => '都道府県',
         'mail_address'   => 'メールアドレス',
         'other_descript' => 'その他の詳細'
    );
    //エラーの内容
    private static $_kind_list = array(
        'noText'    => '入力',
        'noChoise'  => '選択',
        'overText'  => '字以内で入力',
        'illegal'   => '正しく入力',
        'overLap'   => '変更'

    );

    //エラーが一つでもあるか
    private static $_has_error = false;

    //生成されたエラーのテキストをすべて格納
    private static $_all_error_string = array();

    private $_name;  //エラー項目
    private $_kind;  //エラー内容
    private $_value; //'50'字以内などの値

    public function __construct($error_name, $error_kind, $error_value) {
        $this->_name  = Error_Message::$_name_list[$error_name];
        $this->_kind  = Error_Message::$_kind_list[$error_kind];
        $this->_value = $error_value;

        Error_Message::$_has_error = true;

        $this->_addErrorString();
    }

    //一つでも生成されたら、エラーがあるのでtrue
    public static function hasError() { return Error_Message::$_has_error; }

    //全エラーの文字列を取得
    public static function getAllErrorString() { return Error_Message::$_all_error_string; }

    //このエラーの表示を一覧に追加
    private function _addErrorString() {
        $text = sprintf('%sを%s%sしてください。', $this->_name, $this->_value, $this->_kind);
        array_push(Error_Message::$_all_error_string, $text);
    }
}


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
        $dsn  = 'mysql:dbname=firstDB;host=127.0.0.1';
        $user = 'root';
        $pdo  = null;

        try {
            $pdo = new PDO($dsn, $user);
        } catch (PDOException $e) {
            printf('Connection Failed:%s', $e->getMessage());
            exit();
            return false;
        }

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

//名前や性別など、一つの項目についてのエラーチェックを行う。
class Error_Checker
{
    private $_show_name;            //項目名　名前　性別　など
    private $_error_array;          //エラー一覧
    private $_check_func_array;      //チェック一回分の情報　を複数持つ配列
    private $_max_turn;             //最遅チェック順

    public function __construct($show_name, $check_func_array) {
        $this->_show_name        = $show_name;
        $this->_check_func_array = $check_func_array;
        $this->_error_array = array();

        //最大turnを検索
        $this->_max_turn = 0;
        foreach ($this->_check_func_array as $func) {
            $turn = 0;
            while ($func->isTurn($turn) == false) {
                $turn++;
            }

            if ($turn > $this->_max_turn) $this->_max_turn = $turn;
        }
    }

    //この項目についてのエラーをチェックし、配列として返す。
    public function getCheckResult() {
        $functions = $this->_check_func_array;

        for ($turn = 0; $turn <= $this->_max_turn; $turn++) {//実行順番毎にチェック
            $end_flag = false;
            foreach ($functions as $func_data) {
                if ($func_data->isTurn($turn) == false) continue;

                $is_error = $func_data->check($this->_error_array, $turn);
                unset($func_data);

                if ($is_error || empty($functions)) {  //チェックを実行してエラーがあるor全てチェックした
                    $end_flag = true;
                }
            }
            if ($end_flag) break;
        }
        return $this->_error_array;
    }
}
