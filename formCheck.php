<?php
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
        printf("%s %s", $TRIMED_POST_DATA['name_first'] ,$TRIMED_POST_DATA['name_last']);
        ?>
      </p>
      <p>
        性別：
        <?php
            print $TRIMED_POST_DATA['sex'];
        ?>
      </p>
       
      <p>
        郵便番号:
        <?php
        printf("%s-%s", $TRIMED_POST_DATA['post_first'], $TRIMED_POST_DATA['post_last']);
        ?>
      </p>
      
      <p>
        都道府県:
        <?php
        print $TRIMED_POST_DATA['prefecture'];
        ?>
      </p>
      
      <p>
        メールアドレス:
        <?php
        print $TRIMED_POST_DATA['mail_address'];
        ?>
      </p>

      <p>
        趣味:
        <?php
        if (isSet($TRIMED_POST_DATA['hobby'])) {
            foreach ($TRIMED_POST_DATA['hobby'] as $hobby) {
                printf("%s ", $hobby);
            }
            //その他があれば詳細を表示
            if (in_array('その他', $TRIMED_POST_DATA['hobby'])) {
                printf("(%s)", $TRIMED_POST_DATA['other_descript']);
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
      if(empty($TRIMED_POST_DATA['hobby']) == false) {
          foreach($TRIMED_POST_DATA['hobby'] as $hobby) {
              printf("<input type='hidden' name='hobby[]' value='%s'>", $hobby);
          }
      }
      ?>
      <input type='hidden' name='other_descript' value="<?php printf('%s', $TRIMED_POST_DATA['other_descript']); ?>">
      <input type='hidden' name='opinion'        value="<?php printf('%s', $TRIMED_POST_DATA['opinion']);        ?>">
       
      <input type='submit' value='戻る'>
      <input type='hidden' name='return' value='return'>
    </form>
  </section>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
