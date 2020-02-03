<?php

header('Content-Type: text/html; charset=utf-8');

require_once '../mydb.php';
$db = new MyDb();
$backup_exists=FALSE;
$categories_exists=FALSE;

$sql = "SHOW TABLES like 'oldcategories'";
$rows = $db->queryArray($sql);
if($rows && sizeof($rows)==1)
  $backup_exists=TRUE;
$sql = "SHOW TABLES like 'categories'";
$rows = $db->queryArray($sql);
if($rows && sizeof($rows)==1)
  $categories_exists=TRUE;

if($categories_exists && !$backup_exists) {
  // 如果沒有備份的話，就修改類別資料表的名稱
  $sql = "ALTER TABLE titles DROP FOREIGN KEY titles_ibfk_3";
  $db->execute($sql);
  $sql = "RENAME TABLE categories TO oldcategories";
  $db->execute($sql);

} elseif($categories_exists && $backup_exists) {
  // 有備份的話，就直接扔掉類別資料表
  $sql = "DROP TABLE categories";
  $db->execute($sql);
}

// 建立新的類別資料表
$sql = "CREATE TABLE categories ( " .
       "  catID       int NOT NULL auto_increment, " .
       "  catName     varchar(60) collate latin1_german1_ci NOT NULL default '', " .
       "  parentCatID int default NULL, " .
       "  indent      int, " .
       "  PRIMARY KEY  (catID), "  .
       "  KEY catName (catName), " .
       "  KEY parentCatID (parentCatID), " .
       "  CONSTRAINT categories_ibfk_1 FOREIGN KEY (parentCatID) " .
       "    REFERENCES categories (catID) )" .
       "ENGINE=InnoDB " .
       "DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci";
$db->execute($sql);

// 為每層類別產生隨機資料
// 陣列每項是一層類別
// 新增次數、每次新增的資料筆數
$no_of_items = array(array( 1, 30),  // 建立 30 個第一層類別
                     array(20, 10),  // 建立 20*10 個第二層類別
                     array(40,  6),  // ...
                     array(50,  5),
                     array(40,  4),
                     array(40,  3),
                     array(40,  3));

// 新增第一個項目 (第 0 層)
$sql = "INSERT INTO categories (catName, parentCatID, indent) " .
       "VALUES ('all books', NULL, 0)";
$db->execute($sql);

// 處理第 1 層到第 7 層
for($level=1; $level<=sizeof($no_of_items); $level++) {
  $iterations = $no_of_items[$level-1][0];
  $items      = $no_of_items[$level-1][1];

  // 取得所有上層分類
  $sql = "SELECT catID FROM categories WHERE indent=" . ($level-1) ;
  $parentcats = $db->queryArray($sql);
  $no_parents = sizeof($parentcats);

  // 處理每次新增資料
  for($n=0; $n<$iterations; $n++) {

    // 隨機選取一個上層類別，將新類別加在下面
    $catID = $parentcats[rand(0, $no_parents-1)][0];

    // 產生插入 $realitems 個新類別的字串
    $realitems = $items * 0.666 + rand(0, $items * 0.666);
    $newcats = "";
    for($i=0; $i<$realitems; $i++) {
      $newcats .= random_string(10 + rand(0, 19)) . ";";
    }

    // 新增類別
    insert_new_categories($catID, $newcats, $level);
  }
}

// 丟掉 indent 欄位 (只在新增時需要)
$sql = "ALTER TABLE categories DROP indent";
$db->execute($sql);

// 取得類別數量
$sql = "SELECT COUNT(*) FROM categories";
$n = $db->querySingleItem($sql);
echo "<p>新資料表包含 $n 個類別。</p>\n";

echo '<p>連結:';
echo '<br /><a href="show_categories_list.php">show_categories_list.php</a>';
echo '<br /><a href="remove_categories_test.php">remove_categories_test.php</a>';
echo "</p>\n";

// 將新類別新增到指定的類別之下
function insert_new_categories($insertID, $subcategories, $indent) {
  global $db;
  $subcatarray = explode(";", $subcategories);
  $count = 0;
  foreach($subcatarray as $newcatname) {
    $result = insert_new_category($insertID, trim($newcatname), $indent);
    if($result == -1) {
      echo "<p>抱歉，發生錯誤，無法儲存資料。</p>\n";
      return FALSE; }
    elseif($result)
      $count++;
  }
  return TRUE;
}

// 將新類別加到 categories 資料表內
// 傳回 -1 代表遇到錯誤
//       1 代表儲存完成
//       0 代表無法儲存
function insert_new_category($insertID, $newcatName, $indent) {
  global $db;
  // 檢查 newcatName 是否為空字串
  if(!$newcatName) return 0;
  $newcatName = $db->sql_string($newcatName);

  // 新增類別
  $sql = "INSERT INTO categories (catName, parentCatID, indent) " .
         "VALUES ($newcatName, $insertID, $indent)";
  if($db->execute($sql))
    return 1;
  else
    return -1;
}

// 傳回 $n 個字元的隨機字串
function random_string($n) {
  $tmp = "";
  for($i=0; $i<$n; $i++)
    $tmp .= chr(rand(97, 97+24));
  return $tmp;
}


?>
