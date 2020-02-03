/* SampleChangeResultSet.java */

import java.sql.*;

public class SampleChangeResultSet
{
  public static void main(String[] args)
  {
    try {
      Connection conn;
      Statement stmt;
      ResultSet res;

      // 連線
      Class.forName("com.mysql.jdbc.Driver").newInstance();
      conn = DriverManager.getConnection(
        "jdbc:mysql://uranus/mylibrary", "root", "uranus");
      stmt = conn.createStatement(ResultSet.TYPE_SCROLL_SENSITIVE, 
                                  ResultSet.CONCUR_UPDATABLE);
      // 新增出版社，顯示所有出版社
      res = stmt.executeQuery(
        "SELECT publID, publName FROM publishers ORDER BY publID");
      res.moveToInsertRow(); 
      res.updateString(2, "New publisher"); 
      res.insertRow();
      res.last();
      int newid = res.getInt(1);
      res.beforeFirst();
      while (res.next())
        System.out.println(res.getString(1) + " " + res.getString(2));
      res.close();

      // read new publisher
      res = stmt.executeQuery(
        "SELECT publID, publName FROM publishers WHERE publID = " + newid);
      res.next();
      res.updateString(2, "new with another name");
      res.updateRow();

      // 刪除出版社
      res.last();
      res.deleteRow();
      res.close();
    }
    catch(Exception e) {
      System.out.println("Error: " + e.toString() );
    }
  }
}

