<?php
require_once('format_text.php');
$formated_post = getFormatedTextArray($_POST);
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
        printf("%s %s", $formated_post['name_first'] ,$formated_post['name_last']);
        ?>
      </p>
      <p>
        性別：
        <?php
        print $formated_post['sex'];
        ?>
      </p>
       
      <p>
        郵便番号:
        <?php
        printf("%s-%s", $formated_post['post_first'], $formated_post['post_last']);
        ?>
      </p>
      
      <p>
        都道府県:
        <?php
        print $formated_post['prefecture'];
        ?>
      </p>
      
      <p>
        メールアドレス:
        <?php
        print $formated_post['mail_address'];
        ?>
      </p>

      <p>
        趣味:
        <?php
        if (isSet($formated_post['hobby'])) {
            foreach ($formated_post['hobby'] as $hobby) {
                printf("%s ", $hobby);
            }
            //その他があれば詳細を表示
            if (in_array('その他', $formated_post['hobby'])) {
                printf("(%s)", $formated_post['other_descript']);
            }
        }
        ?>
      </p>
      
      <p>
        ご意見:
        <?php print $formated_post['opinion']; ?>
      </p>
      <input type="button" value="送信" onClick="form.action=document.sendForm.submit();">
      <input type="button" value="戻る" onClick="form.action=document.returnForm.submit();">
    </form>  
    
    <form name="sendForm" method="post" action="finish.php">
      <input type='hidden' name='name_first'     value="<?php printf('%s', $formated_post['name_first']);   ?>">
      <input type='hidden' name='name_last'      value="<?php printf('%s', $formated_post['name_last']);    ?>">
      <input type='hidden' name='sex'            value="<?php printf('%s', $formated_post['sex']);          ?>">
      <input type='hidden' name='post_first'     value="<?php printf('%s', $formated_post['post_first']);   ?>">
      <input type='hidden' name='post_last'      value="<?php printf('%s', $formated_post['post_last']);    ?>">
      <input type='hidden' name='prefecture'     value="<?php printf('%s', $formated_post['prefecture']);   ?>">
      <input type='hidden' name='mail_address'   value="<?php printf('%s', $formated_post['mail_address']); ?>">
      <?php
      if (empty($formated_post['hobby']) == false) {
          foreach ($formated_post['hobby'] as $hobby) {
              printf("<input type='hidden' name='hobby[]' value='%s'>", $hobby);
          }
      }
      ?>
      <input type='hidden' name='other_descript' value="<?php printf('%s', $formated_post['other_descript']); ?>">
      <input type='hidden' name='opinion'        value="<?php printf('%s', $formated_post['opinion']);        ?>">
    </form>

    <form name="returnForm" method="post" action="form.php">
      <input type='hidden' name='name_first'     value="<?php printf('%s', $formated_post['name_first']);   ?>">
      <input type='hidden' name='name_last'      value="<?php printf('%s', $formated_post['name_last']);    ?>">
      <input type='hidden' name='sex'            value="<?php printf('%s', $formated_post['sex']);          ?>">
      <input type='hidden' name='post_first'     value="<?php printf('%s', $formated_post['post_first']);   ?>">
      <input type='hidden' name='post_last'      value="<?php printf('%s', $formated_post['post_last']);    ?>">
      <input type='hidden' name='prefecture'     value="<?php printf('%s', $formated_post['prefecture']);   ?>">
      <input type='hidden' name='mail_address'   value="<?php printf('%s', $formated_post['mail_address']); ?>">
      <?php
      if (empty($formated_post['hobby']) == false) {
          foreach ($formated_post['hobby'] as $hobby) {
              printf("<input type='hidden' name='hobby[]' value='%s'>", $hobby);
          }
      }
      ?>
      <input type='hidden' name='other_descript' value="<?php printf('%s', $formated_post['other_descript']); ?>">
      <input type='hidden' name='opinion'        value="<?php printf('%s', $formated_post['opinion']);        ?>">
      <!-- 確認画面から戻ったというフラグ、自動ジャンプ防止用--> 
      <input type='hidden' name='return' value='return'>
    </form>
  </section>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
