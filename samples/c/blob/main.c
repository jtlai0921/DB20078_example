#include <stdio.h>
#include <my_global.h>  // for strmov
#include <m_string.h>   // for strmov
#include <mysql.h>

int main(int argc, char *argv[])
{
  int id;
  FILE *f;
  MYSQL *conn;        // MySQL 伺服器連線
  MYSQL_RES *result;  // 儲存 SELECT 查詢結果
  MYSQL_ROW row;      // 一筆資料
  size_t fsize;
  char fbuffer[512 * 1024];  // 檔案最大 512 KB
  char tmp[1024 * 1024], *tmppos;
  unsigned long *lengths;

  // 連到 MySQL 伺服器
  conn = mysql_init(NULL);
  if(mysql_real_connect(
        conn, "localhost", "root", "uranus", 
        "exceptions", 0, NULL, 0) == NULL) {
      fprintf(stderr, "抱歉，無法連上資料庫伺服器 ...\n");
      return 1;
    }

  // 讀入 test.jpg 並且存成 test_blob 的資料
  f = fopen("test.jpg", "r");
  fsize = fread(fbuffer, 1, sizeof(fbuffer), f);
  fclose(f);
  tmppos = strmov(tmp, "INSERT INTO test_blob (a_blob) VALUES ('");
  tmppos += mysql_real_escape_string(
    conn, tmppos, fbuffer, fsize);
  tmppos = strmov(tmppos, "')");
  *tmppos++ = (char)0;
  mysql_query(conn, tmp);
  id = (int)mysql_insert_id(conn);

  // 讀回資料，並存成 test-copy.jpg
  f = fopen("test-copy.jpg", "w");
  sprintf(tmp, "SELECT a_blob FROM test_blob WHERE id = %i", id);
  mysql_query(conn, tmp);
  result = mysql_store_result(conn);
  row = mysql_fetch_row(result);
  lengths = mysql_fetch_lengths(result);
  fwrite(row[0], 1, lengths[0], f);
  fclose(f);

  // 刪除資料
  sprintf(tmp, "DELETE FROM test_blob WHERE id = %i", id);
  mysql_query(conn, tmp);

  // 釋放資源
  mysql_free_result(result);
  mysql_close(conn);
  return 0;
}
