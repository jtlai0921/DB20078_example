#include <stdio.h>
#include <mysql.h>

int main(int argc, char *argv[])
{
  MYSQL      *conn;      // MySQL 伺服器連線
  MYSQL_STMT *stmt;      // 預備敘述
  char       *cmd = 
    "SELECT title, titleID, catID, ts \
     FROM titles ORDER BY RAND() LIMIT 5";

  MYSQL_BIND    bind[4]; // 查詢結果的欄位
  char          title_buf[256];
  unsigned long title_len;
  int           titleID, catID;
  my_bool       catID_is_null;
  MYSQL_TIME    ts;

  int           err;     // mysql 相關函式的傳回值

  // 連到 MySQL 伺服器
  conn = mysql_init(NULL);
  mysql_options(conn, MYSQL_READ_DEFAULT_FILE, "");
  if(mysql_real_connect(
        conn, "localhost", "root", "uranus",
        "mylibrary", 0, NULL, 0) == NULL) {
      fprintf(stderr, "抱歉，無法連上資料庫伺服器 ...\n");
      return 1;
    }

  // 如果要以 UTF-8 顯示資料的話才需要
  mysql_query(conn, "SET NAMES 'utf8'");

  // 建立敘述
  stmt = mysql_stmt_init(conn);

  // 預備敘述
  mysql_stmt_prepare(stmt, cmd, strlen(cmd));

  // 定義查詢結果的欄位
  memset(bind, 0, sizeof(bind));
  bind[0].buffer_type = FIELD_TYPE_VAR_STRING; // title
  bind[0].buffer = title_buf;
  bind[0].buffer_length = 256;
  bind[0].length = &title_len;

  bind[1].buffer_type = FIELD_TYPE_LONG;      // titleID
  bind[1].buffer =  (gptr) &titleID;

  bind[2].buffer_type = FIELD_TYPE_LONG;      // catID
  bind[2].buffer =  (gptr) &catID;
  bind[2].is_null = &catID_is_null;

  bind[3].buffer_type = FIELD_TYPE_TIMESTAMP; // ts
  bind[3].buffer = (gptr) &ts;

  // 建立欄位連結
  mysql_stmt_bind_result(stmt, bind);

  // 執行敘述
  err = mysql_stmt_execute(stmt);
  if(err) {
    fprintf(stderr, "抱歉，遇到錯誤 ...\n");
    return 1;  }

  // 拿回所有查詢結果
  mysql_stmt_store_result(stmt);

  // 逐筆處理所有資料
  while(!mysql_stmt_fetch(stmt)) {

    printf("titleID=%d \t", titleID);

    if(catID_is_null)
      printf("catID=NULL \t");
    else
      printf("catID=%d \t", catID);
    printf("timestamp=%d-%02d-%02d %02d-%02d-%02d\t", 
	   ts.year, ts.month, ts.day, 
	   ts.hour, ts.minute, ts.second);
    printf("title=%s\n", title_buf);
  }

// 關閉敘述、連線
  mysql_stmt_close(stmt);
  mysql_close(conn);
  return 0;
}
