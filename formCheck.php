<?php
//エラーメッセージの内容や対象を保持・出力するクラス
class Error_Info
{
    //エラー項目の名前
    private static $_NAME = array(
        'name_first'     => '姓',
        'name_last'      => '名',
        'sex'            => '性別',
        'post'           => '郵便番号',
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

    //チェック関数に配列を渡すと、エラーの場合のみインスタンスを追加。インスタンスの生成はこれらのみ
    
    public static function checkIsNoText(&$errorArray, $data, $name) {           //必須入力チェック
        if (empty($data)) {
            array_push($errorArray, new Error_Info($name, 'noText', ''));
            return true;
        }
        return false;
    }
    public static function checkIsEmptyValue(&$errorArray, $data, $name, $emptyValue){//必須入力チェック(空を表す文字列と一致しないか)
        if ($data == $emptyValue) {
            array_push($errorArray, new Error_Info($name, 'noText', ''));
            return true;
        }
        return false;
    }
   
    public static function checkIsNoChoise(&$errorArray, $data, $name) {         //必須選択チェック
        if (empty($data)) {
            array_push($errorArray, new Error_Info($name, 'noChoise', ''));
            return true;
        }
        return false;
    }
    public static function checkIsOverText(&$errorArray, $data, $name, $value) { //字数チェック
        if (mb_strlen($data) > $value) {
            array_push($errorArray, new Error_Info($name, 'overText', $value));
            return true;
        }
        return false;
    }
    public static function checkIsIllegal(&$errorArray, $data, $name) {          //文法チェック
        switch ($name) {
        case 'mail_address' ://メールアドレス
            if (preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@[a-zA-Z0-9_-]+([a-zA-Z0-9\._-]+)+$/', $data) == false) {
                array_push($errorArray, new Error_Info($name, 'illegal', ''));
                return true;
            }
            return false;
        
        default : return false;
        }
    }
    //エラーメッセージ配列をすべて出力
    public static function outErrorMessage($errorMessages) {
        foreach($errorMessages as $msg) {
            $msg->_show();
        }
    }

    private $_name;  //エラー項目
    private $_kind;  //エラー内容
    private $_value; //'50'字以内などの値

    private function __construct($errorName, $errorKind, $errorValue) {
        $this->_name  = Error_Info::$_NAME[$errorName];
        $this->_kind  = Error_Info::$_KIND[$errorKind];
        $this->_value = $errorValue;
    }
    //エラー内容を表示
    private function _show() {
        $valueText = (empty($this->_value)) ? (string)$this->_value : '';
        printf('%sを%s%sしてください。', $this->_name, $valueText, $this->_kind);
    }
}

//_POSTから前後の空白を除いたもの
$TRIMED_POST_DATA = getTrimedPOST();

//_TRIMED_POST_DATAを取得
function getTrimedPOST() {
    $trimedArray = array();

    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $arrayKey => $arrayValue) {
                $trimedArray[$key][$arrayKey] = trim($arrayValue);
            }
        } else {
            $trimedArray[$key] = trim($value);
        }
    }

    return $trimedArray;
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" type="text/css" href="common.css">
  <link rel="stylesheet" type="text/css" href="formCheck.css">
  <title>確認画面</title>
</head>

<body>
  <header>
    <h1>フォーム>確認</h1>
  </header>

  <section>
    <form/>
      <p>
        名前：
        <?php
        $nameErrors = array();
        
        Error_Info::checkIsNoText($nameErrors, $TRIMED_POST_DATA['name_first'], 'name_first');
        Error_Info::checkIsNoText($nameErrors, $TRIMED_POST_DATA['name_last' ], 'name_first');
        Error_Info::checkIsOverText($nameErrors, $TRIMED_POST_DATA['name_first'], 'name_first', 50);
        Error_Info::checkIsOverText($nameErorrs, $TRIMED_POST_DATA['name_last'] , 'name_last',  50);
        
        //表示
        if (empty($nameErrors)) {
            printf("%s %s", $TRIMED_POST_DATA['name_first'] ,$TRIMED_POST_DATA['name_last']);
        } else {
            Error_Info::outErrorMessage($nameErrors);
        }
        ?>
      </p>
      <p>
        性別：
        <?php
        $sexErrors = array();

        Error_Info::checkIsNoChoise($sexErrors, $TRIMED_POST_DATA['sex'], 'sex');

        //表示 
        if (empty($sexErrors)) {
            print $TRIMED_POST_DATA['sex'];
        } else {
            Error_Info::outErrorMessage($sexErrors);
        }
        ?>
      </p>
       
      <p>
        郵便番号:
        <?php
        $postErrors = array();
        
        //前半入力済みの場合のみ後半を調べて、エラーの重複を避ける
        $isNoFirstNumber = Error_Info::checkIsNoText($postErrors, $TRIMED_POST_DATA['post_first'], 'post');
        if($isNoFirstNumber == false) Error_Info::checkIsNoText($postErrors, $TRIMED_POST_DATA['post_last'], 'post');

        if (empty($postErrors)) {
            printf("%s-%s", $TRIMED_POST_DATA['post_first'], $TRIMED_POST_DATA['post_last']);
        } else {
            Error_Info::outErrorMessage($postErrors);
        }
        ?>
      </p>
      
      <p>
        都道府県:
        <?php
        $prefectureErrors = array();
        
        Error_Info::checkIsEmptyValue($prefectureErrors, $TRIMED_POST_DATA['prefecture'], 'prefecture', '--');

        if (empty($prefectureErrors)) {
            print $TRIMED_POST_DATA['prefecture'];
        } else {
            Error_Info::outErrorMessage($prefectureErrors);
        }
        ?>
      </p>
      
      <p>
        メールアドレス:
        <?php
        $mailErrors = array();

        //入力されている場合のみ書式を調べる
        $noMailAddress = Error_Info::checkIsNoText($mailErrors, $TRIMED_POST_DATA['mail_address'], 'mail_address');
        if ($noMailAddress == false) Error_Info::checkIsIllegal($mailErrors, $TRIMED_POST_DATA['mail_address'], 'mail_address');

        if (empty($mailErrors)) {
            print $TRIMED_POST_DATA['mail_address'];
        } else {
            Error_Info::outErrorMessage($mailErrors);
        }
        ?>
      </p>

      <p>
        趣味:
        <?php
        $hobbyErrors = array();

        //チェックしたボックス一覧を取得・表示
        if (isSet($TRIMED_POST_DATA['hobby'])) {
            $checkList = $TRIMED_POST_DATA['hobby'];
            $length    = count($checkList);

            //その他があれば、詳細の入力をチェック
            if (in_array('その他', $checkList)) {
                Error_Info::checkIsNoText($hobbyErrors, $TRIMED_POST_DATA['other_descript'], 'other_descript');
            }

            if (empty($hobbyErrors)) {
                //表示
                for ($i = 0; $i < $length-1; $i++ ) {
                    printf("%s,", $checkList[$i]);
                }
                printf("%s", $checkList[$length-1]);
                //その他があれば詳細を表示
                if (in_array('その他', $checkList)) {
                    printf("(%s)", $TRIMED_POST_DATA['other_descript']);
                }
            } else {
                Error_Info::outErrorMessage($hobbyErrors);
            }
        }
        ?>
      </p>
      
      <p>
        ご意見:
        <?php print $TRIMED_POST_DATA['opinion']; ?>
      </p>
      <input type="submit" value="送信" formaction="finish.php">
    </form>  
    <form action="form.php" method="post">
      <input type='hidden' name='name_first'     value="<?php printf('%s', $TRIMED_POST_DATA['name_first']);   ?>">
      <input type='hidden' name='name_last'      value="<?php printf('%s', $TRIMED_POST_DATA['name_last']);    ?>">
      <input type='hidden' name='sex'            value="<?php printf('%s', $TRIMED_POST_DATA['sex']);          ?>">
      <input type='hidden' name='post_first'     value="<?php printf('%s', $TRIMED_POST_DATA['post_first']);   ?>">
      <input type='hidden' name='post_last'      value="<?php printf('%s', $TRIMED_POST_DATA['post_last']);    ?>">
      <input type='hidden' name='prefecture'     value="<?php printf('%s', $TRIMED_POST_DATA['prefecture']);   ?>">
      <input type='hidden' name='mail_address'   value="<?php printf('%s', $TRIMED_POST_DATA['mail_address']); ?>">
      <?php
      if(empty($checkList) == false) {
          for ($i = 0; $i < $length; $i++) {
              printf("<input type='hidden' name='hobby[]' value='%s'>", $checkList[$i]);
          }
      }
      ?>
      <input type='hidden' name='other_descript' value="<?php printf('%s', $TRIMED_POST_DATA['other_descript']); ?>">
      <input type='hidden' name='opinion'        value="<?php printf('%s', $TRIMED_POST_DATA['opinion']);        ?>">
       
      <input type='submit' value='戻る'>
    </form>
  </section>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
