<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<title>mylibrary categories</title>
</head><body>

<?php

require_once 'mydb.php';
require_once 'mylibraryfunctions.php';

// 連到 MySQL 伺服器
$db = new MyDb();

// 讀取表單、URL 變數
$insertID      = array_item($_REQUEST, 'insertID');
$deleteID      = array_item($_REQUEST, 'deleteID');
$submitbutton  = array_item($_POST, 'submitbutton');
$subcategories = array_item($_POST, 'subcategories');

// 移除 magic quotes
if(get_magic_quotes_gpc())
  $subcategories = stripslashes($subcategories);

// 檢查是否指定了 deleteID
// 是的話就刪除指定類別、以及所有子類別
if($deleteID) {
  $sql = "SELECT COUNT(*) FROM categories WHERE catID='$deleteID'";
  if($db->querySingleItem($sql)==1) {
    $db->execute("START TRANSACTION");
    if(delete_category($deleteID)==-1)
      $db->execute("ROLLBACK");
    else
      $db->execute("COMMIT");
  }
}

// 檢查是否指定了 insertID
if($insertID) {
  $sql = "SELECT COUNT(*) FROM categories WHERE catID='$insertID'";
  $n = $db->querySingleItem($sql); }

// 如果有正確的 insertID，就將指定的類別顯示出來
// 並顯示新增類別的表單
if($insertID && $n==1) {
  echo "<h2>插入新類別</h2>\n";

  // 如果有輸入表單資料的話
  // 就將新類別存入資料庫
  if($subcategories) {
    $db->execute("START TRANSACTION");
    if(insert_new_categories($insertID, $subcategories))
      $db->execute("COMMIT");
    else
      $db->execute("ROLLBACK"); }

  print_category_entry_form($insertID);
}

// 否則顯示所有類別的階層清單
else {
  echo "<h2>選擇類別</h2>\n";
  echo "<p>點選連結以新增/刪除類別。</p>\n";

  // 查詢所有類別
  $sql = "SELECT catName, catID, parentCatID FROM categories ORDER BY catName";
  $rows = $db->queryObjectArray($sql);
  // 建立兩個陣列:
  //   subcats[catID] 包含 catID 的所有子類別 catID
  //   catNames[catID] 包含 catID 的 catName
  foreach($rows as $row) {
    $subcats[$row->parentCatID][] = $row->catID;
    $catNames[$row->catID] = $row->catName; }
  // 建立階層清單
  print_categories($subcats[NULL], $subcats, $catNames);

  // 連到輸入、搜尋表單
  printf("<p><br />%s<br />%s</p>\n",
    build_href("titleform.php", "", "輸入新書資料"),
    build_href("find.php", "", "搜尋書籍/作者"));
}

// $db->showStatistics();

?>
</body></html>


<?php    // 相關函式


// 搜尋 $rows[n]->parentCatID=$parentCatID
// 並顯示 $rows[n]->catName; 接著遞迴呼叫自己
function print_categories($catIDs, $subcats, $catNames) {
  echo "<ul>";
  foreach($catIDs as $catID) {
    printf("<li>%s (%s, %s, %s)</li>\n",
      htmlspecialchars($catNames[$catID]),
      build_href("categories.php", "insertID=$catID", "新增"),
      build_href("categories.php", "deleteID=$catID", "刪除"),
      build_href("find.php", "catID=$catID", "顯示書籍"));
    if(array_key_exists($catID, $subcats))
      print_categories($subcats[$catID], $subcats, $catNames);
  }
  echo "</ul>\n";
}

// 顯示目前類別、所有上層類別以及下一層類別
// 以及新增類別的表單
function print_category_entry_form($insertID) {
  global $db;

  // 查詢所有類別
  $sql  = "SELECT catName, catID, parentCatID " .
          "FROM categories ORDER BY catName";
  $rows = $db->queryObjectArray($sql);

  // 建立類別名稱、上層類別與下層類別的關聯陣列
  foreach($rows as $row) {
    $catNames[$row->catID] = $row->catName;
    $parents[$row->catID] = $row->parentCatID;
    $subcats[$row->parentCatID][] = $row->catID;   }

  // 建立 $insertID 的上層類別清單
  $catID = $insertID;
  while($parents[$catID]!=NULL) {
    $catID = $parents[$catID];
    $parentList[] = $catID;   }

  // 顯示所有上層類別 (從根類別開始顯示)
  if(isset($parentList))
    for($i=sizeof($parentList)-1; $i>=0; $i--)
      printf("<ul><li>%s</li>\n", htmlspecialchars($catNames[$parentList[$i]]));

  // 以粗體顯示選定的類別
  printf("<ul><li><b>%s</b></li>\n", htmlspecialchars($catNames[$insertID]));

  // 顯示選定類別目前的子類別 (一層) 以及「刪除」連結
  // 我們仍然使用上一個 SELECT 的查詢結果
  echo "<ul>";
  $subcat=0;
  if(array_key_exists($insertID, $subcats))
    foreach($subcats[$insertID] as $catID)
      printf("<li>%s (%s)</li>\n",
        htmlspecialchars($catNames[$catID]),
        build_href("categories.php",
          "insertID=$insertID&deleteID=$catID", "刪除"));
  else
    echo "(還沒有子類別)";
  echo "</ul>\n";

  // 關閉階層類別清單
  if(isset($parentList))
    echo str_repeat("</ul>", sizeof($parentList)+1), "\n";

  echo '<form method="post" action="categories.php?insertID=',
    $insertID, '">', "\n",
    "<p>為 <b>$catNames[$insertID]</b> 新增子類別。<br />",
    "您可以一次新增好幾個子類別，在中間以 ; 隔開即可。</p>\n",
    '<p><input name="subcategories" size="60" maxlength="80" />', "\n",
    '<input type="submit" value="OK" name="submitbutton" /></p>', "\n",
    "</form>\n";

  // 連回類別清單
  echo "<p>回到完整的 ",
    build_href("categories.php", "", "類別清單") . ".\n";
}

// 為指定的類別新增子類別
function insert_new_categories($insertID, $subcategories) {
  global $db;
  $subcatarray = explode(";", $subcategories);
  $count = 0;
  foreach($subcatarray as $newcatname) {
    $result = insert_new_category($insertID, trim($newcatname));
    if($result == -1) {
      echo "<p>抱歉，中途發生問題，因此沒有修改任何資料。</p>\n";
      return FALSE; }
    elseif($result)
      $count++;
  }
  if($count)
    echo "<p>成功新增了 $count 個類別。</p>\n";
  return TRUE;
}

// 將新類別加到 categories 資料表內
// 傳回 -1 代表遇到錯誤
//       1 代表儲存完成
//       0 代表無法儲存
function insert_new_category($insertID, $newcatName) {
  global $db;
  // 檢查 newcatName 是否為空字串
  if(!$newcatName) return 0;
  $newcatName = $db->sql_string($newcatName);

  // 檢查 newcatName 是否已經出現過
  $sql = "SELECT COUNT(*) FROM categories " .
         "WHERE parentCatID=$insertID " .
         "  AND catName=$newcatName";
  if($db->querySingleItem($sql)>0) {
    return 0; }

  // 新增類別
  $sql = "INSERT INTO categories (catName, parentCatID) " .
         "VALUES ($newcatName, $insertID)";
  if($db->execute($sql))
    return 1;
  else
    return -1;
}

// 刪除類別
// 傳回 1 代表已刪除指定的類別與其子類別
//      0 代表無法刪除類別
//     -1 代表遇到問題
function delete_category($catID) {
  // 尋找 catID 的子類別
  // 並遞迴呼叫 delete_category 刪除這些類別
  global $db;
  $sql = "SELECT catID FROM categories " .
         "WHERE parentCatID='$catID'";
  if($rows = $db->queryObjectArray($sql)) {
    $deletedRows = 0;
    foreach($rows as $row) {
      $result = delete_category($row->catID);
      if($result==-1)
        return -1;
      else
        $deletedRows++;
    }
    // 如果無法刪除子類別的話，就不要刪除這個類別
    if($deletedRows != count($rows))
      return 0;
  }

  // 刪除 catID
  // 不刪除 catIDs<=11
  if($catID<=11) {
    echo "<br />您不能刪除這個範例內 catID&lt;=11 的類別。\n";
    return 0;
  }

  // 如果仍有書籍屬於這個類別的話，也不刪除
  $sql = "SELECT COUNT(*) FROM titles WHERE catID='$catID'";
  if($n = $db->querySingleItem($sql)>0) {
    $sql = "SELECT catName FROM categories WHERE catID='$catID'";
    $catname = $db->querySingleItem($sql);
    printf("<br />屬於 %s 類別的書籍有 %d 本。" .
           "您不能刪除這個類別\n", $catname, $n);
    return 0;
  }

  // 刪除類別
  $sql = "DELETE FROM categories WHERE catID='$catID' LIMIT 1";
  if($db->execute($sql))
    return 1;
  else
    return -1;
}

?>
