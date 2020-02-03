#!c:/perl/bin/perl -w
# mylibrary-simpleinput.pl

use utf8;
use Encode qw(decode);
binmode(STDOUT, ":utf8");

use strict;
use DBI;
use CGI qw(:standard);
use CGI::Carp qw(fatalsToBrowser);

# 變數宣告
my($datasource, $user, $passw, $dbh, @row,
   $formTitle, $formAuthors, $titleID, $authID, $author);

# 連到資料庫
$datasource   = "DBI:mysql:database=mylibrary;host=localhost;";
$user  = "root";
$passw = "uranus";
$dbh = DBI->connect($datasource, $user, $passw,
  {'PrintError' => 0});

if(DBI->err()) {
  print header(),
    start_html("抱歉，無法連到資料庫"), 
    p("抱歉，無法連到資料庫"), 
    end_html(); 
  exit();
}

# 告訴 MySQL 以 UTF-8 傳回資料
$dbh->do("SET NAMES 'utf8'");

# 輸出 HTML 開頭
print header(-type => "text/html",  -charset => "utf-8"),
    start_html("Perl 程式設計，新增資料到 mylibrary 資料庫"), "\n",
    h2("為 mylibrary 資料庫新增書籍資料"), "\n";

# 處理輸入資料
if(param()) {
  $formTitle = decode('utf8', param('formTitle'));
  $formAuthors = decode('utf8', param('formAuthors'));

  # 檢查輸入資料
  if($formTitle eq "" || $formAuthors eq "") {
    print p(), b("請指定標題，以及至少一位作者！"); }

  # 輸入看來沒問題，存入 mylibrary 資料庫
  else {
    # 儲存書籍
    $dbh->do("INSERT INTO titles (title) VALUES (?)", undef, ($formTitle));
    $titleID = $dbh->{'mysql_insertid'};

    # 儲存作者
    foreach $author (split(/;/, $formAuthors)) {
      # 首先檢查資料庫內是否已經有作者資料
      @row = $dbh->selectrow_array("SELECT authID FROM authors " .
                                   "WHERE authName = " . $dbh->quote($author));
      # 作者資料已存在，取得 authID
      if(@row) {
        $authID = $row[0]; }
      # 作者不存在，新增資料並取得新的 authID
      else {
        $dbh->do("INSERT INTO authors (authName) VALUES (?)", undef, ($author));
        $authID = $dbh->{'mysql_insertid'};
      }
      # 儲存 rel_title_author 連結
      $dbh->do("INSERT INTO rel_title_author (titleID, authID) " .
               "VALUES ($titleID, $authID)");
    }

    # 回到輸入畫面
    print p(), "已儲存您輸入的書籍。";
    print br(), "您可以繼續輸入下一本書籍的資料。";

    # 清空表單
    param(-name=>'formTitle', -value=>'');
    param(-name=>'formAuthors', -value=>'');
  }
}

# 顯示表單
print start_form(),
    p(), "標題:", 
    br(), 
    textfield({-name => 'formTitle', -size => 60, 
               -maxlength => 80}), "\n",
    p(), "作者:",
    br(),
    textfield({-name => 'formAuthors', -size => 60, 
               -maxlength => 100}), "\n",
    br(), 
    "(先寫姓！如果您想指定許多位作者的話，請在中間以 ; 隔開！)", "\n",
    p(),
    submit({-name => 'formSubmit', -value => 'OK'}),
    end_form(), "\n";

# 顯示 mylibrary-find.pl 的連結
print p(), 'Goto ', a({-href=>'mylibrary-find.pl'}, 'mylibrary-find'), "\n";

# 結束
print end_html();
$dbh->disconnect();
