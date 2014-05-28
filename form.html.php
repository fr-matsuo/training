<?php require_once('form.php'); ?>
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
  <form method="post" name="form" action="form.html.php">
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
