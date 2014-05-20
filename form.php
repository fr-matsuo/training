<?php
//エラーのメッセージデータを表し、エラーの有無や配列の出力を行う。
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
        return false;
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

//文字列を安全なものに変換したものを返す
function getSecureText($text) {
    return htmlspecialchars($text, ENT_QUOTES);
}

//最初の字が指定した文字群か
function isMBCharsPosFirst($text, $char_array) {
    foreach ($char_array as $char) {
        $pos = mb_strpos($text, $char);
        if ($pos === 0) return true;
    }
    return false;
}

//最期の字が指定した文字群か
function isMBCharsPosLast($text, $char_array) {
    foreach ($char_array as $char) {
        $pos = mb_strrpos($text, $char);
        if ($pos === mb_strlen($text) - 1) return true;
    }
    return false;
}

//文字配列orその配列の要素をトリムしたものを返す
function getTrimedText($text) {
    $space_list = array(' ', '　');

    while (isMBCharsPosFirst($text, $space_list)) {
        $text = mb_substr($text, 1);
    }
    while (isMBCharsPosLast($text, $space_list)) {
        $text = mb_substr($text, 0, mb_strlen($text)-1);
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

    if ($index == count($format_functions)) {
        return $text;
    } else {
        $next_text = $format_functions[$index]($text);
        $index++;
        return getFormatedText($next_text, $index);
    }
}

//入力値をフォーマットしたものを返す
function getFormatedTextArray($text_array) {
    $formated_array = array();

    foreach ($text_array as $key => $value) {
        if (is_array($value)) {
            $add = array();
            foreach ($value as $array_key => $array_value) {
                $add += array($array_key => getFormatedText($array_value, 0));
            }
            $formated_array += array($key => $add);
        } else {
            $formated_array += array($key => getFormatedText($value, 0));
        }
    }
    
    return $formated_array;
}

//値のエラーをチェックし、エラー一覧を返す
function checkErrors() {
    $formated_post = getFormatedTextArray($_POST);

    //エラーチェックの引数リスト作成

    $name_check_functions = array(
        new Check_Function_Data('name_first', $formated_post['name_first'], 'checkIsNoText', 0),
        new Check_Function_Data('name_last',  $formated_post['name_last'] , 'checkIsNoText', 0),
        new Check_Function_Data('name_first', $formated_post['name_first'], 'checkIsOverText', 1, 50),
        new Check_Function_Data('name_last',  $formated_post['name_last'] , 'checkIsOverText', 1, 50)
    );
    $name_checker = new Error_Checker(
        '名前',
        $name_check_functions
    );

    $sex_value = isset($formated_post['sex']) ? $formated_post['sex'] : '';
    $sex_check_functions = array(
        new Check_Function_Data('sex', $sex_value, 'checkIsNoChoise', 0)
    );
    $sex_checker = new Error_Checker(
        '性別',
        $sex_check_functions
    );

    $post_check_functions = array(
        new Check_Function_Data('post_first', $formated_post['post_first'], 'checkIsNoText', 0),
        new Check_Function_Data('post_last',  $formated_post['post_last'],  'checkIsNoText', 1),
        new Check_Function_Data('post_first', $formated_post['post_first'], 'checkIsIllegal', 2),
        new Check_Function_Data('post_last',  $formated_post['post_last'],  'checkIsIllegal', 3)
    );
    $post_checker = new Error_Checker(
        '郵便番号',
        $post_check_functions
    );

    $prefecture_check_functions = array(
        new Check_Function_Data('prefecture', $formated_post['prefecture'], 'checkIsEmptyValue', 0, '--')
    );
    $prefecture_checker = new Error_Checker(
        '都道府県',
        $prefecture_check_functions
    );

    $mail_address_check_functions = array(
        new Check_Function_Data('mail_address', $formated_post['mail_address'], 'checkIsNoText', 0),
        new Check_Function_Data('mail_address', $formated_post['mail_address'], 'checkIsIllegal', 1)
    );
    $mail_address_checker = new Error_Checker(
        'メールアドレス',
        $mail_address_check_functions
    );

    $hobby_check_functions = array();
    if (isset($formated_post['hobby']) && in_array('その他', $formated_post['hobby'])) {
        $other_hobby_value = isset($formated_post['other_descript']) ? $formated_post['other_descript'] : '';
        array_push(
            $hobby_check_functions,
            new Check_Function_Data('other_descript', $other_hobby_value, 'checkIsNoText', 0)
        );
    }
    $hobby_checker = new Error_Checker(
        '趣味',
        $hobby_check_functions
    );

    $checkers = array(
        $name_checker,
        $sex_checker,
        $post_checker,
        $prefecture_checker,
        $mail_address_checker,
        $hobby_checker
    );

    //エラー一覧を取得
    $errors = array();
    foreach ($checkers as $checker) {
        array_push($errors, $checker->getCheckResult());
    }

    return $errors;
}

//次のページに行けるならジャンプする関数。入力をform.phpに戻し、エラーがないならformCheck.phpへジャンプ
function checkJump() {
    if (empty($_POST) || isset($_POST['return'])) return;
    
    checkErrors();
    if (Error_Message::hasError() == false ) {
        print "onLoad='document.checkForm.submit();'";
    }      
}

//エラーがあればエラー一覧を表示
function showError() {
    if (empty($_POST) || isset($_POST['return'])) return;

    if (Error_Message::hasError()) {
        $error_texts = Error_Message::getAllErrorString();
        foreach ($error_texts as $error) {
            printf("%s<br>", $error);
        }
    } else {
        print "<input type='submit' value='dummy'>";
    }
}

//ポストデータがあればその文字列を、なければ空文字を返す
function getPOST($key) {
    return (isset($_POST[$key])) ? getFormatedText($_POST[$key], 0) : '';
}

//getPOSTの配列版
function getPOSTArray($key) {
    $ret = array();
    if (!isset($_POST[$key]) || !is_array($_POST[$key])) return $ret;
    
    foreach ($_POST[$key] as $key => $value) {
        $ret += array($key => getFormatedText($value,0));
    }
    return $ret;
}

//ポストの値があれば表示、なければ空白を表示
function showPOST($key) {
    print getPOST($key);
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
    <input type="text" name="name_first" id="name_first" value="<?php showPOST('name_first'); ?>">
    <input type="text" name="name_last" id="name_last" value="<?php showPOST('name_last'); ?>">
    <br> 
    
    <label>性別:</label>
      <?php
      $man_checked   = (getPOST('sex') == "男性")? "checked" : "";
      $woman_checked = (getPOST('sex') == "女性")? "checked" : "";
      ?>
    <input type="radio" name="sex" id="man" value="男性" <?php print $man_checked; ?>><label for="man">男性</label>
    <input type="radio" name="sex" id="woman" value="女性" <?php print $woman_checked; ?>><label for="woman">女性</label>
    <br>

    <label>郵便番号:</label>
    <input type="text" name="post_first" id="post_first" maxlength="3" value="<?php showPOST('post_first'); ?>">
    -
    <input type="text" name="post_last" id="post_last" maxlength="4" value="<?php showPOST('post_last'); ?>">
    <br>

    <label>都道府県:</label>

    <select name="prefecture" id="prefecture" size=1 value="<?php showPOST('prefecture'); ?>">
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
        $selected = (getPOST('prefecture') == $elm) ? 'selected' : '';
        printf("<option value='%s' %s>%s</option>", $elm, $selected, $elm);
    }
    ?>
    </select>
    <br>
    
    <label>メールアドレス:</label>
    <input type="text" name="mail_address" id="mail" value="<?php showPOST('mail_address'); ?>">
    <br>

    <label>趣味</label>
    <?php
    $HOBBYS = array('music' => '音楽鑑賞','movie' => '映画鑑賞','other' => 'その他');

    $check_list = getPOSTArray('hobby');

    foreach ($HOBBYS as $key => $elm) {
        $checked = '';
        
        if (empty($check_list) == false) {
            $checked = (in_array($elm, $check_list)) ? 'checked' : '';
        }
        printf("<input type='checkbox' id='%s' name='hobby[]' value='%s' %s><label for='%s'>%s</label>",
                      $key, $elm, $checked, $key, $elm);
    }
    ?>
    <input type="text" name="other_descript" id="other_descript" value="<?php showPOST('other_descript'); ?>">
    <br>

    <label>ご意見</label>
    <input type="text" id="opinion" name="opinion" value="<?php showPOST('opinion'); ?>">

    <input type="submit" value="確認">
    </fieldset>
  </form>

  <form method="post" name="checkForm" action="formCheck.php">
  <?php
  $formated_post = getFormatedTextArray($_POST);

  $NAMES = array(
      'name_first', 'name_last', 'sex', 'post_first', 'post_last',
      'prefecture','mail_address', 'other_descript', 'opinion'
  );

  foreach ($NAMES as $name) {
      $input = (isset($formated_post[$name])) ? $formated_post[$name] : '';
      printf("<input type='hidden' name='%s' value='%s'>", $name, $input);
  }

  if (isset($_POST['hobby'])) {
      foreach ($_POST['hobby'] as $hobby) {
          printf("<input type='hidden' name='hobby[]' value='%s'>", $hobby);
      }
  }

  //その他の趣味の詳細が入力されていたら、その他にチェックを付与
  if (!empty($formated_post['other_descript']) && !in_array('その他', $_POST['hobby'])) {
      print "<input type='hidden' name='hobby[]' value='その他'>";
  }

  showError();
  ?>
  </form>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
