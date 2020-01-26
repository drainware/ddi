#!/bin/bash

FTP_SERVER=u76363.your-backup.de
FTP_USER=u76363
FTP_PASSWD=ZLFxNCkk6kkigkWb


cd /tmp 

TEMP_DIR=$(mktemp -d)

NOW=$(date +%Y%m%d%H%M%S)

mongodump -o $TEMP_DIR

tar cvfj mongodump-$NOW.tar.bz2 $TEMP_DIR

rm -Rf $TEMP_DIR

ftp -pinv $FTP_SERVER << EOF 
user $FTP_USER $FTP_PASSWD
put mongodump-$NOW.tar.bz2
bye
EOF

echo rm mongodump-$NOW.tar.bz2

cd -



