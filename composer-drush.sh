#!/bin/bash
cd ../
# Remove folder (if already exists)
rm -rf $1
mkdir $1
cd $1
# Save all log entries in text file.
exec >> $1.txt 2>&1
echo "Composer Drush Started"
pwd
export COMPOSER_HOME="$HOME/.config/composer";
#Install Drush 8 version.
composer require drush/drush:8.x-dev --prefer-source
echo "Composer Drush Ended"