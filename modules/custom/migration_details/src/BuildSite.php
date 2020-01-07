<?php

namespace Drupal\migration_details;

use Symfony\Component\Process\Process;

class BuildSite {

  public static function siteBuild($script, $message, &$context) {
    $results = [];
    if ($script) {
      $module_process = new Process($script);
      $module_process->setTimeout(600);
      $module_process->mustRun();
      $results[] = $module_process->getOutput();
    }
    $context['sandbox']['progress']++;
    $context['sandbox']['current_letter'] = $message;
    $context['message'] = $message;
    $context['results'][] = $results;
  }

  function siteBuildFinishedCallback($success, $results, $operations) {
    if ($success) {
      $message = t('Successfully migrated...');
    }
    else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addMessage($message);
  }
}