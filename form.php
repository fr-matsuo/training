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
        <input type="text" name="name_first" id="name_first">
        <input type="text" name="name_last" id="name_last"> 
        <br> 
        
        <label>性別:</label>
        <input type="radio" name="sex" id="man" value="man"><label for="man">男性</label>
        <input type="radio" name="sex" id="woman" value="woman"><label for="woman">女性</label>
        <br>
  
        <label>郵便番号:</label>
        <input type="text" name="post_first" id="post_first">
        -
        <input type="text" name="post_last" id="post_last">
        <br>
        
        <label>都道府県:</label>
        <select name="prefecture" id="prefecture" size=1>
          <option id="--" value="--">--</option>
          <option id="Tokyo" value="Tokyo">東京都</option>
          <option id="Saitama" value="Saitama">埼玉県</option>
          <option id="Gunma" value="Gunma">群馬県</option>
        </select>
        <br>
        
        <label>メールアドレス:</label>
        <input type="text" name="mail_address" id="mail">
        <br>
        
        <label>趣味</label>
        <input type="checkbox" name="hobby" id="music" value="music"><label for="music">音楽鑑賞</label>
        <input type="checkbox" name="hobby" id="movie" value="movie"><label for="movie">映画鑑賞</label>
        <input type="checkbox" name="hobby" id="other" value="other"><label for="other">その他</label>
        <input type="text" name="other_descript" id="other_descript">
        <br>
        
        <label>ご意見</label>
        <textarea id="opinion" name="opinion"></textarea>
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
