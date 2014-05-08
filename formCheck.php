<?php
/*
$ERROR_MESSAGE_NO_FIRST_NAME        = "姓を入力してください。";
$ERROR_MESSAGE_NO_LAST_NAME         = "名を入力してください。";
$ERROR_MESSAGE_OVER_FIRST_NAME      = "姓は50字以内で入力してください。";
$ERROR_MESSAGE_OVER_LAST_NAME       = "名は50字以内で入力してください。";
$ERROR_MESSAGE_NO_SEX               = "性別を選択してください。";
$ERROR_MESSAGE_NO_POST_NUMBER       = "郵便番号を入力してください。";
$ERROR_MESSAGE_NO_PREFECTURE        = "都道府県を入力してください。";
$ERROR_MESSAGE_NO_MAIL_ADDRESS      = "メールアドレスを入力してください。";
$ERROR_MESSAGE_ILLEGAL_MAIL_ADDRESS = "メールアドレスを正しく入力してください。";
$ERROR_MESSAGE_NO_OTHER             = "その他の詳細を入力してください。";
*/

//エラーメッセージの内容や対象を保持・出力するクラス
class ErrorInfo 
{
    //エラー項目の名前
    public static $NAME = array(
        'name_first'     => '姓',
        'name_last'      => '名',
        'sex'            => '性別',
        'post'           => '郵便番号',
        'prefecture'     => '都道府県',
        'mail_address'   => 'メールアドレス',
        'other_descript' => 'その他の詳細'
    );
    
    //エラーの内容
    public static $KIND = array(
        'noText'   => '入力',
        'noChoise' => '選択',
        'overText' => '字以内で入力',
        'illegal'  => '正しく入力'
    );

    private $name;  //エラー項目
    private $kind;  //エラー内容
    private $value; //'50'字以内などの値

    public function __construct($errorName, $errorKind, $errorValue) {
        $this->name  = ErrorInfo::$NAME[$errorName];
        $this->kind  = ErrorInfo::$KIND[$errorKind];
        $this->value = $errorValue;
    }

    //エラー内容を表示
    public function show(){
        printf('%sを%s%sしてください。', $this->name, $this->value, $this->kind);
    }
}

//エラー一覧
$ERROR_MESSAGE_NO_FIRST_NAME        = new ErrorInfo('name_first',     'noText',   '');
$ERROR_MESSAGE_NO_LAST_NAME         = new ErrorInfo('name_last' ,     'noText',   '');
$ERROR_MESSAGE_OVER_FIRST_NAME      = new ErrorInfo('name_first',     'overText', '50');
$ERROR_MESSAGE_OVER_LAST_NAME       = new ErrorInfo('name_last' ,     'overText', '50');
$ERROR_MESSAGE_NO_SEX               = new ErrorInfo('sex',            'noText',   '');
$ERROR_MESSAGE_NO_POST_NUMBER       = new ErrorInfo('post',           'noText',   '');
$ERROR_MESSAGE_NO_PREFECTURE        = new ErrorInfo('prefecture',     'noText',   '');
$ERROR_MESSAGE_NO_MAIL_ADDRESS      = new ErrorInfo('mail_address',   'noText',   '');
$ERROR_MESSAGE_ILLEGAL_MAIL_ADDRESS = new ErrorInfo('mail_address',   'illegal',  '');
$ERROR_MESSAGE_NO_OTHER             = new ErrorInfo('other_descript', 'noText',   '');

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
        $msg -> show();
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

        if (empty($_POST['name_first']))          {array_push($nameErrors, $ERROR_MESSAGE_NO_FIRST_NAME   );}
        if (empty($_POST['name_last' ]))          {array_push($nameErrors, $ERROR_MESSAGE_NO_LAST_NAME    );}
        if (mb_strlen($_POST['name_first']) > 50) {array_push($nameErrors, $ERROR_MESSAGE_OVER_FIRST_NAME );}
        if (mb_strlen($_POST['name_last' ]) > 50) {array_push($nameErrors, $ERROR_MESSAGE_OVER_LAST_NAME  );}

        //表示
        if (empty($nameErrors)) {
            printf("%s %s", $_POST['name_first'] ,$_POST['name_last']);
        } else {
            outErrorMessage($nameErrors);
        } 
        ?>
      </p>
      <p>
        性別：
        <?php
        $sexErrors = array();

        if (empty($_POST['sex'])) {array_push($sexErrors, $ERROR_MESSAGE_NO_SEX);}

        $errorNum = count($sexErrors);

        //表示 
        if ($errorNum == 0) {
            print $_POST['sex'];
        } else {
            outErrorMessage($sexErrors);
        }
        ?>
      </p>
       
      <p>
        郵便番号:
        <?php
        $postErrors = array();

        if (empty($_POST['post_first']) || empty($_POST['post_last'])) {array_push($postErrors, $ERROR_MESSAGE_NO_POST_NUMBER);}

        if (empty($postErrors)) {
            printf("%s-%s", $_POST['post_first'], $_POST['post_last']);
        } else {
            outErrorMessage($postErrors);
        }
        ?>
      </p>
      
      <p>
        都道府県:
        <?php
        $prefectureErrors = array();
          
        if ($_POST['prefecture'] == '--') {array_push($prefectureErrors, $ERROR_MESSAGE_NO_PREFECTURE);}

        if (empty($prefectureErrors)) {
            printf("%s", $_POST['prefecture']);
        } else {
            outErrorMessage($prefectureErrors);
        }
        ?>
      </p>
      
      <p>
        メールアドレス:
        <?php
        $mailErrors = array();

        if (empty($_POST['mail_address'])) {
            array_push($mailErrors, $ERROR_MESSAGE_NO_MAIL_ADDRESS);
        } else if (isMailAddress($_POST['mail_address']) == false) {
            array_push($mailErrors, $ERROR_MESSAGE_ILLEGAL_MAIL_ADDRESS);
        }

        if (empty($mailErrors)) {
            printf("%s", $_POST['mail_address']);
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
        if (isSet($_POST['hobby'])) {
            $checkList = $_POST['hobby'];
            $length    = count($checkList);
            if (in_array('その他', $checkList) && empty($_POST['other_descript'])) {
                  array_push($hobbyErrors, $ERROR_MESSAGE_NO_OTHER);
            }

            if (empty($hobbyErrors)) {
                //表示
                for ($i = 0; $i < $length-1; $i++ ) {
                    printf("%s,", $checkList[$i]);
                }
                printf("%s", $checkList[$length-1]);
                if (in_array('その他', $checkList)) {
                    printf("(%s)", $_POST['other_descript']);
                }
            } else {
                outErrorMessage($hobbyErrors);
            }
        }
        ?>
      </p>
      
      <p>
        ご意見:
        <?php
          printf("%s", $_POST['opinion']);
        ?>
      </p>
      <input type="submit" value="送信" formaction="finish.php">
    </form>  
    
    
    <form action="form.php" method="post">
      <input type='hidden' name='name_first'     value="<?php printf('%s', $_POST['name_first']);    ?>">
      <input type='hidden' name='name_last'      value="<?php printf('%s', $_POST['name_last']);     ?>">
      <input type='hidden' name='sex'            value="<?php printf('%s', $_POST['sex']);           ?>">
      <input type='hidden' name='post_first'     value="<?php printf('%s', $_POST['post_first']);    ?>">
      <input type='hidden' name='post_last'      value="<?php printf('%s', $_POST['post_last']);     ?>">
      <input type='hidden' name='prefecture'     value="<?php printf('%s', $_POST['prefecture']);    ?>">
      <input type='hidden' name='mail_address'   value="<?php printf('%s', $_POST['mail_address']);  ?>">
      <?php
      if(empty($checkList) == false) {
          for ($i = 0; $i < $length; $i++) {
              printf("<input type='hidden' name='hobby[]' value='%s'>", $checkList[$i]);
          }
      }
      ?>
      <input type='hidden' name='other_descript' value="<?php printf('%s', $_POST['other_descript']);?>">
      <input type='hidden' name='opinion'        value="<?php printf('%s', $_POST['opinion']);       ?>">
       
      <input type='submit' value='戻る'>
    </form>
  </section>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
