#!/usr/bin/perl
use DBI();

# 連線
$dbh = DBI->connect(
 "DBI:mysql:database=mysql;host=localhost",
 "root", "xxxx", {'RaiseError' => 1});

# 查詢
$sth = $dbh->prepare("SHOW DATABASES");
$sth->execute();

# 顯示結果
while(@ary = $sth->fetchrow_array()) {
  print join("\t", @ary), "\n";
}
$sth->finish();

# 等待輸入
print "Please hit Return to end the program!";
$wait_for_return = <STDIN>;
