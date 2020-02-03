<?php
// images/connect.php

// 連到 MySQL 並選用 'testimages' 資料庫;
function connect_to_picdb() {
  $mysqluser = "root";       // 使用者名稱
  $mysqlpw   = "uranus";     // 密碼
  $mysqlhost = "localhost";  // MySQL 伺服器主機名稱
  $mysqldb   = "testimages"; // 資料庫名稱

  $mysqli = new mysqli($mysqlhost, $mysqluser, $mysqlpw, $mysqldb);
  if(mysqli_connect_errno()) {
    echo "<p>Sorry, no connection to database ...</p></body></html>\n";
    exit();  }

  return $mysqli;
}

// 檢查陣列是否有指定的鍵值
// 並傳回它的內容
function array_item($ar, $key) {
  if(is_array($ar) && array_key_exists($key, $ar))
    return($ar[$key]); }

?>
