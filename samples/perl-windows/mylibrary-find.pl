#!c:/perl/bin/perl -w
# mylibrary-find.pl

use utf8;
use Encode qw(decode);
binmode(STDOUT, ":utf8");

use strict;
use DBI;
use CGI qw(:standard);
use CGI::Carp qw(fatalsToBrowser);

# 變數
my($datasource, $user, $passw, $dbh, $search, $sql, $sth, 
   $result, $rows, $i, $row);

# 連到資料庫
$datasource   = "DBI:mysql:database=mylibrary;host=localhost;";
$user  = "root";
$passw = "uranus";
$dbh = DBI->connect($datasource, $user, $passw,
  {'PrintError' => 0});

$dbh->do('SET NAMES utf8');

# 顯示錯誤訊息
if(DBI->err()) {
  print header(),
    start_html("抱歉，無法與資料庫建立連線！"), 
    p("抱歉，無法與資料庫建立連線！"), 
    end_html(); 
  exit();
}



# 顯示 HTML 標題
print header(-type => "text/html",  -charset => "utf-8"),
    start_html("Perl 程式設計，搜尋 mylibrary 資料庫"), "\n",
    h2("搜尋 mylibrary 資料庫內的書籍"), "\n";

# 處理表單輸入
$search = decode('utf8', param('formSearch'));
# 移除 _ 與 %
$search =~ tr/%_//d;
if($search) {
  print p(), b("以 ", escapeHTML($search), " 開頭的書籍資料"), hr();

  # 搜尋書籍
  $sql = "SELECT titles.titleID, titles.publID, title, year, publName, " .
         "       GROUP_CONCAT(DISTINCT authName ORDER BY authName SEPARATOR ', ') " .
         "         AS authors " .
         "FROM titles, authors, rel_title_author " .
         "  LEFT JOIN publishers ON (publID = publishers.publID) " .
         "WHERE titles.titleID = rel_title_author.titleID " .
         "  AND authors.authID = rel_title_author.authID " .
         "  AND title LIKE '$search%' " .
         "GROUP BY titles.titleID " .
         "ORDER BY title " .
         "LIMIT 100";

  $sth = $dbh->prepare($sql);
  $sth->execute();
  $result = $sth->fetchall_arrayref({});
  $sth->finish();

  # 有任何結果嗎?
  $rows = @{$result};
  if($rows==0) {
    print p(), "抱歉，找不到符合條件的書籍！"; }
  # 顯示結果
  else {
    # 逐筆處理所有資料
    for($i=0; $i<$rows; $i++) {
      $row = $result->[$i];
      print p(), 
	b(escapeHTML(decode("utf8", $row->{'title'}))), ": ",
        i(escapeHTML(decode("utf8", $row->{'authors'}))), ". ",
          escapeHTML(decode("utf8", $row->{'publName'})), " ",
        $row->{'year'}, "\n";
    }
    print p(), hr(), p(), "重新搜尋:", p();
  }
}

# 顯示表單
print start_form(),
    p(), "搜尋標題開頭 ... ",
    textfield({-name => 'formSearch', -size => 20,
               -maxlength => 20}), " ",
    submit({-name => 'formSubmit', -value => 'OK'}),
    end_form();

# 顯示 mylibrary-simpleinput.pl 的連結
print p(), '前往 ',
      a({-href=>'mylibrary-simpleinput.pl'}, 'mylibrary-simpleinput'),
      "\n";

# 結束
print end_html();
$dbh->disconnect();
