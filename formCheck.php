<?php
$formatedPOST = getFormatedTextArray($_POST);

function getFormatedTextArray($array) {
    $ret = array();

    foreach($array as $key => $value) {
        if(is_array($value) == false) {
            $ret += array($key => htmlspecialchars($value));
        } else {
            $add = array();
            foreach ($value as $arrayKey => $arrayValue) {
                $add += array($arrayKey => htmlspecialchars($arrayValue));
            }
            $ret += array($key => $add);
        }
    }

    return $ret;
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
    <form name="form" method="post" action="finish.php">
      <p>
        名前：
        <?php
        printf("%s %s", $formatedPOST['name_first'] ,$formatedPOST['name_last']);
        ?>
      </p>
      <p>
        性別：
        <?php
        print $formatedPOST['sex'];
        ?>
      </p>
       
      <p>
        郵便番号:
        <?php
        printf("%s-%s", $formatedPOST['post_first'], $formatedPOST['post_last']);
        ?>
      </p>
      
      <p>
        都道府県:
        <?php
        print $formatedPOST['prefecture'];
        ?>
      </p>
      
      <p>
        メールアドレス:
        <?php
        print $formatedPOST['mail_address'];
        ?>
      </p>

      <p>
        趣味:
        <?php
        if (isSet($formatedPOST['hobby'])) {
            foreach ($formatedPOST['hobby'] as $hobby) {
                printf("%s ", $hobby);
            }
            //その他があれば詳細を表示
            if (in_array('その他', $formatedPOST['hobby'])) {
                printf("(%s)", $formatedPOST['other_descript']);
            }
        }
        ?>
      </p>
      
      <p>
        ご意見:
        <?php print $formatedPOST['opinion']; ?>
      </p>
      <input type="submit" value="送信">
      <input type="button" value="戻る" onClick="form.action=document.returnForm.submit();">
    </form>  

    <form name="returnForm" method="post" action="form.php">
      <input type='hidden' name='name_first'     value="<?php printf('%s', $formatedPOST['name_first']);   ?>">
      <input type='hidden' name='name_last'      value="<?php printf('%s', $formatedPOST['name_last']);    ?>">
      <input type='hidden' name='sex'            value="<?php printf('%s', $formatedPOST['sex']);          ?>">
      <input type='hidden' name='post_first'     value="<?php printf('%s', $formatedPOST['post_first']);   ?>">
      <input type='hidden' name='post_last'      value="<?php printf('%s', $formatedPOST['post_last']);    ?>">
      <input type='hidden' name='prefecture'     value="<?php printf('%s', $formatedPOST['prefecture']);   ?>">
      <input type='hidden' name='mail_address'   value="<?php printf('%s', $formatedPOST['mail_address']); ?>">
      <?php
      if(empty($formatedPOST['hobby']) == false) {
          foreach($formatedPOST['hobby'] as $hobby) {
              printf("<input type='hidden' name='hobby[]' value='%s'>", $hobby);
          }
      }
      ?>
      <input type='hidden' name='other_descript' value="<?php printf('%s', $formatedPOST['other_descript']); ?>">
      <input type='hidden' name='opinion'        value="<?php printf('%s', $formatedPOST['opinion']);        ?>">
      <!-- 確認画面から戻ったというフラグ、自動ジャンプ防止用--> 
      <input type='hidden' name='return' value='return'>
    </form>
  </section>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
