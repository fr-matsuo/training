<?php
//エラーのメッセージデータを表し、エラーの有無や配列の出力を行う。
//html内では、hasError()でのエラーの有無の確認のみ直接呼び出す
class Error_Message
{
    //エラー項目の名前
    private static $_NAME = array(
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
    private static $_KIND = array(
        'noText'   => '入力',
        'noChoise' => '選択',
        'overText' => '字以内で入力',
        'illegal'  => '正しく入力'
    );
 
    //エラーが一つでもあるか
    private static $_hasError = false;

    //生成されたエラーのテキストをすべて格納
    private static $_allErrorString = array();

    //一つでも生成されたら、エラーがあるのでtrue
    public static function hasError(){ return Error_Message::$_hasError; }

    //全エラーの文字列を取得
    public static function getAllErrorString(){ return Error_Message::$_allErrorString; }

    private $_name;  //エラー項目
    private $_kind;  //エラー内容
    private $_value; //'50'字以内などの値

    public function __construct($errorName, $errorKind, $errorValue) {
        $this->_name  = Error_Message::$_NAME[$errorName];
        $this->_kind  = Error_Message::$_KIND[$errorKind];
        $this->_value = $errorValue;

        Error_Message::$_hasError = true;

        $this->_addErrorString();
    }
    
    //このエラーの表示を一覧に追加
    private function _addErrorString() {
        $valueText = (empty($this->_value)) ? (string)$this->_value : '';
        $text      = sprintf('%sを%s%sしてください。', $this->_name, $valueText, $this->_kind);
        array_push(Error_Message::$_allErrorString, $text);
    }
}

//チェック関数一回実行分の情報を保持・実行し、ErrorInfoを生成する.
//html内では直接呼ばない
class Check_Function_Data
{
    //チェック関数に配列を渡すと、エラーの場合のみError_Infoのインスタンスを生成、渡された配列に追加
    //引数は　格納されるエラーの配列, 入力値, 要素のname, 閾値などの値
    public static function checkIsNoText(&$errorArray, $data, $name, $dummy) {           //必須入力チェック
        if (empty($data)) {
            array_push($errorArray, new Error_Message($name, 'noText', ''));
            return true;
        }
        return false;
    }
    public static function checkIsEmptyValue(&$errorArray, $data, $name, $emptyValue) {//必須入力チェック(空を表す文字列と一致しないか)
        if ($data == $emptyValue) {
            array_push($errorArray, new Error_Message($name, 'noText', ''));
            return true;
        }
        return false;
    }
    public static function checkIsNoChoise(&$errorArray, $data, $name, $dummy) {      //必須選択チェック
       if (empty($data)) {
           array_push($errorArray, new Error_Message($name, 'noChoise', ''));
           return true;
       }
       return false;
    }
    public static function checkIsOverText(&$errorArray, $data, $name, $value) {      //字数チェック
        if (mb_strlen($data) > $value) {
            array_push($errorArray, new Error_Message($name, 'overText', $value));
            return true;
        }
        return false;
    }
    public static function checkIsIllegal(&$errorArray, $data, $name, $dummy) {       //文法チェック
        switch ($name) {
        case 'mail_address'://メールアドレス
            $pattern = '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@[a-zA-Z0-9_-]+([a-zA-Z0-9\._-]+)+$/';
            if (preg_match($pattern, $data) == false) {
                array_push($errorArray, new Error_Message($name, 'illegal', ''));
                return true;
            }
            return false;

        default:
            return false;
        }
    }

    private $_name;     //Error_InfoのNAMEに対応する項目名
    private $_data;     //入力値
    private $_value;    //閾値や空文字など
    private $_function; //チェックする関数
    private $_turn;     //チェックする順番,郵便番号の前後が無いなどのエラー重複排除用
    
    public function __construct($name, $data, $value, $function, $turn) {
        $this->_name     = $name;
        $this->_data     = $data;
        $this->_value    = $value;
        $this->_function = $function;
        $this->_turn     = $turn;
    }

    //ターンが一致するか
    public function isTurn($turn) {
        return $turn == $this->_turn;
    }

    //turnが_turnと一致するなら、チェックを行いエラーの有無を返す。エラーはerrorArrayにプッシュされる。
    public function check(&$errorArray, $turn) { 
        $func = $this->_function;
        return  Check_Function_Data::$func($errorArray, $this->_data, $this->_name, $this->_value);
        // return  Check_Function_Data::$this->_function($errorArray, $this->_data, $this->_name, $this->_value);
        //とは書けないので,一旦funcに代入
    }
}

//名前や性別など、一つの項目についてのエラーチェックを行う。
//checkErrors()から呼ばれるgetCheckResult()によりエラー一覧の配列を取得する
class Error_Checker
{
    private $_showName;            //項目名　名前　性別　など
    private $_errorArray;          //エラー一覧
    private $_checkFuncArray;      //チェック一回分の情報　を複数持つ配列
    private $_maxTurn;             //最遅チェック順

    public function __construct($showName, $checkFuncArray) {
        $this->_showName       = $showName;
        $this->_checkFuncArray = $checkFuncArray;
        $this->_errorArray = array();

        $_maxTurn = 0;
        foreach($this->_checkFuncArray as $func){
            $turn = 0;
            while($func->isTurn($turn) == false) {
                $turn ++;
            }

            if($turn > $this->_maxTurn) $this->_maxTurn = $turn;
        }
    }

    //この項目についてのエラーをチェックし、配列として返す。
    public function getCheckResult() {
        $checkNum = 0;
        $functions = $this->_checkFuncArray;

        for($turn = 0; $turn <= $this-_maxturn; $turn++) {               //順番にチェック
            $endFlag = false;
            foreach ($functions as $funcData) {
                if($funcData->isTurn($turn) == false) continue;

                $isError = $funcData->check($this->_errorArray, $turn);
                unset($funcData);

                if ($isError || empty($functions)) {  //チェックを実行してエラーがあるor全てチェックした
                    $endFlag = true;
                }
            }
            if($endFlag) break;
        }
        return $this->_errorArray;
    }
}

//文字配列orその配列の要素をトリムしたものを返す
function getTrimedTextArray($textArray) {
    $trimedArray = array();

    //連想配列は、array_mergeを使わないと正しく追加できないらしい?
    foreach ($textArray as $key => $value) {
        if (is_array($value)) {
            $add = array();
            foreach ($value as $arrayKey => $arrayValue) {
                $elem = array($arrayKey => $arrayValue);
                $addBuf = array($key => $elem);
                $add = array_merge($add, $addBuf);
            }
            $trimedArray = array_merge($trimedArray, $add);
        } else {
            $add = array($key => $value);
            $trimedArray = array_merge($trimedArray, $add);
        }
    }
    
    return $trimedArray;
}

//値のエラーをチェックし、エラー一覧を返す
function checkErrors() {
    $TRIMED_POST = getTrimedTextArray($_POST);
    //if(empty($TRIMED_POST)) return null;

    //エラーチェックの引数リスト作成
    //引数の配列は、コンストラクタにnewで渡せなかったので別に記述

    $nameCheckFunctions = array(
        new Check_Function_Data('name_first', $TRIMED_POST['name_first'], '', 'checkIsNoText', 0),
        new Check_Function_Data('name_last',  $TRIMED_POST['name_last'] , '', 'checkIsNoText', 0)
    );
    $nameChecker = new Error_Checker(
        '名前',
        $nameCheckFunctions
    );

    $sexCheckFunctions = array(
        new Check_Function_Data('sex', $TRIMED_POST['sex'], '', 'checkIsNoChoise', 0)
    );
    $sexChecker = new Error_Checker(
        '性別',
        $sexCheckFunctions
    );

    $postCheckFunctions = array(
        new Check_Function_Data('post_first', $TRIMED_POST['post_first'], '', 'checkIsNoText', 0),
        new Check_Function_Data('post_last',  $TRIMED_POST['post_last'],  '', 'checkIsNoText', 1)
    );
    $postChecker = new Error_Checker(
        '郵便番号',
        $postCheckFunctions
    );

    $prefectureCheckFunctions = array(
        new Check_Function_Data('prefecture', $TRIMED_POST['prefecture'], '--', 'checkIsEmptyValue', 0)
    );
    $prefectureChecker = new Error_Checker(
        '都道府県',
        $prefectureCheckFunctions
    );

    $mailAddressCheckFunctions = array(
        new Check_Function_Data('mail_address', $TRIMED_POST['mail_address'], '', 'checkIsNoText', 0),
        new Check_Function_Data('mail_address', $TRIMED_POST['mail_address'], '', 'checkIsIllegal', 1)
    );
    $mailAddressChecker = new Error_Checker(
        'メールアドレス',
        $mailAddressCheckFunctions
    );

    $hobbyCheckFunctions = array();
    if (in_array('その他',$TRIMED_POST['hobby'])) {
        array_push(
            $hobbyCheckFunctions,
            new Check_Function_Data('other_descript', $TRIMED_POST['other_descript'], '', 'checkIsNoText', 0)
        );
    }
    $hobbyChecker = new Error_Checker(
        '趣味',
        $hobbyCheckFunctions
    );

    //foreachで回すために上記を格納
    $checkers = array(
        $nameChecker,
        $sexChecker,
        $postChecker,
        $prefectureChecker,
        $mailAddressChecker,
        $hobbyChecker
    );

    //エラー一覧を取得
    $errors = array();
    foreach ($checkers as $checker) {
        array_push($errors, $checker->getCheckResult());
    }

    return $errors;
}

//次のページに行けるならジャンプする関数。入力をform.phpに戻し、エラーがないならformCheck.phpへジャンプ
//bodyの宣言で呼び出し
function checkJump() {
    if (empty($_POST) || isset($_POST['return'])) {return;}
    
    checkErrors();
    if(Error_Message::hasError() == false ) {
        print "onLoad='document.checkForm.submit();'";
    }      
}
//エラーがあればエラー一覧、なけければ送信用ダミーボタンを表示
//初回は処理を飛ばしたいので関数化
function showError() {
    if(empty($_POST) || isset($_POST['return'])) return;

    if(Error_Message::hasError()) {
        $errorTexts = Error_Message::getAllErrorString();
        foreach ($errorTexts as $error) {
            printf("%s<br>", $error);
        }
    } else {
        print "<input type='submit' value='dummy'>";
    }
}
?>
<!DOCTYPE html>

<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" type="text/css" href="common.css">
  <link rel="stylesheet" type="text/css" href="form.css">
  <title>フォーム画面</title>  

  <script type="text/javascript">
  <!--
  function checkOther(){
      var otherHobby = document.getElementById('other').value;
      document.getElementById('other').checked = (otherHobby != '');
  }
  -->
  </script>
</head>

<body <?php checkJump(); ?> >
<header>
<h1>フォーム>入力</h1>
</header>

<section>
  <form method="post" name="form" action="form.php">
    <fieldset>
    <legend>フォーム</legend>

    <label>名前:</label>
    <input type="text" name="name_first" id="name_first" value="<?php print $_POST['name_first']; ?>">
    <input type="text" name="name_last" id="name_last" value="<?php print $_POST['name_last']; ?>">
    <br> 
    
    <label>性別:</label>
      <?php
      $manChecked   = ($_POST['sex'] == "男性")? "checked" : "";
      $womanChecked = ($_POST['sex'] == "女性")? "checked" : "";
      ?>
    <input type="radio" name="sex" id="man" value="男性" <?php print $manChecked; ?>><label for="man">男性</label>
    <input type="radio" name="sex" id="woman" value="女性" <?php print $womanChecked; ?>><label for="woman">女性</label>
    <br>

    <label>郵便番号:</label>
    <input type="text" name="post_first" id="post_first" maxlength="3" value="<?php print $_POST['post_first']; ?>">
    -
    <input type="text" name="post_last" id="post_last" maxlength="4" value="<?php print $_POST['post_last']; ?>">
    <br>

    <label>都道府県:</label>

    <select name="prefecture" id="prefecture" size=1 value="<?php print $_POST['prefecture']; ?>">
    <?php
    $PREFECTURES = array(
        '--',
        '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
        '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
        '新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県',
        '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
        '鳥取県','島根県','岡山県','広島県','山口県',
        '徳島県','香川県','愛媛県','高知県',
        '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'
    );

    foreach ($PREFECTURES as $elm) {
        $selected = ($_POST['prefecture'] == $elm) ? 'selected' : '';
        printf("<option value='%s' %s>%s</option>", $elm, $selected, $elm);
    }
    ?>
    </select>
    <br>
    
    <label>メールアドレス:</label>
    <input type="text" name="mail_address" id="mail" value="<?php print $_POST['mail_address']; ?>">
    <br>

    <label>趣味</label>
    <?php
    $HOBBYS = array('music' => '音楽鑑賞','movie' => '映画鑑賞','other' => 'その他');

    $checkList = $_POST['hobby'];

    foreach ($HOBBYS as $key => $elm) {
        $checked = (in_array($elm, $checkList)) ? 'checked' : '';
        printf("<input type='checkbox' id='%s' name='hobby[]' value='%s' %s><label for='%s'>%s</label>",
                      $key, $elm, $checked, $key, $elm);
    }
    ?>
    <input type="text" name="other_descript" id="other_descript" value="<?php print $_POST['other_descript']; ?>" onChange="checkOther();">
    <br>

    <label>ご意見</label>
    <input type="text" id="opinion" name="opinion" value="<?php print $_POST['opinion']; ?>">

    <input type="submit" value="確認" onClick=>
    </fieldset>
  </form>
  <form method="post" name="checkForm" action="formCheck.php">
    <input type='hidden' name='name_first'     value="<?php printf('%s', $_POST['name_first']);   ?>">
    <input type='hidden' name='name_last'      value="<?php printf('%s', $_POST['name_last']);    ?>">
    <input type='hidden' name='sex'            value="<?php printf('%s', $_POST['sex']);          ?>">
    <input type='hidden' name='post_first'     value="<?php printf('%s', $_POST['post_first']);   ?>">
    <input type='hidden' name='post_last'      value="<?php printf('%s', $_POST['post_last']);    ?>">
    <input type='hidden' name='prefecture'     value="<?php printf('%s', $_POST['prefecture']);   ?>">
    <input type='hidden' name='mail_address'   value="<?php printf('%s', $_POST['mail_address']); ?>">
    <?php
        if(empty($checkList) == false) {
            foreach ($checkList as $checked) {
                printf("<input type='hidden' name='hobby[]' value='%s'>", $checked);
            }            
        }
    ?> 
    <input type='hidden' name='other_descript' value="<?php printf('%s', $_POST['other_descript']); ?>">
    <input type='hidden' name='opinion'        value="<?php printf('%s', $_POST['opinion']);        ?>">

    <?php
        showError();
    ?>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
