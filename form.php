<?php
//エラーのメッセージデータを表し、エラーの有無や配列の出力を行う。
//html内では、hasError()でのエラーの有無の確認のみ直接呼び出す
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
        'noText'   => '入力',
        'noChoise' => '選択',
        'overText' => '字以内で入力',
        'illegal'  => '正しく入力'
    );
 
    //エラーが一つでもあるか
    private static $_hasError = false;

    //生成されたエラーのテキストをすべて格納
    private static $_allErrorString = array();

    private $_name;  //エラー項目
    private $_kind;  //エラー内容
    private $_value; //'50'字以内などの値

    public function __construct($errorName, $errorKind, $errorValue) {
        $this->_name  = Error_Message::$_name_list[$errorName];
        $this->_kind  = Error_Message::$_kind_list[$errorKind];
        $this->_value = $errorValue;

        Error_Message::$_hasError = true;

        $this->_addErrorString();
    }
    
    //一つでも生成されたら、エラーがあるのでtrue
    public static function hasError(){ return Error_Message::$_hasError; }

    //全エラーの文字列を取得
    public static function getAllErrorString(){ return Error_Message::$_allErrorString; }

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
        $patter;

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
            array_push($errorArray, new Error_Message($name, 'illegal', ''));
            return true;
        }
        return false;
    }

    //ターンが一致するか
    public function isTurn($turn) {
        return $turn == $this->_turn;
    }

    //turnが_turnと一致するなら、チェックを行いエラーの有無を返す。エラーはerrorArrayにプッシュされる。
    public function check(&$errorArray, $turn) { 
        $func = $this->_function;
        return  Check_Function_Data::$func($errorArray, $this->_data, $this->_name, $this->_value);
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

        //最大turnを検索
        $this->_maxTurn = 0;
        foreach($this->_checkFuncArray as $func) {
            $turn = 0;
            while($func->isTurn($turn) == false) {
                $turn++;
            }

            if($turn > $this->_maxTurn) {$this->_maxTurn = $turn;}
        }
    }

    //この項目についてのエラーをチェックし、配列として返す。
    public function getCheckResult() {
        $checkNum = 0;
        $functions = $this->_checkFuncArray;

        for($turn = 0; $turn <= $this->_maxTurn; $turn++) {               //順番にチェック
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

//SQLの入力に対してエスケープしたものを返す
function getEscapeSQLText($text) {
    $search_chars = array(
        '"' => '&quot;',
        '&' => '&amp;',
        '<' => '&lt;',
        '>' => '&gt;',
        '\'' => '&#39;'
    );

    $replaced=$text;
    foreach($search_chars as $before => $after) {
        $buf      = $replaced;
        $replaced = str_replace($before, $after, $buf);
    }
    return $replaced;
}

//文字列を安全なものに変換したものを返す
function getSecureText($text) {
    $forHtml = htmlspecialchars($text);
    $forSQL  = getEscapeSQLText($forHtml);
    return $forSQL;
}

//最初の字が指定した文字群か
function isMBCharsPosFirst($text, $charArray) {
    foreach($charArray as $char) {
        $pos = mb_strpos($text, $char);
        if($pos === 0) return true;
    }
    return false;
}
//最期の字が指定した文字群か
function isMBCharsPosLast($text, $charArray) {
    foreach($charArray as $char) {
        $pos = mb_strrpos($text, $char);
        if($pos === mb_strlen($text)-1) return true;
    }
    return false;
}
//文字配列orその配列の要素をトリムしたものを返す
function getTrimedText($text) {
    $space = array(' ', '　');

    while(isMBCharsPosFirst()) {
        $text = mb_strlen($text, 1);
    }
    while(isMBCharsPosLast()) {
        $text = mb_strlen($text, 0, mb_strlen($text)-1);
    }

    return $text;
}
 
//文字列をフォーマットしたものを返す
function getFormatedText($text, $index) {
    //これらの関数を上から再帰的に適用する
    $format_functions = array(
        'getTrimedText',
        'getSecureText'
    );

    if($index == count($format_functions)) {
        return $text;
    } else {
        $nextText = $format_functions[$index]($text, $index);
        $index++;
        return getFormatedText($nextText, $index);
    }
}

//入力値をフォーマットしたものを返す
function getFormatedTextArray($textArray) {
    $trimedArray = array();
    $trimChars=' 　';

    //連想配列は、array_mergeを使わないと正しく追加できないらしい?
    foreach ($textArray as $key => $value) {
        if (is_array($value)) {
            $add = array();
            foreach ($value as $arrayKey => $arrayValue) {
                $elem = array($arrayKey => getFormatedText($arrayValue, 0));
                $addBuf = array($key => $elem);
                $add = array_merge($add, $addBuf);
            }
            $trimedArray = array_merge($trimedArray, $add);
        } else {
            $add = array($key => getFormatedText($value, 0));
            $trimedArray = array_merge($trimedArray, $add);
        }
    }
    
    return $trimedArray;
}

//値のエラーをチェックし、エラー一覧を返す
function checkErrors() {
    $formated_post = getFormatedTextArray($_POST);

    //エラーチェックの引数リスト作成
    //引数の配列は、コンストラクタにnewで渡せなかったので別に記述

    $nameCheckFunctions = array(
        new Check_Function_Data('name_first', $formated_post['name_first'], '', 'checkIsNoText', 0),
        new Check_Function_Data('name_last',  $formated_post['name_last'] , '', 'checkIsNoText', 0)
    );
    $nameChecker = new Error_Checker(
        '名前',
        $nameCheckFunctions
    );

    $sexCheckFunctions = array(
        new Check_Function_Data('sex', $formated_post['sex'], '', 'checkIsNoChoise', 0)
    );
    $sexChecker = new Error_Checker(
        '性別',
        $sexCheckFunctions
    );

    $postCheckFunctions = array(
        new Check_Function_Data('post_first', $formated_post['post_first'], '', 'checkIsNoText', 0),
        new Check_Function_Data('post_last',  $formated_post['post_last'],  '', 'checkIsNoText', 1),
        new Check_Function_Data('post_first', $formated_post['post_first'], '', 'checkIsIllegal', 2),
        new Check_Function_Data('post_last',  $formated_post['post_last'],  '', 'checkIsIllegal', 3)
    );
    $postChecker = new Error_Checker(
        '郵便番号',
        $postCheckFunctions
    );

    $prefectureCheckFunctions = array(
        new Check_Function_Data('prefecture', $formated_post['prefecture'], '--', 'checkIsEmptyValue', 0)
    );
    $prefectureChecker = new Error_Checker(
        '都道府県',
        $prefectureCheckFunctions
    );

    $mailAddressCheckFunctions = array(
        new Check_Function_Data('mail_address', $formated_post['mail_address'], '', 'checkIsNoText', 0),
        new Check_Function_Data('mail_address', $formated_post['mail_address'], '', 'checkIsIllegal', 1)
    );
    $mailAddressChecker = new Error_Checker(
        'メールアドレス',
        $mailAddressCheckFunctions
    );

    $hobbyCheckFunctions = array();
    if (in_array('その他',$formated_post['hobby'])) {
        array_push(
            $hobbyCheckFunctions,
            new Check_Function_Data('other_descript', $formated_post['other_descript'], '', 'checkIsNoText', 0)
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
  //何も入力されてないなら、その他のチェックを外す
  function checkOther() {
      var hobby = document.getElementById('other_descript');
      if (hobby.value.length == 0) { document.getElementById('other').checked=false; }
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
    <input type="text" name="other_descript" id="other_descript" value="<?php print $_POST['other_descript']; ?>" 
      onClick="document.getElementById('other').checked = true;" onBlur="checkOther()">
    <br>

    <label>ご意見</label>
    <input type="text" id="opinion" name="opinion" value="<?php print $_POST['opinion']; ?>">

    <input type="submit" value="確認" onClick=>
    </fieldset>
  </form>
  <form method="post" name="checkForm" action="formCheck.php">
  <?php
  $formated_post = getFormatedTextArray($_POST);

  $NAMES = array(
      'name_first', 'name_last', 'sex', 'post_first', 'post_last',
      'prefecture','mail_address', 'other_descript', 'opinion'
  );

  foreach($NAMES as $name) {
      printf("<input type='hidden' name='%s' value='%s'>", $name, $formated_post[$name]);
  }

  if(empty($checkList) == false) {
      foreach ($checkList as $checked) {
          printf("<input type='hidden' name='hobby[]' value='%s'>", $checked);
      }
  }
  showError();
  ?>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
