#!/bin/bash
#set -e
# Save all log entries in text file.
exec &> $1.txt
IFS='.' read -ra DATA <<< "$2"
# Remove folder (if already exists)
rm -rf $1
# Download new D8.
drush dl drupal-8 --drupal-project-rename=$1 --destination=.
# Drop database(if already exists)
echo "DROP database $1" | /Applications/MAMP/Library/bin/mysql -uroot -proot
echo "DROP database ${DATA[0]}" | /Applications/MAMP/Library/bin/mysql -uroot -proot
# Create database for D7 (Source) & D8 (Destination)
echo "create database ${DATA[0]}" | /Applications/MAMP/Library/bin/mysql -uroot -proot
echo "create database $1" | /Applications/MAMP/Library/bin/mysql -uroot -proot
# Import given database to source database (D7)
echo "Importing Source database.." | /Applications/MAMP/Library/bin/mysql -uroot -proot ${DATA[0]} < /Applications/MAMP/htdocs/ajay/${DATA[0]}.sql
# Goto D8 folder
cd $1
mv ../$1.txt .
# Install drupal with standard profile and credentials
drush si standard --db-url=mysql://root:root@127.0.0.1:8889/$1 --account-name=admin --account-pass=admin --account-mail=swathi@gmail.com --site-name=$1 --site-mail=swathi@gmail.com --notify -y
# Create file for migrate details.
echo "" >> settings.migrate.php
pwd
echo -e "ajay\n" | sudo -S cp ../settings.migrate.php sites/default/settings.migrate.php
echo -e "ajay\n" | sudo -S chmod 777 sites/default/settings.migrate.php
# Insert migrate database settings.
echo "<?php
\$databases['migrate']['default'] = [
  'database' => '${DATA[0]}',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => '127.0.0.1',
  'port' => '8889',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];" >> sites/default/settings.migrate.php
pwd
sudo chmod 777 sites/default/settings.php
# Include settings.migrate.php in settings.php file for migration.
echo "\$local_settings = __DIR__ .'/settings.migrate.php';
if (file_exists(\$local_settings)) {
  include \$local_settings;
}" >> sites/default/settings.php
mkdir modules/contrib
mkdir modules/custom
mkdir libraries
# Copy all custom code, libraries, install script, composer json/lock from root folder.
cp -a ../custom modules/
cp -a ../libraries/* libraries
cp ../install.sh .
cp ../composer.json .
cp ../composer.lock .
# Run install.sh for next steps (Download necessary modules, vendor files, libraries)
./install.sh
