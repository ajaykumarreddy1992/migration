<?php

namespace Drupal\migrate_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

class MigrationSummary extends ControllerBase {

  public function generateReport() {
    // Switch to external database
    $database = Database::getConnectionInfo('migrate');
    if ($database) {
      Database::setActiveConnection('migrate');
      // Get a connection going
      $db = Database::getConnection();
      // Get node count.
      $node_types = $db->select('node_type', 'n')
        ->fields('n', ['name'])
        ->execute()
        ->fetchAll();
      $nodetypes = count($node_types);
      $data['Content_types'] = $nodetypes;
      $nodes_count = $db->select('node', 'n')
        ->fields('n', ['nid'])
        ->execute()
        ->fetchAll();
      $nodes = count($nodes_count);
      $data['nodes'] = $nodes;
      // Get File count.
      $files = $db->select('file_managed', 'f')
        ->fields('f', ['fid'])
        ->execute()
        ->fetchAll();
      $files_managed = count($files);
      $data['files_managed'] = $files_managed;

      //Get User Role count.
      $userroles = $db->select('role', 'r')
        ->fields('r', ['name'])
        ->execute()
        ->fetchAll();
      $roles = count($userroles);
      $data['roles'] = $roles;

      //Get count of users
      $usersquery = $db->select('users', 'u')
        ->fields('u', ['uid'])
        ->execute()
        ->fetchAll();
      $users = count($usersquery);
      $data['users'] = $users;

      //Get count of taxonomy vocabulary
      $taxonomy_vocab = $db->select('taxonomy_vocabulary', 't')
        ->fields('t', ['name'])
        ->execute()
        ->fetchAll();
      $taxonomy_vocabulary = count($taxonomy_vocab);
      $data['taxonomy_vocabulary'] = $taxonomy_vocabulary;

      //Get counf of taxonomy terms
      $taxonomy_terms = $db->select('taxonomy_term_data', 't')
        ->fields('t', ['tid'])
        ->execute()
        ->fetchAll();
      $terms = count($taxonomy_terms);
      $data['terms'] = $terms;

      //Get count of viewsdata
      $viewsdata = [];
      //if ( \Drupal\Core\Extension\ModuleHandler::moduleExists('views')) {
      //Fetching views data
      $views_query = $db->select('views_view', 'v')
        ->fields('v', ['vid', 'name', 'description'])
        ->execute();
      $views_count = 0;
      foreach ($views_query as $view) {
        $views_count++;
        $query2 = $db->select('views_display', 'v')
          ->fields('v', ['id', 'display_title'])
          ->condition('vid', $view->vid, '=')
          ->execute()
          ->fetchAll();
        $display_count = count($query2);
        array_push($viewsdata, [
          'view' => $view->name,
          'description' => $view->description,
          'displays' => $display_count,
        ]);
      }
      //}
      //$data['viewsdata'] = $viewsdata;
      $data['viewsdata'] = $views_count;

      //Get count of all enabled Modules
      $enabled_modules = $db->select('system', 's')
        ->fields('s', ['filename', 'name', 'schema_version'])
        ->condition('status', 1, '=')
        ->condition('type', 'module', '=')
        ->execute()
        ->fetchAll();
      $enabled_modules = count($enabled_modules);
      $data['enabled_modules'] = $enabled_modules;

      //Get count of all enabled Themes
      $enabled_themes = $db->select('system', 's')
        ->fields('s', ['filename', 'name', 'schema_version'])
        ->condition('status', 1, '=')
        ->condition('type', 'theme', '=')
        ->execute()
        ->fetchAll();
      $enabled_themes = count($enabled_themes);
      $data['enabled_themes'] = $enabled_themes;
      // Set back D8 database active.
      Database::setActiveConnection();
      //      print'<pre>';print_r($data);exit;
      return $data;
    }
  }

}
