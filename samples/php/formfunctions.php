<?php  // 輔助顯示表單的函式
       // categories.php, titleform.php, search.php 都有用到這些功能

// 表單開頭，以表格排列表單內容
function form_start($action) {
  echo '<table class="myformtable">', "\n";
  echo '<form method="post" ',
    html_attribute("action", $action), ">\n"; }

// 表單結尾
function form_end() {
  echo "</form></table>\n\n"; }

// 表格新增一列
function form_new_line() {
  echo "<tr>"; }

// 表格一列的結尾
function form_end_line() {
  echo "</tr>\n\n"; }

// 在格子內靠右顯示文字
function form_label($caption, $necessary=FALSE) {
  echo '<td align="right" class="myformtd">';
  if($necessary)
    echo '<span class="red">', htmlspecialchars($caption), '</span>';
  else
    echo htmlspecialchars($caption);
  echo '</td>', "\n";
}

// 在格子內顯示文字
function form_caption($caption, $colspan=1) {
  if($colspan>1)
    echo '<td class="myformtd" ',
      html_attribute("colspan", $colspan), '>';
  else
    echo '<td class="myformtd">';
  echo htmlspecialchars($caption), '</td>', "\n";
}

// 在格子內照樣顯示文字
function form_asis($txt, $colspan=1) {
  if($colspan>1)
    echo '<td class="myformtd" ',
      html_attribute("colspan", $colspan), '>';
  else
    echo '<td class="myformtd">';
  echo $txt, '</td>', "\n";
}

// 在格子內顯示 URL
function form_url($url, $txt, $colspan=1) {
  if($colspan>1)
    echo '<td class="myformtd" ',
      html_attribute("colspan", $colspan), '>';
  else
    echo '<td class="myformtd">';
  echo "<a href=\"$url\">" . htmlspecialchars($txt) . "</a></td>\n";
}

// 在表單內儲存隱藏資料
function form_hidden($name, $value) {
  echo '<input type="hidden" ',
    html_attribute("name", "form[$name]"),
    html_attribute("value", $value),
    " />\n";
}

// 建立 $n 個空格子
function form_empty_cell($n=1) {
  echo str_repeat('<td class="myformtd">&nbsp;</td>', $n) . "\n";
}

// 建立文字輸入欄位
function form_text($name, $value, $size=40, $maxlength=40, $colspan=1) {
  if($colspan>1)
    echo '<td class="myformtd" ',
      html_attribute("colspan", $colspan), '>';
  else
    echo '<td class="myformtd"> ';
  echo '<input class="mycontrol" ',
    html_attribute("name", "form[$name]"),
    html_attribute("size", $size),
    html_attribute("maxlength", $maxlength);
  if($value)
    echo html_attribute("value", $value);
  echo ' /></td>', "\n";
}

// 建立密碼輸入欄位
function form_password($name, $value, $size=40, $maxlength=40, $colspan=1) {
  if($colspan>1)
    echo '<td class="myformtd" ',
      html_attribute("colspan", $colspan), '>';
  else
    echo '<td class="myformtd"> ';
  echo '<input class="mycontrol" ',
    html_attribute("type", "password"),
    html_attribute("name", "form[$name]"),
    html_attribute("size", $size),
    html_attribute("maxlength", $maxlength);
  if($value)
    echo html_attribute("value", $value);
  echo ' /></td>', "\n";
}

// 建立文字輸入欄位
function form_textarea($name, $value, $cols=70, $rows=6, $colspan=1) {
  if($colspan>1)
    echo '<td class="myformtd" ',
      html_attribute("colspan", $colspan), '>';
  else
    echo '<td class="myformtd"> ';
  echo '<textarea class="mycontrol" ',
    html_attribute("name", "form[$name]"),
    html_attribute("rows", $rows),
    html_attribute("cols", $cols), '>';
  if($value)
    echo htmlspecialchars($value);
  echo '</textarea></td>', "\n";
}

// 建立下拉選單
function form_list($name, $rows, $selected=-1) {
  echo '<td class="myformtd">';
  echo '<select class="mycontrol" ',
    html_attribute("name", "form[$name]"), '>', "\n";
  echo '<option value="none">(請選擇)</option>';
  foreach($rows as $row) {
    echo '<option ', html_attribute("value", $row[1]);
    if($selected==$row[1])
      echo 'selected="selected" ';
    $listentry = str_replace(" ", "&nbsp;", htmlspecialchars($row[0]));
    echo ">$listentry</option>\n";
  }
  echo '</select></td>', "\n";
}

// 建立表單按鈕
function form_button($name, $txt, $type="submit") {
  echo '<td class="myformtd"><input ',
    html_attribute("class", "mybutton"),
    html_attribute("type", $type),
    html_attribute("value", $txt),
    html_attribute("name", "form[$name]"),
    ' /></td>', "\n";
}

// 產生 name="value"
function html_attribute($name, $value) {
  return $name . '="' . htmlspecialchars($value) . '" ';
}

// 顯示紅色錯誤訊息
function show_error_msg($txt) {
  echo '<p><span class="red">', htmlspecialchars($txt), '</span></p>', "\n";
}


 ?>
