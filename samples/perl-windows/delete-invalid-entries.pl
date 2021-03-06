#!/usr/bin/perl -w
# delete-invalid-entries.pl

use strict;
use DBI;

# 變數
my($datasource, $user, $passw, $dbh, $sth, $row, $n, $ids);

# 連到資料庫
$datasource   = "DBI:mysql:database=mylibrary;host=localhost;";
$user  = "root";
$passw = "xxx";
$dbh = DBI->connect($datasource, $user, $passw,
  {'RaiseError' => 1});

# 刪除測試書籍
$sth = $dbh->prepare(
  "SELECT DISTINCT title, titles.titleID " .
  "FROM titles, rel_title_author " .
  "WHERE titles.titleID = rel_title_author.titleID " .
  "  AND (title LIKE 'test%' OR subtitle LIKE 'test%')");
$sth->execute();
while($row = $sth->fetchrow_hashref()) {
  print "Delete title: $row->{'title'}\n";
  $dbh->do("DELETE FROM rel_title_author " .
           "WHERE titleID=$row->{'titleID'}");
  $dbh->do("DELETE FROM titles " .
           "WHERE titleID=$row->{'titleID'}");  }
$sth->finish();

# 刪除測試作者
$sth = $dbh->prepare(
  "SELECT DISTINCT authName, authors.authID " .
  "FROM authors, rel_title_author " .
  "WHERE authors.authID = rel_title_author.authID " .
  "  AND authName LIKE 'test%'");
$sth->execute();
while($row = $sth->fetchrow_hashref()) {
  print "Delete author: $row->{'authName'}\n";
  $dbh->do("DELETE FROM rel_title_author " .
           "WHERE authID=$row->{'authID'}");
  $dbh->do("DELETE FROM authors " .
           "WHERE authID=$row->{'authID'}");  }
$sth->finish();

# 刪除孤立書籍
$n = $dbh->do(
  "DELETE FROM titles " .
  "WHERE titleID NOT IN " .
  "  (SELECT titleID FROM rel_title_author)");
if($n>0) {
  print "Deleted $n orphaned titles\n"; }

# 刪除孤立作者
$n = $dbh->do(
  "DELETE FROM authors " .
  "WHERE authID NOT IN " .
  "  (SELECT authID FROM rel_title_author)");
if($n>0) {
  print "Deleted $n orphaned authors\n"; }

# 刪除孤立出版社
$n = $dbh->do(
  "DELETE FROM publishers " .
  "WHERE publID NOT IN " .
  "  (SELECT DISTINCT publID FROM titles " .
  "   WHERE NOT publID IS NULL )");
if($n>0) {
  print "Deleted $n orphaned publishers\n"; }

# 結束
$dbh->disconnect();
