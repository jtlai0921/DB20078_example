<?php header("Content-Type: text/html; charset=utf-8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<link href="form.css" rel="stylesheet" type="text/css">
<link href="resulttable.css" rel="stylesheet" type="text/css">
<title>mylibrary 搜尋</title>
</head><body>

<?php

require_once 'mydb.php';
require_once 'mylibraryfunctions.php';
require_once 'formfunctions.php';

// 連到 MySQL 伺服器
$db = new MyDb();

// 每頁顯示的項目上限
$pagesize = 10;

// URL 資料
// show_array($_REQUEST);
$titleID      = array_item($_REQUEST, 'titleID');
$authID       = array_item($_REQUEST, 'authID');
$catID        = array_item($_REQUEST, 'catID');
$authPattern  = urldecode(array_item($_REQUEST, 'authPattern'));
$titlePattern = urldecode(array_item($_REQUEST, 'titlePattern'));
$page         = array_item($_REQUEST, 'page');

// 檢查資料
if(!$page || $page<1 || !is_numeric($page))
  $page=1;
elseif($page>100)
  $page=100;
if(!is_numeric($catID))
  $catID = FALSE;
if(!is_numeric($authID))
  $authID = FALSE;
if(!is_numeric($titleID))
  $titleID = FALSE;

// 處理表單資料
$formdata = array_item($_POST, "form");
if(is_array($formdata)) {
  // 拿掉 magic quotes
  if(get_magic_quotes_gpc())
    while($i = each($formdata))
      $formdata[$i[0]] = stripslashes($i[1]);
  // show_array($formdata);
  $authPattern  = array_item($formdata, "author") . "%";
  if(!array_item($formdata, "btnAuthor")) {
    $catID        = array_item($formdata, "category");
    $titlePattern = array_item($formdata, "title") . "%"; }
}

// 開始搜尋標題/作者
// 注意: 如果同時指定 $titlePattern 與 $authPattern 的話
// 就會直接忽略 $authPattern
if($titlePattern || $titleID || $catID) {
  // 搜尋標題
  $sql = build_title_query($titlePattern, $titleID, $catID,
    $page, $pagesize);
  $rows = $db->queryObjectArray($sql);
  show_titles($rows, $pagesize);
  $query = "catID=$catID&titleID=$titleID&titlePattern=" .
    urlencode($titlePattern);
  show_page_links($page, $pagesize, sizeof($rows), $query);
  echo '<p><a href="find.php">返回搜尋表單</a></p>', "\n";
}
elseif($authPattern || $authID) {
  // 搜尋作者
  $sql = build_author_query($authPattern, $authID, $page, $pagesize);
  $rows = $db->queryObjectArray($sql);
  show_authors($rows, $pagesize);
  $query = "authID=$authID&authPattern=" . urlencode($authPattern);
  show_page_links($page, $pagesize, sizeof($rows), $query);
  echo '<p><a href="find.php">返回搜尋表單</a></p>', "\n";
}
else {
  // 沒事可做，顯示表單
  echo "<h1>搜尋 mylibrary 的書籍、作者</h1>\n";
  build_form();
  // 連到新增、類別頁面
  printf("<p><br />%s<br />%s</p>\n",
    build_href("titleform.php", "", "輸入新書資料"),
    build_href("categories.php", "", "編輯類別"));
}

// $db->statistics();

?>
</body></html>


<?php    // 函式

// 顯示查詢表單
function build_form($formdata=FALSE) {
  global $db;

  // 書籍表單
  form_start("find.php");
  form_new_line();
  form_label("標題開頭文字");
  form_text("title", array_item($formdata, "title"), 10, 10);
  form_end_line();

  form_new_line();
  form_label("只搜尋指定的類別");
  // 取得所有類別
  $sql = "SELECT catName, catID, parentCatID FROM categories ORDER BY catName";
  $rows = $db->queryObjectArray($sql);
  // 建立兩個陣列:
  //   subcats[catID] 包含 catID 所有子類別的 catID
  //   catNames[catID] 包含 catID 的 catName
  foreach($rows as $row) {
    $subcats[$row->parentCatID][] = $row->catID;
    $catNames[$row->catID] = $row->catName; }
  // 建立階層清單
  $rows = build_category_array($subcats[NULL], $subcats, $catNames);
  form_list("category", $rows , FALSE);
  form_end_line();

  form_new_line();
  form_label("");
  form_button("btnTitle", "搜尋書籍");
  form_end_line();

  // 作者表單
  form_new_line();
  form_label("作者姓名開頭文字");
  form_text("author", array_item($formdata, "author"), 10, 10);
  form_end_line();

  form_new_line();
  form_label("");
  form_button("btnAuthor", "搜尋作者");
  form_end_line();
  form_end();
}

// 建立查詢書籍標題的 SQL 字串
function build_title_query($pattern, $titleID, $catID, $page, $size) {
  global $db;
  $sql = "SELECT titleID, title FROM titles ";
  if($titleID)
    // 已經搞定了
    return $sql . "WHERE titleID=$titleID ORDER BY title";

  // 加入搜尋類別、標題的條件
  if($catID && $catID!="none") {
    $catsql = "SELECT catID, parentCatID FROM categories";
    $rows = $db->queryObjectArray($catsql);
    foreach($rows as $row)
      $subcats[$row->parentCatID][] = $row->catID;
    $cond1 = "catID IN (" . subcategory_list($subcats, $catID) . ") "; }
  else
    $cond1 = "TRUE";
  if($pattern)
    $cond2 = "title LIKE " . $db->sql_string($pattern) . " ";
  else
    $cond2 = "TRUE";
  $sql .= "WHERE " . $cond1 . " AND " . $cond2 .
    " ORDER BY title ";

  // 加上 LIMIT 子句
  $sql .= "LIMIT " . (($page-1) * $size) . "," . ($size + 1);
  return $sql;
}

// 傳回 $catID 以及所有子類別的逗號分隔字串
function subcategory_list($subcats, $catID) {
  $lst = $catID;
  if(array_key_exists($catID, $subcats))
    foreach($subcats[$catID] as $subCatID)
      $lst .= ", " . subcategory_list($subcats, $subCatID);
  return $lst;
}



// 建立查詢作者的 SQL 字串
function build_author_query($pattern, $authID, $page, $size) {
  global $db;

  $sql = "SELECT authID, authName FROM authors ";
  if($authID)
    // 已經搞定
    return $sql . "WHERE authID = $authID";
  else
    return $sql ."WHERE authName LIKE " . $db->sql_string($pattern) .
      " ORDER BY authNAME " .
      "LIMIT " . (($page-1) * $size) . "," . ($size + 1);
}

// $titles 是個物件陣列
// 每個物件都有 authId 與 authName 屬性
function show_titles($titles, $pagesize) {
  global $db;

  echo "<h1>搜尋結果</h1>\n";
  if(!$titles) {
    echo "<p>抱歉，找不到符合條件的書籍。</p>\n";
    return; }

  // 建立 titleID 的逗號分隔清單
  $items = min($pagesize, sizeof($titles));
  for($i=0; $i<$items; $i++)
    if($i==0)
      $titleIDs = $titles[$i]->titleID;
    else
      $titleIDs .= "," . $titles[$i]->titleID;

  // 取得所有書籍資料 (不包括作者資料)
  $sql =
    "SELECT titleID, title, subtitle, year, edition, isbn, " .
    "langName, catID, publName " .
    "FROM titles " .
    "  LEFT JOIN languages  ON titles.langID = languages.langID " .
    "  LEFT JOIN publishers ON titles.publID = publishers.publID " .
    "WHERE titleID IN ($titleIDs) " .
    "ORDER BY title";
  $titlerows = $db->queryObjectArray($sql);

  // 取得這些書籍的作者資料
  $sql =
    "SELECT authName, rel_title_author.authID, titleID " .
    "FROM authors, rel_title_author ".
    "WHERE authors.authID = rel_title_author.authID " .
    "  AND rel_title_author.titleID IN ($titleIDs) " .
    "ORDER BY authName";
  $rows = $db->queryObjectArray($sql);
  // 建立快速取得作者資訊的關聯陣列
  foreach($rows as $author)
    $authors[$author->titleID][] =
      array($author->authName, $author->authID);

  // 取得所有類別，以便顯示類別資料
  $sql = "SELECT catName, catID, parentCatID FROM categories";
  $rows = $db->queryObjectArray($sql);
  // 建立快速取得類別名稱、上層類別的關聯陣列
  foreach($rows as $cat) {
    $catNames[$cat->catID] = $cat->catName;
    $catParents[$cat->catID] = $cat->parentCatID; }

  // 以表格顯示所有搜尋到的書籍
  echo '<table class="resulttable">', "\n";
  foreach($titlerows as $title) {
    echo td1("標題:", "td1head");
    $html = htmlentities($title->title, ENT_QUOTES, "UTF-8") . " " .
      build_href("titleform.php", "editID=$title->titleID", "(edit)");
    echo td2asis($html, "td2head");
    if($title->subtitle)
      echo td1("副標題:"), td2($title->subtitle);

    // 顯示這本書的所有作者
    if(array_key_exists($title->titleID, $authors)) {
      $auth=0;
      foreach($authors[$title->titleID] as $author) {
        if($auth==0)
          echo td1("作者:");
        else
          echo td1("");
        echo td2url($author[0], "find.php?authID=$author[1]");
        $auth++;
      }
    }
    // echo "</td></tr>\n";

    // 顯示更多書籍資料
    if($title->catID)
      echo td1("類別:"),
        td2asis(build_cat_string($catNames, $catParents, $title->catID));
    if($title->publName)
      echo td1("出版社:"), td2($title->publName);
    if($title->isbn)
      echo td1("ISBN:"), td2($title->isbn);
    if($title->edition)
      echo td1("版號:"), td2($title->edition);
    if($title->langName)
      echo td1("語言:"), td2($title->langName);
    // 在下一本書籍之前留一段空白
    echo td1("", "tdinvisible"), td2asis("&nbsp;", "tdinvisible");
  }
  echo "</table>\n";
}

// 傳回一個字串，包含 $catID 以及所有上層類別的連結
function build_cat_string($catNames, $catParents, $catID) {
  $tmp = build_href("find.php", "catID=$catID", $catNames[$catID]);
  while($catParents[$catID] != NULL) {
    $catID = $catParents[$catID];
    $tmp = build_href("find.php", "catID=$catID", $catNames[$catID]) .
      " &rarr; " . $tmp; }
  return $tmp;
}

// $authors 是個物件陣列
// 每個物件都有 authId 與 authName 屬性
function show_authors($authors, $pagesize) {
  global $db;

  echo "<h1>搜尋結果</h1>\n";
  if(!$authors) {
    echo "<p>抱歉，找不到符合條件的作者。</p>\n";
    return; }

  // 建立逗號分隔的 authID 字串
  $items = min($pagesize, sizeof($authors));
  for($i=0; $i<$items; $i++)
    if($i==0)
      $authIDs = $authors[$i]->authID;
    else
      $authIDs .= "," . $authors[$i]->authID;

  // 取得這些作者所撰寫的書籍
  $sql = "SELECT title, rel_title_author.titleID, authID " .
    "FROM titles, rel_title_author ".
    "WHERE titles.titleID = rel_title_author.titleID " .
    "AND rel_title_author.authID IN ($authIDs) " .
    "ORDER BY title";
  $rows = $db->queryObjectArray($sql);

  // 處理所有作者，顯示每位作者的所有作品
  echo '<table class="resulttable">', "\n";
  for($i=0; $i<$items; $i++) {
    echo td1("作者:", "td1head"),
      td2($authors[$i]->authName, "td2head");
    $titles=0;
    foreach($rows as $row)
      if($authors[$i]->authID == $row->authID) {
        if($titles==0)
          echo td1("著作:");
        else
          echo td1("");
        echo td2url($row->title, "find.php?titleID=$row->titleID");
        $titles++;
      }
    // 在下一本書之前空一段距離
    echo td1("", "tdinvisible"), td2asis("&nbsp;", "tdinvisible");
  }
  echo "</table>\n";
}

// 輔助建立表格的函式
function td1($txt, $class="td1") {
  echo "<tr><td class=\"$class\">",
    htmlentities($txt, ENT_QUOTES, "UTF-8"), "</td>\n"; }
function td2($txt, $class="td2") {
  echo "<td class=\"$class\">",
    htmlentities($txt, ENT_QUOTES, "UTF-8"), "</td></tr>\n"; }
function td2asis($txt, $class="td2") {
  echo "<td class=\"$class\">$txt</td></tr>\n"; }
function td2url($txt, $url, $class="td2") {
  echo "<td class=\"$class\">",
    build_href($url, "", $txt), "</td></tr>\n"; }
function td2txturl($txt, $urltxt, $url, $class="td2") {
  echo "<td class=\"$class\">",
    htmlentities($txt, ENT_QUOTES, "UTF-8"), " ",
    build_href($url, "", $urltxt), "</td></tr>\n"; }

// 顯示翻頁連結
// $page     .. 目前頁數
// $pagesize .. 每頁的項目數
// $results  .. 搜尋結果數
function show_page_links($page, $pagesize, $results, $query) {
  if(($page==1 && $results<=$pagesize) || $results==0)
    // nothing to do
    return;
  echo "<p>跳到頁面: ";
  if($page>1) {
    for($i=1; $i<$page; $i++)
      echo build_href("find.php", $query . "&page=$i", $i), " ";
    echo "$page "; }
  if($results>$pagesize) {
    $nextpage = $page + 1;
    echo build_href("find.php", $query . "&page=$nextpage", $nextpage);
  }
  echo "</p>\n";
}

?>
