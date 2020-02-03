<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<title>PHP Programming, mysqli sample</title>
</head><body>

<?php

require_once("mydb.php");

// 連線
$db = new MyDb();

// 查詢一個資料
if(($n = $db->querySingleItem("SELECT COUNT(*) FROM titles"))!=-1)
  printf("<p>titles 的資料筆數: %d</p>\n", $n);

// INSERT 資料
$db->execute("INSERT INTO titles (title, subtitle) VALUES ('test', 'subtest')");
$id=$db->insertId();
printf("<p>insertid=%d</p>\n", $id);

// SELECT 資料
if($result = $db->queryObjectArray("SELECT * FROM titles")) {
  foreach($result as $row)
    printf("<br />TitleID=%d Title=%s Subtitle=%s\n",
      $row->titleID, $row->title, $row->subtitle);
}

// DELETE 資料
$db->execute("DELETE FROM titles WHERE titleID=" . $id);

// 關閉連線
$db->close();

?>
</body></html>
