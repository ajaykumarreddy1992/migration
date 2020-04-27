#!/bin/bash
# Goto D8 folder
cd ../
cd $1
# Save all log entries in text file.
exec >> $1.txt 2>&1
echo "Settings Copy Started"
pwd
export COMPOSER_HOME="$HOME/.config/composer";
echo -e "ajaykumar%\$#@!\n" | sudo -S
cd docroot/sites/default/
cp default.settings.php settings.php
mkdir files/
mkdir files/private
chmod 777 settings.php files/
cd ../../
pwd
IFS='.' read -ra DATA <<< "$2"
# Insert migrate database settings.
echo "<?php
\$databases['migrate']['default'] = [
  'database' => '${DATA[0]}',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];" >> sites/default/settings.migrate.php

# Insert drupal-8 database settings.
echo "<?php
\$databases['default']['default'] = [
  'database' => '$1_d8',
  'username' => 'root',
  'password' => 'root',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];
\$settings['hash_salt'] ='$3';
\$settings['file_private_path'] = 'sites/default/files/private';
\$config_directories['sync'] ='sites/default/files/config';
" >> sites/default/settings.local.php
chmod 777 sites/default/settings.php

# Include settings.local.php in settings.php file for migration.
echo "\$local_settings = __DIR__ .'/settings.local.php';
if (file_exists(\$local_settings)) {
  include \$local_settings;
}" >> sites/default/settings.php
# Include settings.migrate.php in settings.php file for migration.
echo "\$migrate_settings = __DIR__ .'/settings.migrate.php';
if (file_exists(\$migrate_settings)) {
  include \$migrate_settings;
}" >> sites/default/settings.php
pwd
mkdir modules/contrib
mkdir modules/custom
mkdir libraries
# Copy all custom code, libraries, install script, composer json/lock from root folder.
cp -a ../../migration/files/custom modules/
cp -a ../../migration/files/libraries/* libraries
cp ../../migration/files/install.sh .
cp ../../migration/files/composer.json .
cp ../../migration/files/composer.lock .
cp ../../migration/files/.htaccess .
# Run /usr/local/bin/composer install to download all required vendor files.
composer install
echo "Settings Copy Ended"