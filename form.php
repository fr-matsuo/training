<!DOCTYPE html>

<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" type="text/css" href="common.css">
  <link rel="stylesheet" type="text/css" href="form.css">
  <title>フォーム画面</title>
</head>

<body>
  <header>
    <h1>フォーム>入力</h1>
  </header>

  <section>
    <form method="post" action="formCheck.php">
      <fieldset>
        <legend>フォーム</legend>
        
        <label>名前:</label>
        <input type="text" name="name_first" id="name_first" value="<?php printf('%s', $_POST['name_first']); ?>">
        <input type="text" name="name_last" id="name_last" value="<?php printf('%s', $_POST['name_last']); ?>">
        <br> 
        
        <label>性別:</label>
        <?php $manChecked   = ($_POST['sex'] == "男性")? "checked" : "";?>
        <?php $womanChecked = ($_POST['sex'] == "女性")? "checked" : "";?>
        <input type="radio" name="sex" id="man" value="男性" <?php print $manChecked ?>><label for="man">男性</label>
        <input type="radio" name="sex" id="woman" value="女性" <?php print $womanChecked ?>><label for="woman">女性</label>
        <br>
  
        <label>郵便番号:</label>
        <input type="text" name="post_first" id="post_first" value="<?php printf('%s', $_POST['post_first']); ?>">
        -
        <input type="text" name="post_last" id="post_last" value="<?php printf('%s', $_POST['post_last']); ?>">
        <br>
        
        <label>都道府県:</label>
        
        <select name="prefecture" id="prefecture" size=1 value="<?php printf('%s', $_POST['prefecture']); ?>">
          <option id="--" value="--">--</option>
          <?php
            $PREFECTURES = array(
              '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
              '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
              '新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県',
              '三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
              '鳥取県','島根県','岡山県','広島県','山口県',
              '徳島県','香川県','愛媛県','高知県',
              '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'
            );

            foreach ($PREFECTURES as $key => $elm) {
              $selected = ($_POST['prefecture'] == $elm) ? 'selected' : '';
              printf("<option value='%s' %s>%s</option>", $elm, $selected, $elm);
            }
          ?>
          
          <!--
          <option id="Tokyo" value="東京都">東京都</option>
          <option id="Saitama" value="埼玉県">埼玉県</option>
          <option id="Gunma" value="群馬県">群馬県</option>
          -->
        </select>
        <br>
        
        <label>メールアドレス:</label>
        <input type="text" name="mail_address" id="mail" value="<?php printf('%s', $_POST['mail_address']); ?>">
        <br>
        
        <label>趣味</label>
        <?php
          $HOBBYS = array('music' => '音楽鑑賞','movie' => '映画鑑賞','other' => 'その他');

          $checkList = $_POST['hobby'];

          foreach ($HOBBYS as $key => $elm) {
            $checked = (in_array($elm, $checkList)) ? 'checked' : '';
            printf("<input type='checkbox' id='%s' name='hobby[]' value='%s' %s><label for='%s'>%s</label>",
                    $key, $elm, $checked, $key, $elm);
          }
        ?>
        <!--
        <input type="checkbox" name="hobby[]" id="music" value="音楽鑑賞"><label for="music">音楽鑑賞</label>
        <input type="checkbox" name="hobby[]" id="movie" value="映画鑑賞"><label for="movie">映画鑑賞</label>
        <input type="checkbox" name="hobby[]" id="other" value="その他"><label for="other">その他</label>
        -->
        <input type="text" name="other_descript" id="other_descript" value="<?php printf('%s', $_POST['other_descript']); ?>">
        <br>
        
        <label>ご意見</label>
        <input type="text" id="opinion" name="opinion" value="<?php printf('%s', $_POST['opinion']); ?>">
        <br>
  
        <input type="submit" value="確認">
      </fieldset>
    </form>
  </section>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>
</html>
