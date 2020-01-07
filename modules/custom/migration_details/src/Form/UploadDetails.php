<?php

namespace Drupal\migration_details\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class UploadDetails extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'upload_details';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['d8_name'] = [
      '#title' => t('Site Name'),
      '#type' => 'textfield',
      '#attributes' => [
        'placeholder' => t('Enter your Site name'),
      ],
      '#required' => TRUE,
    ];
    $form['source_db'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload D7 Database'),
      '#upload_location' => 'public://db/',
      '#progress_indicator' => 'bar',
      '#progress_message' => t('Please wait...'),
      '#upload_validators' => [
        'file_validate_extensions' => ['sql'],
        'file_validate_size' => ['2147483648'],
      ],
      '#max_filesize' => '2048M',
      '#required' => TRUE,
      '#description' => t('Upload your Drupal 7 database.'),
    ];
    $form['d8_user_name'] = [
      '#title' => t('Site UserName (D8)'),
      '#type' => 'textfield',
      '#attributes' => [
        'placeholder' => t('Enter your Site Username'),
      ],
      '#required' => TRUE,
    ];
    $form['d8_password'] = [
      '#title' => t('Site Password (D8)'),
      '#type' => 'password',
      '#attributes' => [
        'placeholder' => t('Enter your Site Password'),
      ],
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $sql_file = $values['source_db'][0];
    if ($sql_file) {
      $sql_load = File::load($sql_file);
      $sql_load->setPermanent();
      $sql_load->save();
    }
    $sql_name = $sql_load->getFilename();
    $sql_location = \Drupal::service('file_system')
      ->realpath($sql_load->getFileUri());
    $user_name = $values['d8_user_name'];
    $pswd = $values['d8_password'];
    $hash_salt = Crypt::randomBytesBase64(55);
    $d8_name = strtolower(str_replace(' ', '_', $values['d8_name']));
    $scripts = [
      'composer' => [
        'script' => './composer-drush.sh',
        'message' => t('Preparing to install "<b>' . $d8_name . '</b>"'),
        'db_name' => $sql_name,
      ],
      'download_d8' => [
        'script' => './download-d8.sh',
        'message' => t('Downloading new Drupal-8 for "<b>' . $d8_name . '</b>"'),
        'db_name' => $sql_name,
      ],
      'setting_copy' => [
        'script' => './settings-copy.sh',
        'message' => t('Setting up ' . '"<b>' . $d8_name . '</b>"'),
        'db_name' => $sql_name,
        'hash' => $hash_salt,
      ],
      'install' => [
        'script' => './new-install.sh',
        'message' => t('Importing source database '),
        'db_name' => $sql_name,
      ],
      'db_import' => [
        'script' => './db-import.sh',
        'message' => t('Installing required modules for  "<b>' . $d8_name . '</b>"'),
        'db_name' => $sql_name,
        'db_location' => $sql_location,
      ],
      'install_modules' => [
        'script' => './install.sh',
        'db_name' => $sql_name,
        'username' => $user_name,
        'password' => $pswd,
        'message' => t('Finishing set-up for "<b>' . $d8_name . '</b>"'),
      ],
      'install_finish' => [
        'script' => './new-install.sh',
        'db_name' => $sql_name,
        'message' => t('Finishing set-up for "<b>' . $d8_name . '</b>"'),
      ],
    ];
    $operations = [];
    foreach ($scripts as $key => $script) {
      if (isset($script['hash'])) {
        $operations[] = [
          '\Drupal\migration_details\BuildSite::siteBuild',
          [
            $script["script"] . ' ' . $d8_name . ' ' . $script['db_name'] . ' ' . $script['hash'],
            $script['message'],
          ],
        ];
      }
      elseif (isset($script['username']) && isset($script['password'])) {
        $operations[] = [
          '\Drupal\migration_details\BuildSite::siteBuild',
          [
            $script["script"] . ' ' . $d8_name . ' ' . $script['db_name'] . ' ' . $script['username'] . ' ' . $script['password'],
            $script['message'],
          ],
        ];
      }
      elseif (isset($script['db_name']) && isset($script['db_location'])) {
        $operations[] = [
          '\Drupal\migration_details\BuildSite::siteBuild',
          [
            $script["script"] . ' ' . $d8_name . '  ' . $script['db_name'] . ' ' . $script['db_location'],
            $script['message'],
          ],
        ];
      }
      else {
        $operations[] = [
          '\Drupal\migration_details\BuildSite::siteBuild',
          [
            $script["script"] . ' ' . $d8_name . ' ' . $script['db_name'],
            $script['message'],
          ],
        ];
      }
    }
    $batch = [
      'title' => t('Building ' . $values['d8_name'] . '...'),
      'operations' => $operations,
      'finished' => '\Drupal\migration_details\BuildSite::siteBuildFinishedCallback',
      'init_message' => t('Initializing migration for "<b>' . $d8_name . '</b>"'),
      'error_message' => t('The migration process has encountered an error.'),
      'progress_message' => t('Estimated time: @estimate.'),
    ];
    batch_set($batch);
  }

}
