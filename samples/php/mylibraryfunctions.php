<?php  // mylibrary 範例的輔助函式
       // categories.php, titleform.php, search.php 會用到

// 檢查陣列元素是否存在，並傳回其值
function array_item($ar, $key) {
  if(is_array($ar) && array_key_exists($key, $ar))
    return($ar[$key]);
  else
    return FALSE;
}

// 建立 <a href=$url?$query>$txt</a>
function build_href($url, $query, $txt) {
  if($query)
    return "<a href=\"$url?" . $query . "\">" . htmlspecialchars($txt) . "</a>";
  else
    return "<a href=\"$url\">" . htmlspecialchars($txt) . "</a>";
}

// 顯示 PHP 陣列的內容，如 show_array($_POST)
// 方便檢查資料傳遞是否正確
function show_array($x)
{
  if(!is_array($x)) return;
  reset($x);
  echo "<p><font color=\"#00ff00\">陣列內容\n";
  if($x)
    while($i=each($x))
      echo "<br />", htmlspecialchars($i[0]), " = ", htmlspecialchars($i[1]), "\n";
  echo "</font></p>\n";
}



// 以二維陣列根據邏輯順序傳回所有類別
// result[n][0] --> catName (根據層級內縮)
// result[n][1] --> catID
function build_category_array($catIDs, $subcats, $catNames, $indent=0) {
  static $tmp;
  if($indent==0)
    $tmp = FALSE;  // unset 無法處理靜態變數!
  foreach($catIDs as $catID) {
    $pair[0] = str_repeat(" ", $indent*3) . $catNames[$catID];
    $pair[1] = $catID;
    $tmp[] = $pair;
    if(array_key_exists($catID, $subcats))
      build_category_array($subcats[$catID], $subcats, $catNames, $indent+1);
  }
  if($indent==0)
    return $tmp;
}

?>
