store.php:

-line 20 change the ip or keep 127.0.0.1 if you server is on the same machine than your server.
-line 21 change game server port
-line 22 change rcon port
-line 392 to 454 here is your direct donation paypal button (package vip/vip+/elite...)

shop.php:

please follow screenshot
-line 31 put your paypal id here from https://developer.paypal.com/developer/applications
-line 32 put your paypal secret here from https://developer.paypal.com/developer/applications

getItemPrice.php:

-line 4 change the ip or keep 127.0.0.1 if you server is on the same machine than your server.
-line 5 change game server port
-line 6 change rcon port

go to api folder:

login.php:
-line 5 change the ip can be ip or domain name


go to steam folder:

apikey.php:
-line 3 put here your steam api key

go back to api folder and go to RstIO folder:
apikey.php:
-line 3 put here your rustio api key

go back to api folder and go to SourceQuery folder
SourceQuery.php:
-line 31 put here your domain name (if server is on same server as webserver) or server ip

go back to api folder and go to mysql folder
settings.ini.php:
-provide here mysql database

create database named 'rust' and import rust_itemprice.sql and rust_payment.sql
