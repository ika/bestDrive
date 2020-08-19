#!/bin/bash
#
# user.sh
# install database  user
#
user="root"
pass="root"
db="tireDB"
dbuser="multiuser"
dbpass="EpYy3ERrrNTJSbz4"
sql="CREATE USER '$dbuser'@'localhost' IDENTIFIED BY '$dbpass'"
sql1="GRANT ALL PRIVILEGES ON $db.* TO '$dbuser'@'localhost'"
sql2="FLUSH PRIVILEGES" 
mysql -u"$user" -p"$pass" <<EOF 
use $db;
$sql;
$sql1;
$sql2;
EOF
