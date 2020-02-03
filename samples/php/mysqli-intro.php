<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<title>PHP 程式設計, mysqli 範例</title>
</head><body>

<?php

// 連線資訊
$mysqlhost   = "localhost";  // MySQL 伺服器主機名稱
$mysqluser   = "root";       // 使用者名稱
$mysqlpasswd = "uranus";     // 密碼
$mysqldb     = "mylibrary";
$mysqli      = new mysqli($mysqlhost, $mysqluser, $mysqlpasswd, $mysqldb);

// 檢查是否連線成功
if(mysqli_connect_errno()) {
  echo "<p>抱歉，無法連線！ ", mysqli_connect_error(), "</p>\n";
  exit();
}

// 查詢資料
if($result = $mysqli->query("SELECT * FROM titles")) {
  printf("<p>查詢結果筆數: %d</p>\n", $result->num_rows);
  printf("<p>欄位數: %d</p>\n", $result->field_count);

//   echo "<p>查詢結果相關資訊: ";
//   foreach($result->fetch_fields() as $meta)
//     printf("<br />Name=%s Table=%s Len=%d Decimals=%s Type=%s\n",
//       $meta->name, $meta->table, $meta->max_length, $meta->decimals, $meta->type);

  while($row = $result->fetch_assoc()){
    if($row["subtitle"]==NULL)
      printf("<br />%s\n", htmlspecialchars($row["title"]));
    else
      printf("<br />%s -- %s\n", htmlspecialchars($row["title"]),
        htmlspecialchars($row["subtitle"]));
  }

  // 釋放查詢結果
  $result->close();
}

// 關閉連線
$mysqli->close();

?>
</body></html>
