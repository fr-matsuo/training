<!DOCTYPE html>

<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" type="text/css" href="common.css">
  <link rel="stylesheet" type="text/css" href="show_form.css">
  <title>フォーム表示</title>

  <script type='text/javascript'>
  <!--
      function jumpPage(next_index) {
          document.write(next_index);

          var form = document.createElement('form');
          form.setAttribute('action', 'show_form.php');
          form.setAttribute('method', 'post');
          form.style.display = 'none';
          document.body.appendChild(form);

          var input = document.createElement('input');
          input.setAttribute('type', 'hidden');
          input.setAttribute('name', 'page_num');
          input.setAttribute('value', next_index);
          form.appendChild(input);

          form.submit();
      }
  -->
  </script>
</head>

<body>
  <header>
    <h1>フォーム表示</h1>
  </header>

  <nav>
    <?php showTable($column_list, $record_list); ?>
    <?php showPagingNavi($record_num); ?>
  </nav>

  <footer>
    <p>Copyright 2014</p>
  </footer>
</body>	
</html>
