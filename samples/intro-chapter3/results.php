<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">
<!-- php/vote/results.php -->
<html><head>
<meta http-equiv="Content-Type"
      content="text/html; charset=utf-8" />
<title>投票結果</title>
</head><body>
<h2>投票結果</h2>
<?php

  $mysqlhost="localhost";
  $mysqluser="root";
  $mysqlpasswd="";
  $mysqldbname="test_vote";

  // 建立資料庫連線
  $link =
    @mysql_connect($mysqlhost, $mysqluser, $mysqlpasswd);
  if ($link == FALSE) {
    echo "<p><b>不幸地，現在無法連上資料庫。
          因此現在沒辦法顯示投票結果，請稍後再試。</b></p>
          </body></html>\n";
    exit();
  }
  mysql_select_db($mysqldbname);

  // 如果有填寫問卷的話
  // 取回 + 儲存結果
  function array_item($ar, $key) {
    if(array_key_exists($key, $ar)) return($ar[$key]);
    return(''); }

  $submitbutton = array_item($_POST, 'submitbutton');
  $vote = array_item($_POST, 'vote');
  
  if($submitbutton=="OK") {
    if($vote>=1 && $vote<=6) {
      mysql_query(
        "INSERT INTO votelanguage (choice) VALUES ($vote)");
    }
    else {
      echo "<p> 選項不正確，請重新投一次。
            回到<a href=\"vote.html\">問卷</a>。</p>
            </body></html>\n";
      exit();
    }
  }
  
  // 顯示結果
  echo "<p><b>您最喜歡用來開發 MySQL
  應用程式的語言是什麼？</b></p>\n";

  // 總投票數
  $result =
    mysql_query("SELECT COUNT(choice) FROM votelanguage");
  $choice_count = mysql_result($result, 0, 0);
  
  // 各投票對象的得票率
  if($choice_count == 0) {
    echo "<p>還沒人投過票。</p>\n";
  }
  else {
    echo "<p>目前有 $choice_count 個人投了票:</p>\n";
    $choicetext = array("", "C/C++", "Java", "Perl", "PHP",
                        "ASP[.NET] / C# / VB[.NET] / VBA",
                        "其他語言");
    print("<p><table>\n");
    for($i=1; $i<=6; $i++) {
      $result = mysql_query(
        "SELECT COUNT(choice) FROM votelanguage " .
        "WHERE choice = $i");
      $choice[$i] = mysql_result($result, 0, 0);
      $percent = round($choice[$i]/$choice_count*10000)/100;
      print("<tr><td>$choicetext[$i]:</td>");
      print("<td>$percent %</td></tr>\n");
    }
    print("</table></p>\n");
  }
?>
</body>
</html>
