/* SampleConnection2.java
   works ONLY with Java 2 version >= 1.4! */

import java.sql.*;
import javax.sql.*;  // 取用 DataSource 

public class SampleConnection2
{
  public static void main(String[] args)
  {
    try {
      com.mysql.jdbc.jdbc2.optional.MysqlDataSource ds;
      Connection conn1, conn2;
      Statement stmt;
      ResultSet res;

      System.out.println("connect using a DataSource ");      
      ds = new com.mysql.jdbc.jdbc2.optional.MysqlDataSource();
      ds.setServerName("uranus");
      ds.setDatabaseName("mylibrary");
      conn2 = ds.getConnection("root", "uranus");

      // 建立 Statement 物件
      stmt = conn2.createStatement();

      // 處理 SQL SELECT 查詢
      res = stmt.executeQuery(
        "SELECT publID, publName FROM publishers " + 
        "ORDER BY publName");

      // 取回查詢結果
      while (res.next()) {
        int id = res.getInt("publID");
        String name = res.getString("publName");
        System.out.println("ID: " + id + "  Name: " + name);
      }
    }
    catch(Exception e) {
      System.out.println("Error: " + e.toString() );
    }
  }
}

