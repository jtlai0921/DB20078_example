#include <stdio.h>
#include <mysql.h>

int main(int argc, char *argv[])
{
  MYSQL *conn;        // MySQL 伺服器連線
  MYSQL_STMT *stmt;   // 預備敘述
  MYSQL_BIND bind[3]; // 參數

  char *insert = 
    "INSERT INTO titles (title, subtitle, langID) VALUES (?, ?, ?)";
  char title_buf[256];
  char subtitle_buf[256];
  unsigned long title_len, subtitle_len;
  int langID;
  my_bool langID_is_null;

  // 連到 MySQL 伺服器
  conn = mysql_init(NULL);
  mysql_options(conn, MYSQL_READ_DEFAULT_FILE, "");
  if(mysql_real_connect(
        conn, "localhost", "root", "uranus",
        "mylibrary", 0, NULL, 0) == NULL) {
      fprintf(stderr, "抱歉，無法連上資料庫伺服器 ...\n");
      return 1;
    }

  // 建立敘述
  stmt = mysql_stmt_init(conn);

  // 預備敘述
  mysql_stmt_prepare(stmt, insert, strlen(insert));

  // 定義參數
  memset(bind, 0, sizeof(bind));
  bind[0].buffer_type = FIELD_TYPE_VAR_STRING;
  bind[0].buffer = title_buf;
  bind[0].buffer_length = 256;
  bind[0].length = &title_len;

  bind[1].buffer_type = FIELD_TYPE_VAR_STRING;
  bind[1].buffer = subtitle_buf;
  bind[1].buffer_length = 256;
  bind[1].length = &subtitle_len;

  bind[2].buffer_type = FIELD_TYPE_LONG;
  bind[2].buffer = (gptr) &langID;
  bind[2].is_null = &langID_is_null;
  mysql_stmt_bind_param(stmt, bind);

  // 執行
  strcpy(title_buf, "title1");
  title_len = strlen(title_buf);
  strcpy(subtitle_buf, "test prepared statements");
  subtitle_len = strlen(subtitle_buf);
  langID_is_null = 0;
  langID=1;
  mysql_stmt_execute(stmt);
  printf("已新增 titleId=%d 的書籍\n", 
         (int) mysql_insert_id(conn));

  // 再執行一次
  strcpy(title_buf, "title2");
  title_len = strlen(title_buf);
  strcpy(subtitle_buf, "test prepared statements");
  subtitle_len = strlen(subtitle_buf);
  langID_is_null = 1;
  mysql_stmt_execute(stmt);
  printf("已新增 titleId=%d 的書籍\n", 
         (int) mysql_insert_id(conn));

  // 關閉預備敘述與連線
  mysql_stmt_close(stmt);
  mysql_close(conn);
  return 0;
}
