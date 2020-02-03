/* SampleGetID2.java */

import java.sql.*;

public class SampleGetID2
{
  public static void main(String[] args)
  {
    try {
      long id;
      Connection conn;
      Statement stmt;

      // 連上 MySQL
      Class.forName("com.mysql.jdbc.Driver").newInstance();
      conn = DriverManager.getConnection(
        "jdbc:mysql://uranus/mylibrary", "root", "uranus");

      // 處理 INSERT 查詢
      stmt = conn.createStatement();
      stmt.executeUpdate(
        "INSERT INTO publishers (publName) VALUES ('new publisher')");
      id = ((com.mysql.jdbc.Statement)stmt).getLastInsertID();
      System.out.println("new ID = " + id);

    }
    catch(Exception e) {
      System.out.println("Error: " + e.toString() );
    }
  }
}
