<!DOCTYPE html>

<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" type="text/css" href="common.css">
  <link rel="stylesheet" type="text/css" href="form.css">
  <title>フォーム画面</title>  
</head>

<body <?php checkJump($formated_post); ?> >
<header>
<h1>フォーム>入力</h1>
</header>

<section>
  <form method="post" name="form" action="form.php">
    <fieldset>
    <legend>フォーム</legend>

    <label>名前:</label>
    <input type="text" name="name_first" id="name_first" value="<?php showPOST('name_first', $formated_post); ?>">
    <input type="text" name="name_last" id="name_last" value="<?php showPOST('name_last', $formated_post); ?>">
    <br> 
    
    <label>性別:</label>
    <input type="radio" name="sex" id="man" value="男性" <?php print $man_checked; ?>><label for="man">男性</label>
    <input type="radio" name="sex" id="woman" value="女性" <?php print $woman_checked; ?>><label for="woman">女性</label>
    <br>

    <label>郵便番号:</label>
    <input type="text" name="post_first" id="post_first" maxlength="3" value="<?php showPOST('post_first', $formated_post); ?>">
    -
    <input type="text" name="post_last" id="post_last" maxlength="4" value="<?php showPOST('post_last', $formated_post); ?>">
    <br>

    <label>都道府県:</label>
    <?php showPrefectures($formated_post, $PREFECTURES); ?>
    <br>
    
    <label>メールアドレス:</label>
    <input type="text" name="mail_address" id="mail" value="<?php showPOST('mail_address', $formated_post); ?>">
    <br>

    <label>趣味</label>
    <?php showHobbys($formated_post, $HOBBYS); ?>
    <br>

    <label>ご意見</label>
    <input type="text" id="opinion" name="opinion" value="<?php showPOST('opinion', $formated_post); ?>">

    <input type="submit" value="確認">
    </fieldset>
  </form>

  <?php showError($formated_post); ?>

  <form method="post" name="checkForm" action="formCheck.php">
  <?php writeHiddenParams($formated_post, $NAMES); ?>
  </form>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
