<?php

// 為了增加安全性，請將 password.php 移到無法透過網頁伺服器瀏覽的目錄內
// 並且修改 require_once 的引用路徑。

class MyDb {
  protected $mysqli;
  protected $showerror = TRUE;   // 設成 FALSE 就不會看到錯誤訊息
  protected $showsql   = FALSE;  // 設成 TRUE 就能看到所有 SQL 指令，方便除錯
  protected $sqlcounter = 0;     // SQL 指令數
  protected $rowcounter = 0;     // SELECT 傳回資料筆數
  protected $dbtime     = 0;     // 計算查詢時間的計時器
  protected $starttime;

  // 建構子
  function __construct() {
    require_once('password.php');
    $this->mysqli = @new mysqli($mysqlhost, $mysqluser, $mysqlpasswd, $mysqldb);
    // 檢查是否連線成功
    if(mysqli_connect_errno()) {
      $this->printerror("Sorry, no connection! (" . mysqli_connect_error() . ")");
      // 您或許需要在此加入結束頁面的 HTML 碼
      // (如 </body></html> 等等)
      $this->mysqli = FALSE;
      exit();
    }
    $this->execute('SET NAMES utf8');
    $this->starttime = $this->microtime_float();
  }

  // 解構子
  function __destruct() {
    $this->close();
   }

  // 明確關閉連線
  function close() {
    if($this->mysqli)
      $this->mysqli->close();
      $this->mysqli = FALSE;
  }

  function getMysqli() {
    return $this->mysqli; }

  // 執行 SELECT 查詢，傳回物件陣列
  function queryObjectArray($sql) {
    $this->sqlcounter++;
    $this->printsql($sql);
    $time1  = $this->microtime_float();
    $result = $this->mysqli->query($sql);
    $time2  = $this->microtime_float();
    $this->dbtime += ($time2 - $time1);
    if($result) {
      if($result->num_rows) {
        while($row = $result->fetch_object())
          $result_array[] = $row;
        $this->rowcounter += sizeof($result_array);
        return $result_array; }
      else
        return FALSE;
    } else {
      $this->printerror($this->mysqli->error);
      return FALSE;
    }
  }

  // 執行 SELECT 查詢，傳回一般陣列
  function queryArray($sql) {
    $this->sqlcounter++;
    $this->printsql($sql);
    $time1  = $this->microtime_float();
    $result = $this->mysqli->query($sql);
    $time2  = $this->microtime_float();
    $this->dbtime += ($time2 - $time1);
    if($result) {
      if($result->num_rows) {
        while($row = $result->fetch_array())
          $result_array[] = $row;
        $this->rowcounter += sizeof($result_array);
        return $result_array; }
      else
        return FALSE;
    } else {
      $this->printerror($this->mysqli->error);
      return FALSE;
    }
  }


  // 執行只傳回一個值的 SELECT 查詢指令
  // (如 SELECT COUNT(*) FROM table)
  // 並傳回查詢結果
  // 小心: 發生錯誤時，它會傳回 -1 而不是 0!
  function querySingleItem($sql) {
    $this->sqlcounter++;
    $this->printsql($sql);
    $time1  = $this->microtime_float();
    $result = $this->mysqli->query($sql);
    $time2  = $this->microtime_float();
    $this->dbtime += ($time2 - $time1);
    if($result) {
      if ($row=$result->fetch_array()) {
        $result->close();
        $this->rowcounter++;
        return $row[0];
      } else {
        // 沒有查到任何資料
        return -1;
      }
    } else {
      $this->printerror($this->mysqli->error);
      return -1;
    }
  }

  // 執行不會傳回資料的 SQL 指令
  function execute($sql) {
    $this->sqlcounter++;
    $this->printsql($sql);
    $time1  = $this->microtime_float();
    $result = $this->mysqli->real_query($sql);
    $time2  = $this->microtime_float();
    $this->dbtime += ($time2 - $time1);
    if($result)
      return TRUE;
    else {
      $this->printerror($this->mysqli->error);
      return FALSE;
    }
  }

  // 在 INSERT 之後取回 insert_id
  function insertId() {
    return $this->mysqli->insert_id; }

  // 在 ', " 等字元前面加上 \
  function escape($txt) {
    return trim($this->mysqli->escape_string($txt)); }

  // 傳回 'NULL' or '字串'
  function sql_string($txt) {
    if(!$txt || trim($txt)=="")
      return 'NULL';
    else
      return "'" . $this->escape(trim($txt)) . "'";  }

  function error() {
    return $this->mysqli->error; }

  private function printsql($sql) {
    if($this->showsql)
      printf("<p><font color=\"#0000ff\">%s</font></p>\n",
        htmlspecialchars($sql));    }

  private function printerror($txt) {
    if($this->showerror)
      printf("<p><font color=\"#ff0000\">%s</font></p>\n",
        htmlspecialchars($txt));  }

  function showStatistics() {
    $totalTime = $this->microtime_float() - $this->starttime;
    printf("<p><font color=\"#0000ff\">SQL 指令數: %d\n",
      $this->sqlcounter);
    printf("<br />傳回的資料筆數: %d\n",
      $this->rowcounter);
    printf("<br />查詢時間 (MySQL): %f\n",
      $this->dbtime);
    printf("<br />處理時間 (PHP): %f\n",
      $totalTime - $this->dbtime);
    printf("<br />MyDB 建立 / 重設之後的總時間: %f</font></p>\n",
      $totalTime);    }

  function resetStatistics() {
    $this->sqlcounter = 0;
    $this->rowcounter = 0;
    $this->dbtime     = 0;
    $this->starttime = $this->microtime_float();  }

  private function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec); }

}
