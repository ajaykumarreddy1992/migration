#!/bin/bash
cd ../
cd $1
# Save all log entries in text file.
exec >> $1.txt 2>&1
echo "Install required modules Started"
cd docroot/
export COMPOSER_HOME="$HOME/.config/composer";
# Install Bootstrap and Adminimal theme for Apigee Admin.
composer require drupal/bootstrap
composer require drupal/adminimal_theme
echo "Enabling Required modules:"
vendor/drush/drush/drush dl entity -y
vendor/drush/drush/drush en entity -y
# Download and enable swagger module as we are importing new content type
# with swagger field formatter.
vendor/drush/drush/drush dl swagger_ui_formatter -y
vendor/drush/drush/drush en swagger_ui_formatter -y
vendor/drush/drush/drush en apigee_modules -y
vendor/drush/drush/drush cr
# Download and install all apigee required modules using /usr/local/bin/composer and vendor/drush/drush/drush
vendor/drush/drush/drush enable:modules
# Download all webform required libraries
vendor/drush/drush/drush webform-libraries-download
vendor/drush/drush/drush en apigee_migration -y
vendor/drush/drush/drush cr
vendor/drush/drush/drush ms
# Run vendor/drush/drush/drush migration scripts which are required to import users,content,etc.,
vendor/drush/drush/drush mim d7_user_role --update
vendor/drush/drush/drush mim d7_node_type --update
vendor/drush/drush/drush mim d7_field --update
vendor/drush/drush/drush mim d7_field_instance --update
vendor/drush/drush/drush mim d7_field_instance_widget_settings --update
vendor/drush/drush/drush mim d7_view_modes --update
vendor/drush/drush/drush mim d7_field_formatter_settings --update
vendor/drush/drush/drush mim d7_user --update
vendor/drush/drush/drush mim apigee_migration_user --update
vendor/drush/drush/drush mim apigee_migration_vocabulary --update
vendor/drush/drush/drush mim apigee_migration_term --update
vendor/drush/drush/drush mim apigee_migration_article --update
vendor/drush/drush/drush mim block_content_type --update
vendor/drush/drush/drush mim d7_filter_format --update
vendor/drush/drush/drush mim block_content_body_field --update
vendor/drush/drush/drush mim d7_custom_block --update
vendor/drush/drush/drush mim apigee_migration_context --update
vendor/drush/drush/drush enable:context
vendor/drush/drush/drush updb -y
vendor/drush/drush/drush cex -y
vendor/drush/drush/drush config-set system.site name "$1" -y
vendor/drush/drush/drush user-password admin --password="$4"
vendor/drush/drush/drush sql-query "update users set name='$3' where uid=1"
pwd
echo "Install required modules Ended"
echo "Completed..."
