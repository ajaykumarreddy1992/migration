#!/bin/bash
#set -e
cd ../
cd $1
# Save all log entries in text file.
exec >> $1.txt 2>&1
echo "Download Drupal Started"
# Download new D8.
vendor/drush/drush/drush dl drupal-8 --drupal-project-rename=docroot --destination=.
echo "Download Drupal Ended"