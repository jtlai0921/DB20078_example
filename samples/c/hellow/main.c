#include <stdio.h>
#include <mysql.h>

int main(int argc, char *argv[])
{
  int i;
  MYSQL *conn;        // MySQL 伺服器連線
  MYSQL_RES *result;  // SELECT 查詢結果
  MYSQL_ROW row;      // 一筆資料

  // 連到 MySQL 伺服器
  conn = mysql_init(NULL);
  // mysql_options(conn, MYSQL_READ_DEFAULT_GROUP, "myclient");
  if(mysql_real_connect(
        conn, "localhost", "root", "uranus", 
        "mylibrary", 0, NULL, 0) == NULL) {
      fprintf(stderr, "抱歉，無法建立連線 ...\n");
      return 1;
    }

  // 只在想以 UTF-8 顯示資料時需要
  mysql_query(conn, "SET NAMES 'utf8'");

  // 取回 mylibrary 資料庫內的出版社
  const char *sql="SELECT COUNT(titleID), publName \
                   FROM publishers, titles \
                   WHERE publishers.publID = titles.publID  \
                   GROUP BY publishers.publID \
                   ORDER BY publName";
  if(mysql_query(conn, sql)) {
    fprintf(stderr, "%s\n", mysql_error(conn));
    fprintf(stderr, "錯誤 %i\n", mysql_errno(conn));
    fprintf(stderr, "%s\n", sql);
    return 1;
  }

  // 處理查詢結果
  result = mysql_store_result(conn);
  if(result==NULL) {
    if(mysql_error(conn))
      fprintf(stderr, "%s\n", mysql_error(conn));
    else
      fprintf(stderr, "%s\n", "不明的錯誤\n");
    return 1;
  }
  printf("找到 %i 筆資料\n", (int)mysql_num_rows(result));

  // 處理每筆資料
  while((row = mysql_fetch_row(result)) != NULL) {
    for(i=0; i < mysql_num_fields(result); i++) {
      if(row[i] == NULL)
        printf("[NULL]\t");
      else
        printf("%s\t", row[i]);
    }
    printf("\n");
  }

  // 釋放記憶體，關閉連線
  mysql_free_result(result);
  mysql_close(conn);
  return 0;
}

// // 將設定檔內容複製到 argc/argv 內
// const char *groups[] = {"client", NULL};
// load_defaults("my", groups, &argc, &argv);

// // 測試一些函式
// printf("%s\n", mysql_get_host_info(conn));
// printf("%s\n", mysql_get_client_info());
// printf("%s\n", mysql_get_server_info(conn));
// printf("%i\n", mysql_get_proto_info(conn));

// // 新增內含特殊字元的資料
// #include <my_global.h>  // for strmov
// #include <m_string.h>   // for strmov
// char tmp[1000], *tmppos;
// char *publname = "O'Reilly";
// tmppos = strmov(tmp, "INSERT INTO publishers (publName) VALUES ('");
// tmppos += mysql_real_escape_string(
//   conn, tmppos, publname, strlen(publname));
// tmppos = strmov(tmppos, "')");
// *tmppos++ = (char)0;
// mysql_query(conn, tmp);
