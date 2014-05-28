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
