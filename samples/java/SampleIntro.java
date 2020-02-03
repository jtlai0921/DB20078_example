/* SampleIntro.java */

import java.sql.*;

public class SampleIntro
{
  public static void main(String[] args)
  {
    try {
      Connection conn;
      Statement stmt;
      ResultSet res;

      // 載入 Connector/J 驅動程式
      Class.forName("com.mysql.jdbc.Driver").newInstance();

      // 連到 MySQL 伺服器
      conn = DriverManager.getConnection(
        "jdbc:mysql://uranus/mylibrary", "root", "uranus");

      // 建立 Statement 物件
      stmt = conn.createStatement();

      // 執行 SQL SELECT 查詢
      res = stmt.executeQuery(
        "SELECT publID, publName FROM publishers " + 
        "ORDER BY publName");

      // 取回查詢結果
      while (res.next()) {
        int id = res.getInt("publID");
        String name = res.getString("publName");
        System.out.println("ID: " + id + "  Name: " + name);
      }
      res.close();
      
    }
    catch(Exception e) {
      System.out.println("Error: " + e.toString() );
    }
  }
}

