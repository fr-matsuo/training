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
      <p>
        名前：
        <?php
          printf("%s %s", $_POST['name_first'] ,$_POST['name_last']);
        ?>
      </p>
      
      <p>
        性別:
        <?php
          printf("%s", $_POST['sex']);
        ?>
      </p>
      
      <p>
        郵便番号:
        <?php
          printf("%s-%s", $_POST['post_first'], $_POST['post_last']);
        ?>
      </p>
      
      <p>
        都道府県:
        <?php
          printf("%s", $_POST['prefecture']);
        ?>
      </p>
      
      <p>
        メールアドレス:
        <?php
          printf("%s", $_POST['mail_address']);
        ?>
      </p>
      
      <p>趣味:
        <?php
          //チェックしたボックス一覧を取得・表示
          $checkList = $_POST['hobby'];
          $length    = count($checkList);
          for ($i = 0; $i < $length-1; $i++ ) {
            printf("%s,", $checkList[$i]);
          }
          printf("%s", $checkList[$length-1]);
          //その他ならそれを表示
          if (in_array('other', $checkList)) {
            printf("(%s)", $_POST['other_descript']);
          }
        ?>
      </p>
      
      <p>ご意見:
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
