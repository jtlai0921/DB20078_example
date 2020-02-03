#!/usr/bin/perl -w
# cgi-test-mysql.pl

use strict;
use DBI;
use CGI qw(:standard);
use CGI::Carp qw(fatalsToBrowser);
use HTML::Entities;

# 變數
my($dbh, $sth, $row, @ary);

# 標題
print header(),
    start_html("Hello CGI"), "\n",
    h1("Helle CGI"), "\n";

# 連線
$dbh = DBI->connect(
  "DBI:mysql:database=mysql;host=localhost",
  "root", "xxx", {'RaiseError' => 1});

if(DBI->err()) {
  print p("Sorry, no database connection"), end_html(); 
  exit();
}

# 查詢資料
$sth = $dbh->prepare("SHOW DATABASES");
$sth->execute();

# 顯示結果
while(@ary = $sth->fetchrow_array()) {
  print br(), join(" ", @ary);
}
$sth->finish();

print end_html();

# 結束
$dbh->disconnect();
