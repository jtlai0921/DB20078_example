<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<link href="form.css" rel="stylesheet" type="text/css">
<link href="resulttable.css" rel="stylesheet" type="text/css">
<title>速度最佳化</title>
</head><body>

<?php

require_once '../mydb.php';
// require_once '../mylibraryfunctions.php';
require_once '../formfunctions.php';

// 連到 MySQL
$db = new MyDb();

// 取得類別數量
$sql = "SELECT COUNT(*) FROM categories";
$n = $db->querySingleItem($sql);
echo "<p>categories 資料表擁有 $n 筆資料</p>\n";

// 作法 1: mylibraryfunctions.php 的原始程式碼
echo "<hr /><p></p>\n";
$db->resetStatistics();
$sql = "SELECT catName, catID, parentCatID FROM categories ORDER BY catName";
$categories = $db->queryObjectArray($sql);
$rows = build_category_array1($categories);
$db->showStatistics();

form_start("find.php");
form_new_line();
form_label("Test 1");
form_list("category", $rows , FALSE);
form_end_line();
form_end();

// 作法 2: 小查詢
echo "<hr /><p></p>\n";
$db->resetStatistics();
$rows = build_category_array2();
$db->showStatistics();

form_start("find.php");
form_new_line();
form_label("Test 2");
form_list("category", $rows , FALSE);
form_end_line();
form_end();

// 作法 3: 大查詢，改進過的 PHP 程式碼
echo "<hr /><p></p>\n";
$db->resetStatistics();
// 查詢所有類別
$sql  = "SELECT catName, catID, parentCatID FROM categories ORDER BY catName";
$rows = $db->queryObjectArray($sql);
// 建立兩個陣列:
//   subcats[catID] 包含 catID 的所有子類別編號
//   catNames[catID] 包含 catID 的類別名稱
foreach($rows as $row) {
  $subcats[$row->parentCatID][] = $row->catID;
  $catNames[$row->catID] = $row->catName; }
$rows = build_category_array3($subcats[NULL], $subcats, $catNames);
$db->showStatistics();

form_start("find.php");
form_new_line();
form_label("Test 3");
form_list("category", $rows , FALSE);
form_end_line();
form_end();

echo '<p>Links:';
echo '<br /><a href="remove_categories_test.php">remove_categories_test.php</a>';
echo "</p>\n";

?>
</body></html>

<?php

// 作法 1
function build_category_array1($rows, $parentCatID=NULL, $indent=0) {
  static $tmp;
  if($indent==NULL)
    $tmp=FALSE;  // unset 無法處理靜態變數!
  foreach($rows as $row)
    if($row->parentCatID==$parentCatID) {
      $pair[0] = str_repeat(" ", $indent*3) . $row->catName;
      $pair[1] = $row->catID;
      $tmp[] = $pair;
      build_category_array1($rows, $row->catID, $indent+1);
    }
  if($indent==NULL)
    return $tmp;
}

// 作法 2
function build_category_array2($parentCatID=NULL, $indent=0) {
  global $db;
  static $tmp;
  if($parentCatID==NULL) {
    $tmp = FALSE;  // unset 無法處理靜態變數!
    $sql = "SELECT catName, catID FROM categories " .
           "WHERE ISNULL(parentCatID) ORDER BY catName"; }
  else
    $sql = "SELECT catName, catID FROM categories " .
           "WHERE parentCatID=$parentCatID ORDER BY catName";
  if($rows = $db->queryObjectArray($sql))
    foreach($rows as $row) {
      $pair[0] = str_repeat(" ", $indent*3) . $row->catName;
      $pair[1] = $row->catID;
      $tmp[] = $pair;
      build_category_array2($row->catID, $indent+1);
    }
  if($parentCatID==NULL)
    return $tmp;
}


// 作法 3
function build_category_array3($catIDs, $subcats, $catNames, $indent=0) {
  static $tmp;
  if($indent==0)
    $tmp = FALSE;  // unset 無法處理靜態變數!
  foreach($catIDs as $catID) {
    $pair[0] = str_repeat(" ", $indent*3) . $catNames[$catID];
    $pair[1] = $catID;
    $tmp[] = $pair;
    if(array_key_exists($catID, $subcats))
      build_category_array3($subcats[$catID], $subcats, $catNames, $indent+1);
  }
  if($indent==0)
    return $tmp;
}

?>
