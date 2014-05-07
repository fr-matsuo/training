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
    <form>
      <?php
        //エラーメッセージ配列を横一列で出力
        function outErrorMessage($errorMessages) {
          foreach($errorMessages as $msg) {
            printf("%s ", $msg);
          }
        }
        //メールアドレスの書式チェック
        function isMailAddress($address) {
          if (substr_count($address, '@') != 1) {return false;}
          if (substr_count($address, ' ') != 0) {return false;}
          if (strstr($address, '@')       == '@') {return false;}
          if (strstr($address, '@', true) == '')  {return false;}
           
          return true;
        }
      ?>
      
      <?php
        foreach ($_POS as $value) {
          if (is_array($value)) {
            foreach ($value as $elm) {
              $elm = trim($elm);
            }
          } else {
            $value = trim($value);
          }
        }
      ?>
      <p>
        名前：
        <?php
          $ERROR_MESSAGE_NO_FIRST_NAME   = "姓を入力してください。";
          $ERROR_MESSAGE_NO_LAST_NAME    = "名を入力してください。";
          $ERROR_MESSAGE_OVER_FIRST_NAME = "姓は50字以内で入力してください。";
          $ERROR_MESSAGE_OVER_LAST_NAME  = "名は50字以内で入力してください。";
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
          $ERROR_MESSAGE_NO_SEX = "性別を選択してください。";
          $sexErrors = array();

          if (empty($_POST['sex'])) {array_push($sexErrors, $ERROR_MESSAGE_NO_SEX);}

          $errorNum = count($sexErrors);

          //表示 
          if ($errorNum == 0) {
            printf("%s", $_POST['sex']);
          } else {
            outErrorMessage($sexErrors);
          }
        ?>
      </p>
       
      <p>
        郵便番号:
        <?php
          $ERROR_MESSAGE_NO_POST_NUMBER = "郵便番号を入力してくださいい。";
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
          $ERROR_MESSAGE_NO_PREFECTURE = "都道府県を入力してください。";
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
          $ERROR_MESSAGE_NO_MAIL_ADDRESS      = "メールアドレスを入力してください。";
          $ERROR_MESSAGE_ILLEGAL_MAIL_ADDRESS = "メールアドレスを正しく入力してください。";
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
          $ERROR_MESSAGE_NO_OTHER = "その他の詳細を入力してください。";
          $hobbyErrors = array();

          //チェックしたボックス一覧を取得・表示
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
        ?>
      </p>
      
      <p>
        ご意見:
        <?php
          printf("%s", $_POST['opinion']);
        ?>
      </p>
      
      <input type="submit" value="戻る" formaction="form.php">
      <input type="submit" value="送信" formaction="finish.php">
    </form>
  </section>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
