#!/bin/bash

MYSQL_ROOT_PASSWORD=etsam4771
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "SHOW DATABASES;" 

mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e  "SELECT * FROM yt_02.users\G;"
# mysql -u root -p"$MYSQL_ROOT_PASSWORD" --pager="less -SFX" -t -e "SELECT * FROM yt_02.users;"
