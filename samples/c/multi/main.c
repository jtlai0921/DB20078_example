#include <stdio.h>
#include <mysql.h>

int main(int argc, char *argv[])
{
  int        i, next;
  MYSQL     *conn;    // MySQL 伺服器連線
  MYSQL_RES *result;  // 儲存 SELECT 查詢結果
  MYSQL_ROW row;      // 一筆資料

  // 連到 MySQL 伺服器
  conn = mysql_init(NULL);
  mysql_options(conn, MYSQL_READ_DEFAULT_GROUP, "");
  if(mysql_real_connect(
        conn, "localhost", "root", "uranus", 
        "mylibrary", 0, NULL, 0) == NULL) {
      fprintf(stderr, "抱歉，無法連上資料庫伺服器 ...\n");
      return 1;
    }
  mysql_set_server_option(conn, MYSQL_OPTION_MULTI_STATEMENTS_ON);

  // 如果要以 UTF-8 顯示資料的話才需要
  mysql_query(conn, "SET NAMES 'utf8'");

  // 執行幾個 SQL 指令
  const char *sql="SELECT * FROM categories LIMIT 5;\
                   INSERT INTO categories (catName) VALUES ('test1'), ('test2');\
                   SELECT 1+2;\
                   DELETE FROM categories WHERE catName LIKE 'test%';\
                   DROP TABLE IF EXISTS dummy";
  if(mysql_query(conn, sql)) {
    fprintf(stderr, "MySQL 錯誤: %s\n", mysql_error(conn));
    fprintf(stderr, "MySQL 錯誤編號: %i\n", mysql_errno(conn));
  }

  do  // 處理每一組查詢結果 (while(!mysql_next_result)
  {
    printf("\n-----------------------------------------------\n\n");
    printf("變動的資料筆數: %i\n", mysql_affected_rows(conn));
    if(mysql_warning_count(conn))
      fprintf(stderr, "MySQL 警告訊息: %i\n", mysql_warning_count(conn));

    result= mysql_store_result(conn); 
    if(result) {
      // 逐筆處理所有資料
      while((row = mysql_fetch_row(result)) != NULL) {
        printf("結果: ");
        for(i=0; i < mysql_num_fields(result); i++) {
          if(row[i] == NULL)
            printf("[NULL]\t");
          else
            printf("%s\t", row[i]);
        }
        printf("\n");
      }
      mysql_free_result(result);
    } else
      printf("沒有查到資料\n");

    next = mysql_next_result(conn);
    if(next>0) {
      printf("\n-----------------------------------------------\n\n");
      printf("mysql_next_result 錯誤編號: %i\n", next);
      if(mysql_errno(conn))
	fprintf(stderr, "MySQL 錯誤: %s\n", mysql_error(conn));
    }
  } while (!next);

  // 釋放記憶體，關閉連線
  mysql_close(conn);
  return 0;
}

