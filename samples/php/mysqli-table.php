<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<link href="table.css" rel="stylesheet" type="text/css">
<title>PHP Programming, mysqli sample</title>
</head><body>

<?php

// 以 HTML 表格顯示 SELECT 查詢結果
function show_table($result) {
  if(!$result) {
    echo "<p>No valid query result.</p>\n";
    return;
  }

  if($result->num_rows>0 && $result->field_count>0) {
    echo "<table>";

    // 欄位名稱
    echo "<tr>";
    foreach($result->fetch_fields() as $meta)
      printf("<th>%s</th>", htmlspecialchars($meta->name));
    echo "</tr>\n";

    // 內容
    while($row = $result->fetch_row()) {
      echo "<tr>";
      foreach($row as $col)
        printf("<td>%s</td>", htmlspecialchars($col));
      echo "</tr>\n";
    }
    echo "</table>\n";
  }
}

require_once 'password.php';

// 連到 MySQL 伺服器
$mysqli = new mysqli($mysqlhost, $mysqluser, $mysqlpasswd, $mysqldb);
if(mysqli_connect_errno()) {
  echo "<p>Sorry, no connection! ", mysqli_connect_error(), "</p>\n";
  exit();
}

// 以 show_table 顯示 SELECT 查詢結果
if($result = $mysqli->query("SELECT * FROM titles")) {
  show_table($result);
  $result->close();
}

// 斷線
$mysqli->close();

?>
</body></html>

<?php


?>
