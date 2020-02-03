(UTF-8)

這些範例檔將示範各種程式設計技巧。大多數範例都需要 PHP >= 5.0.3 並開啟 
mysqli 介面，配合 MySQL >= 4.1 使用。預存程序的範例則需要 MySQL >= 5.0.2。

這些檔案都以 UTF-8 編碼儲存！

在開始試用這些範例之前，您 _必須_ 先修改檔案內的使用者名稱與密碼！

如果您使用 Red Hat Enterprise Linux 4 或 Fedora Core 的話，也必須將 localhost 
換成本機的真實網路名稱，理由是 SELinux 的預設設定不允許 Apache (與 PHP) 
透過 socket 檔與 MySQL 通訊，因此必須改用 TCP/IP 進行連線 (另一個做法是以 
system-config-security 關閉 httpd 的 SELinux 功能。

- mysql-intro.php
- mysqli-intro.php
- password.php

範例檔功能簡介

phpinfo.php           	測試 PHP 所支援的延伸模組
mysql-intro.php       	mysql 介面 (延伸模組) 的基本範例
mysqli-intro.php      	mysqli 介面 (延伸模組) 的基本範例
mysqli-prepared.php   	預備敘述的範例
mysqli-table.php      	以 HTML 表格顯示 SELECT 結果

mydb.php              	MyDb 的類別定義
test-mydb.php         	MyDb 的測試檔
password.php          	MyDb 的密碼資料

categories.php        	mylibrary 範例資料庫的書籍類別管理
find.php              	mylibrary 的書籍資料搜尋功能
titleform.php         	mylibrary 的書籍資料新增功能

formfunctions.php       建立 HTML 表單的各種函式
mylibraryfunctions.php  categories.php, find.php 與 titleform.php 用到的
                        各種函式

deletegarbage.php       刪除 mylibrary 資料庫內的垃圾資料

*.css                   各類 CSS 樣式表

optimize/*              程式碼調整的範例
images/*                檔案上傳範例
spadmin/*               SP Administrator
unicode/*               mylibrary 範例的 Unicode 版
