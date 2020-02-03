<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<title>PHP 程式設計, mysqli 範例</title>
</head><body>

<?php

// 取得連線資訊，建立連線
require_once 'password.php';
$mysqli = new mysqli($mysqlhost, $mysqluser, $mysqlpasswd, $mysqldb);

// 檢查連線是否成功
if(mysqli_connect_errno()) {
  echo "<p>抱歉，連線失敗！ ", mysqli_connect_error(), "</p>\n";
  exit();
}

// 預備具有參數的 SQL 指令
$stmt = $mysqli->prepare(
  "INSERT INTO titles (title, subtitle, langID) VALUES (?, ?, ?)");
$stmt->bind_param('ssi', $title, $subtitle, $langID);

// 重複執行指令
$title="new Linux title 1";
$subtitle="new subtitle 1";
$langID=1;
$stmt->execute();

$title="new MySQL title 2";
$subtitle="new subtitle 2";
$langID=2;
$stmt->execute();

// 釋放指令
$stmt->close();

// 組合 bind_param 與 bind_result 進行查詢
$stmt = $mysqli->prepare(
  "SELECT titleID, title FROM titles WHERE title LIKE ?");
$stmt->bind_param('s', $pattern);

$pattern="%Linux%";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($titleID, $title);
echo "<p></p>\n";
while($stmt->fetch())
  printf("<br />%d %s\n", $titleID, htmlspecialchars($title));

$pattern="%MySQL%";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($titleID, $title);
echo "<p></p>\n";
while($stmt->fetch())
  printf("<br />%d %s\n", $titleID, htmlspecialchars($title));
$stmt->close();

// 刪除稍早建立的測試資料
$mysqli->query("DELETE FROM titles WHERE title LIKE 'new %'");

// 關閉連線
$mysqli->close();

?>
</body></html>
