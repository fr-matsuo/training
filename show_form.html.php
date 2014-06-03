<!DOCTYPE html>

<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Style-Type" content="text/css">
  <link rel="stylesheet" type="text/css" href="common.css">
  <link rel="stylesheet" type="text/css" href="show_form.css">
  <title>フォーム表示</title>
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
