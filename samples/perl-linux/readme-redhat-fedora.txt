--------

(UTF-8)

您必須修改使用者名稱與密碼，才能正常執行！

如果您使用 Red Hat Enterprise Linux 4 或 Fedora Core 的話，
還需要把 localhost 換成正式的網路名稱，理由是 SELinux 的預設設定
不允許 Apache (與它執行的 Perl CGI scripts) 存取 MySQL 的 socket 檔，
因此必須改以 TCP/IP 連到 MySQL 伺服器。

另一種作法是以 system-config-security 關閉 httpd 的 SELinux。
