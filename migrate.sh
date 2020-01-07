#!/bin/bash
cd ../
## Save all log entries in text file.
#exec &> $1.txt
#IFS='.' read -ra DATA <<< "$2"
## Remove folder (if already exists)
#rm -rf $1
## Download new D8.
#~/.composer/vendor/drush/drush/drush dl drupal-8 --drupal-project-rename=$1 --destination=.
# Goto D8 folder
cd $1
exec &> $1.txt
pwd
## Install only Drush 8 version as most of the vendor files depende on this.
#/usr/local/bin/composer require drush/drush:8.x-dev --prefer-source
## Drop database(if already exists)
#echo "DROP database $1" | /Applications/MAMP/Library/bin/mysql -uroot -proot
#echo "DROP database ${DATA[0]}" | /Applications/MAMP/Library/bin/mysql -uroot -proot
## Create database for D7 (Source) & D8 (Destination)
#echo "create database ${DATA[0]}" | /Applications/MAMP/Library/bin/mysql -uroot -proot
#echo "create database $1" | /Applications/MAMP/Library/bin/mysql -uroot -proot
## Import given database to source database (D7)
#echo "Importing Source database.." | /Applications/MAMP/Library/bin/mysql -uroot -proot ${DATA[0]} < /Applications/MAMP/htdocs/files/${DATA[0]}.sql
#mv ../$1.txt .
# Install drupal with standard profile and credentials
./data.sh
#drush si standard --db-url="/Applications/MAMP/Library/bin/mysql'://root:root@127.0.0.1:8889/$1" --account-name=admin --account-pass=admin --account-mail=swathi@gmail.com --site-name=$1 --site-mail=swathi@gmail.com --notify --debug -y
## Create file for migrate details.
#echo "" >> settings.migrate.php
#pwd
#echo -e "ajay\n" | sudo -S cp ../settings.migrate.php sites/default/settings.migrate.php
#echo -e "ajay\n" | sudo -S chmod 777 sites/default/settings.migrate.php
## Insert migrate database settings.
#echo "<?php
#\$databases['migrate']['default'] = [
#  'database' => '${DATA[0]}',
#  'username' => 'root',
#  'password' => 'root',
#  'prefix' => '',
#  'host' => '127.0.0.1',
#  'port' => '8889',
#  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
#  'driver' => 'mysql',
#];" >> sites/default/settings.migrate.php
#pwd
#sudo chmod 777 sites/default/settings.php
## Include settings.migrate.php in settings.php file for migration.
#echo "\$local_settings = __DIR__ .'/settings.migrate.php';
#if (file_exists(\$local_settings)) {
#  include \$local_settings;
#}" >> sites/default/settings.php
#mkdir modules/contrib
#mkdir modules/custom
#mkdir libraries
## Copy all custom code, libraries, install script, composer json/lock from root folder.
#cp -a ../files/custom modules/
#cp -a ../files/libraries/* libraries
#cp ../files/install.sh .
#cp ../files/composer.json .
#cp ../files/composer.lock .
### Run install.sh for next steps (Download necessary modules, vendor files, libraries)
###./install.sh
