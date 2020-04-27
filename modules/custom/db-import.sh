#!/bin/bash
# Drop database(if already exists)
cd ../
cd $1
# Save all log entries in text file.
exec >> $1.txt 2>&1
echo "DB Import Started"
cd docroot/
IFS='.' read -ra DATA <<< "$2"
echo "DROP database $1_d8" | mysql -u root -proot
echo "DROP database ${DATA[0]}" | mysql -u root -proot
# Create database for D7 (Source) & D8 (Destination)
echo "create database ${DATA[0]}" | mysql -u root -proot
echo "create database $1_d8" | mysql -u root -proot
# Import given database to source database (D7)
echo "Importing Source database.."
mysql -u root -proot ${DATA[0]} < $3
echo "Creation $1 database.."
mysql -u root -proot $1"_d8" < /var/www/html/migration/files/d8.sql
echo "DB Import Ended"