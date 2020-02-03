/* SampleGetID1.java 
   works ONLY with Java 2 version >= 1.4! */

import java.sql.*;

public class SampleGetID1
{
  public static void main(String[] args)
  {
    try {
      int id, n;
      Connection conn;
      Statement stmt;
      ResultSet newid, newids;

      // 連到 MySQL 伺服器
      Class.forName("com.mysql.jdbc.Driver").newInstance();
      conn = DriverManager.getConnection(
        "jdbc:mysql://uranus/mylibrary", "root", "uranus");

      // 處理 INSERT 查詢
      stmt = conn.createStatement();
      n = stmt.executeUpdate(
        "INSERT INTO publishers (publName) VALUES ('new publisher')");
      newid = stmt.getGeneratedKeys();
      newid.next();
      id = newid.getInt(1);
      System.out.println("新資料筆數 = " + n);
      System.out.println("ID = " + id);
      System.out.println();

      // 一起處理三個 INSERT 指令
      n = stmt.executeUpdate(
        "INSERT INTO publishers (publName) VALUES ('publ1'), ('publ2'), ('publ3')", 
        Statement.RETURN_GENERATED_KEYS);
      System.out.println("新資料筆數 = " + n);
      newids = stmt.getGeneratedKeys();
      while(newids.next()) {  // 取回所有 ID
        System.out.println("ID = " + newids.getInt(1));
      }

    }
    catch(Exception e) {
      System.out.println("Error: " + e.toString() );
    }
  }
}
