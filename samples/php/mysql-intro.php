<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<title>PHP 程式設計, mysql 範例</title>
</head><body>

<?php

$mysqlhost = "localhost";    // MySQL 伺服器主機名稱
$mysqluser = "root";         // 使用者名稱
$mysqlpasswd = "uranus";     // 密碼
$mysqldb   = "mylibrary";

if($conn = @mysql_connect($mysqlhost, $mysqluser, $mysqlpasswd)) {
  mysql_query("SET NAMES utf8");
  mysql_select_db("mylibrary");
  if($result=mysql_query("SELECT * FROM titles ORDER BY title")) {
    printf("<p>資料數: %d</p>\n", mysql_num_rows($result));
    printf("<p>欄位數: %d</p>\n",
      mysql_num_fields($result));
    while($row = mysql_fetch_object($result)) {
      if($row->subtitle)
        printf("<br />%s -- %s\n",
          htmlspecialchars($row->title), htmlspecialchars ($row->subtitle));
      else
        printf("<br />%s\n", htmlspecialchars($row->title));
    }
    mysql_free_result($result);
  }
} else {
  printf("<p>抱歉，無法連到 MySQL 伺服器! %s</p>\n",
    mysql_error());
}
mysql_close($conn);

?>
</body></html>
