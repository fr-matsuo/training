<?php
//エラーメッセージの内容や対象を保持・出力するクラス
class ErrorInfo 
{
    //エラー項目の名前
    private static $NAME = array(
        'name_first'     => '姓',
        'name_last'      => '名',
        'sex'            => '性別',
        'post'           => '郵便番号',
        'prefecture'     => '都道府県',
        'mail_address'   => 'メールアドレス',
        'other_descript' => 'その他の詳細'
    );
    
    //エラーの内容
    private static $KIND = array(
        'noText'   => '入力',
        'noChoise' => '選択',
        'overText' => '字以内で入力',
        'illegal'  => '正しく入力'
    );

    //チェック関数に配列を渡すと、エラーの場合のみインスタンスを追加。インスタンスの生成はこれらのみ
    
    public static function checkIsNoText(&$errorArray, $data, $name) {           //必須入力チェック
        if (empty($data)) {
            array_push($errorArray, new ErrorInfo($name, 'noText', ''));
            return true;
        }
        return false;
    }
    public static function checkIsEmptyValue(&$errorArray, $data, $name, $emptyValue){//必須入力チェック(空を表す文字列と一致しないか)
        if ($data == $emptyValue) {
            array_push($errorArray, new ErrorInfo($name, 'noText', ''));
            return true;
        }
        return false;
    }
   
    public static function checkIsNoChoise(&$errorArray, $data, $name) {         //必須選択チェック
        if (empty($data)) {
            array_push($errorArray, new ErrorInfo($name, 'noChoise', ''));
            return true;
        }
        return false;
    }
    public static function checkIsOverText(&$errorArray, $data, $name, $value) { //字数チェック
        if (mb_strlen($data) > $value) {
            array_push($errorArray, new ErrorInfo($name, 'overText', $value));
            return true;
        }
        return false;
    }
    public static function checkIsIllegal(&$errorArray, $data, $name) {          //文法チェック
        if ($name == 'mail_address' && isMailAddress($data) == false) {
            array_push($errorArray, new ErrorInfo($name, 'illegal', ''));
            return true;
        }
        return false;
    }

    private $name;  //エラー項目
    private $kind;  //エラー内容
    private $value; //'50'字以内などの値

    private function __construct($errorName, $errorKind, $errorValue) {
        $this->name  = ErrorInfo::$NAME[$errorName];
        $this->kind  = ErrorInfo::$KIND[$errorKind];
        $this->value = $errorValue;
    }
    //エラー内容を表示
    public function show() {
        $valueText = (empty($this->value)) ? (string)$this->value : '';
        printf('%sを%s%sしてください。', $this->name, $valueText, $this->kind);
    }
}

$TRIMED_POST_DATA = getTrimedPOST();

//_POSTから前後の空白を除いたものを取得
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

//エラーメッセージ配列をすべて出力
function outErrorMessage($errorMessages) {
    foreach($errorMessages as $msg) {
        $msg->show();
    }
}
    
//メールアドレスの書式チェック
function isMailAddress($address) {
    if (substr_count($address, '@') != 1) return false;
    if (substr_count($address, ' ') != 0) return false;
    if (strstr($address, '@')       == '@') return false;
    if (strstr($address, '@', true) == '')  return false;
        
    return true;
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
        
        ErrorInfo::checkIsNoText($nameErrors, $TRIMED_POST_DATA['name_first'], 'name_first');
        ErrorInfo::checkIsNoText($nameErrors, $TRIMED_POST_DATA['name_last' ], 'name_first');
        ErrorInfo::checkIsOverText($nameErrors, $TRIMED_POST_DATA['name_first'], 'name_first', 50);
        ErrorInfo::checkIsOverText($nameErorrs, $TRIMED_POST_DATA['name_last'] , 'name_last',  50);
        
        //表示
        if (empty($nameErrors)) {
            printf("%s %s", $TRIMED_POST_DATA['name_first'] ,$TRIMED_POST_DATA['name_last']);
        } else {
            outErrorMessage($nameErrors);
        }
        ?>
      </p>
      <p>
        性別：
        <?php
        $sexErrors = array();

        ErrorInfo::checkIsNoChoise($sexErrors, $TRIMED_POST_DATA['sex'], 'sex');

        //表示 
        if (empty($sexErrors)) {
            print $TRIMED_POST_DATA['sex'];
        } else {
            outErrorMessage($sexErrors);
        }
        ?>
      </p>
       
      <p>
        郵便番号:
        <?php
        $postErrors = array();
        
        //前半入力済みの場合のみ後半を調べて、エラーの重複を避ける
        $isNoFirstNumber = ErrorInfo::checkIsNoText($postErrors, $TRIMED_POST_DATA['post_first'], 'post');
        if($isNoFirstNumber == false) ErrorInfo::checkIsNoText($postErrors, $TRIMED_POST_DATA['post_last'], 'post');

        if (empty($postErrors)) {
            printf("%s-%s", $TRIMED_POST_DATA['post_first'], $TRIMED_POST_DATA['post_last']);
        } else {
            outErrorMessage($postErrors);
        }
        ?>
      </p>
      
      <p>
        都道府県:
        <?php
        $prefectureErrors = array();
        
        ErrorInfo::checkIsEmptyValue($prefectureErrors, $TRIMED_POST_DATA['prefecture'], 'prefecture', '--');

        if (empty($prefectureErrors)) {
            print $TRIMED_POST_DATA['prefecture'];
        } else {
            outErrorMessage($prefectureErrors);
        }
        ?>
      </p>
      
      <p>
        メールアドレス:
        <?php
        $mailErrors = array();

        //入力されている場合のみ書式を調べる
        $noMailAddress = ErrorInfo::checkIsNoText($mailErrors, $TRIMED_POST_DATA['mail_address'], 'mail_address');
        if ($noMailAddress == false) ErrorInfo::checkIsIllegal($mailErrors, $TRIMED_POST_DATA['mail_address'], 'mail_address');

        if (empty($mailErrors)) {
            print $TRIMED_POST_DATA['mail_address'];
        } else {
            outErrorMessage($mailErrors);
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
                ErrorInfo::checkIsNoText($hobbyErrors, $TRIMED_POST_DATA['other_descript'], 'other_descript');
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
                outErrorMessage($hobbyErrors);
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
