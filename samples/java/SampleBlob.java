/* SampleBlob.java */

import java.sql.*;
import java.io.*;

public class SampleBlob
{
  public static void main(String[] args)
  {
    try {
      // 載入 Connector/J 驅動程式
      Class.forName("com.mysql.jdbc.Driver").newInstance();

      // 連到 MySQL 伺服器
      Connection conn = DriverManager.getConnection(
        "jdbc:mysql://uranus/exceptions", "root", "uranus");

      // 建立 Statement 物件
      PreparedStatement pstmt1, pstmt2, pstmt3;
      pstmt1 = conn.prepareStatement(
        "INSERT INTO test_blob (a_blob) VALUES(?)");
      pstmt2 = conn.prepareStatement(
        "SELECT a_blob FROM test_blob WHERE id=?");
      pstmt3 = conn.prepareStatement(
        "DELETE FROM test_blob WHERE id=?");

      // 讀入檔案，存進 BLOB
      File readfile = new File("test.jpg");
      FileInputStream fis = new FileInputStream(readfile);
      pstmt1.setBinaryStream(1, fis, (int)readfile.length());
      pstmt1.executeUpdate();
      fis.close();

      // a_blob 資料表的新資料 ID
      long id = ((com.mysql.jdbc.Statement)pstmt1).getLastInsertID();

      // 建立新檔案
      File writefile = new File("copy-test.jpg");
      if(writefile.exists()) {
        writefile.delete();
        writefile.createNewFile(); }
      FileOutputStream fos = new FileOutputStream(writefile);

      // 從 a_blob 讀出 BLOB
      pstmt2.setLong(1, id);
      ResultSet res = pstmt2.executeQuery();
      res.next();
      InputStream is = res.getBinaryStream(1);

      // 將 BLOB 存成新檔案
      final int BSIZE = 2^15;
      int n;
      byte[] buffer = new byte[BSIZE];
      while((n=is.read(buffer, 0, BSIZE))>0)
        fos.write(buffer, 0, n);
      
      // 關閉物件
      is.close();
      fos.close();
      res.close();

      // 刪除新資料
      pstmt3.setLong(1, id);
      pstmt3.executeUpdate();
    }
    catch(Exception e) {
      System.out.println("Error: " + e.toString() );
    }
  }
}

