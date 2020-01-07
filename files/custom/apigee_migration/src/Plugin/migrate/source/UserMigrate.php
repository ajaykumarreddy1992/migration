<?php


namespace Drupal\apigee_migration\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Drupal 7 user source from database.
 *
 * @MigrateSource(
 *   id = "apigee_migration_user",
 * )
 */
class UserMigrate extends FieldableEntity {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('users', 'u')
      ->fields('u')
      ->condition('u.uid', 0, '>');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'pass' => $this->t('Password'),
      'mail' => $this->t('Email address'),
      'signature' => $this->t('Signature'),
      'signature_format' => $this->t('Signature format'),
      'created' => $this->t('Registered timestamp'),
      'access' => $this->t('Last access timestamp'),
      'login' => $this->t('Last login timestamp'),
      'status' => $this->t('Status'),
      'timezone' => $this->t('Timezone'),
      'language' => $this->t('Language'),
      'picture' => $this->t('Picture'),
      'init' => $this->t('Init'),
      'data' => $this->t('User data'),
    ];
    $fields['field_first_name'] = $this->t('First Name');
    $fields['field_last_name'] = $this->t('Last Name');
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');
    // first_name
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_first_name_value
      FROM
        {field_data_field_first_name} fld
      WHERE
        fld.entity_id = :uid
    ', [':uid' => $uid]);
    foreach ($result as $record) {
      $row->setSourceProperty('field_first_name', $record->field_first_name_value);
      $row->setSourceProperty('first_name', $record->field_first_name_value);
    }
    // last_name
    $result = $this->getDatabase()->query('
      SELECT
        fld.field_last_name_value
      FROM
        {field_data_field_last_name} fld
      WHERE
        fld.entity_id = :uid
    ', [':uid' => $uid]);
    foreach ($result as $record) {
      $row->setSourceProperty('field_last_name', $record->field_last_name_value);
      $row->setSourceProperty('last_name', $record->field_last_name_value);
    }
    // End of fields.
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'u',
      ],
    ];
  }


}
