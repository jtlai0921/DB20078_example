<?php {  // images/showpic.php?id=n
  include("connect.php");

  // 取得 ID，沒有 ID 的話就直接結束
  $id = array_item($_GET, 'id');
  if(!$id) exit;

  // 連到 MySQL 查詢圖片
  $mysqli = connect_to_picdb();
  $result = $mysqli->query(
    "SELECT image, type FROM images WHERE id = $id");
  if(!$result) exit;

  // 顯示圖片
  $row = $result->fetch_object();
  if(!$row) exit;
  header($row->type);
  echo $row->image;
} ?>

