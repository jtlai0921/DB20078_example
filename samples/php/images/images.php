<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<link href="table.css" rel="stylesheet" type="text/css">
<title>處理圖片</title>
</head><body>

<?php   // images/images.php

include("connect.php");
$mysqli = connect_to_picdb();

// part 1: 儲存上傳的圖片 
$submitbtn = array_item($_POST,  'submitbtn');
$descr =     array_item($_POST,  'descr');
$imgfile =   array_item($_FILES, 'imgfile');

// 需要處理表單資料否?
if($submitbtn == 'OK' and is_array($imgfile)) {
  $name    = $imgfile['name'];
  $type    = $imgfile['type'];
  $size    = $imgfile['size'];
  $uperr   = array_item($imgfile, 'error');
  $tmpfile = $imgfile['tmp_name'];
  if(!$descr) $descr = $name;
  switch ($type) {
  case "image/gif":
    $mime = "GIF Image";  break;
  case "image/jpeg":
  case "image/pjpeg":
    $mime = "JPEG Image"; break;
  case "image/png":
  case "image/x-png":
    $mime = "PNG Image";  break;
  default:
    $mime = "unknown";
  }
  if(!$tmpfile or $uperr or $mime == "unknown" or !is_uploaded_file($tmpfile))
    echo "<p>處理表單資料的時候發生問題:
          或許您忘記指定圖檔、或是圖檔太大、也有可能無法判別圖檔格式。</p>\n"; 
  else {
    // 讀取上傳的檔案並存入資料庫
    $file = fopen($tmpfile, "rb");
    $imgdata = fread($file, $size);
    fclose($file);
    if(!$mysqli->query(
        "INSERT INTO images (name, type, image) " .
        "VALUES ('" . $mysqli->escape_string($descr) . "', " .
        "        '$mime', " .
        "        '" . $mysqli->escape_string($imgdata) . "')"))
      printf("<p>無法儲存圖片: %s</p>\n", $mysqli->error);
  }
}

// part 2: 顯示圖片
echo "<h2>最近上傳的圖片 ...</h2>\n";
$sql =
  "SELECT id, name, " .
  "DATE_FORMAT(ts, '%Y/%c/%e %k:%i') AS dt " .
  "FROM images ORDER BY ts DESC LIMIT 10";
$result = $mysqli->query($sql);
if($result->num_rows==0)
  echo "<p>資料庫裡面還沒有圖片 ...</p>\n";
else {
  while($row = $result->fetch_object())
    $rows[] = $row;
  echo '<table>', "\n<tr>";
  for($i=0; $i<sizeof($rows); $i++)  // 圖片
    echo '<th>',
      "<img src=\"showpic.php?id=" .
      $rows[$i]->id . "\" /></th>";
  echo "</tr>\n<tr>";
  for($i=0; $i<sizeof($rows); $i++)  // 名稱、說明
    echo "<td>" . htmlspecialchars($rows[$i]->name) . "</td>";
  echo "</tr>\n<tr>";
  for($i=0; $i<sizeof($rows); $i++)  // 日期時間
    echo "<td>" . $rows[$i]->dt . "</td>";
  echo "</tr>\n</table>\n";
}

// part 3: 上傳用表單
?>

<h2>上傳圖片</h2>

<p>檔案大小必須在 200 kB 以內，可上傳 PNG, JPEG 與 GIF 檔。</p>

<table>
<form method="post" action="images.php" enctype="multipart/form-data">
  <input type="hidden" value="204800" name="MAX_FILE_SIZE" />
  <tr><td>說明文字 (可省略):</td>
       <td><input name="descr" type="text" /></td></tr>
  <tr><td>圖檔:</td>
       <td><input name="imgfile" type="file" /></td></tr>
  <tr><td></td>
       <td><input type="submit" value="OK" name="submitbtn" /></td></tr>
</form>
</table>

</body></html>
