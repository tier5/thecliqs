<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: install.php 9893 2013-02-14 00:00:53Z shaun $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $this->_addUserProfileContent();
    $this->_addBrowsePage();
    $this->_addViewPage();
    
    $this->_addCreatePage();
    $this->_addManagePage();
    
    parent::onInstall();
  }
  
  protected function _addManagePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'classified_index_manage')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'classified_index_manage',
        'displayname' => 'Classified Manage Page',
        'title' => 'My Listings',
        'description' => 'This page lists a user\'s classifieds.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $main_right_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'classified.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
      
      // Insert search
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'classified.browse-search',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 1,
      ));
      
      // Insert gutter menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'classified.browse-menu-quick',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 2,
      ));
    }
  }
  
  protected function _addCreatePage()
  {
  
  $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'classified_index_create')
      ->limit(1)
      ->query()
      ->fetchColumn();
      
    if( !$page_id ) {
      
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'classified_index_create',
        'displayname' => 'Classified Create Page',
        'title' => 'Post a New Listing',
        'description' => 'This page is the classified create page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'classified.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
    }
  }
  
  protected function _addBrowsePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'classified_index_index')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'classified_index_index',
        'displayname' => 'Classified Browse Page',
        'title' => 'Classified Browse',
        'description' => 'This page lists classifieds.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $main_right_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'classified.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
      
      // Insert search
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'classified.browse-search',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 1,
      ));
      
      // Insert gutter menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'classified.browse-menu-quick',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 2,
      ));
    }
  }
  
  protected function _addViewPage()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    
    // Check if it's already been placed
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'classified_index_view')
      ->limit(1)
      ->query()
      ->fetchColumn(0);
      ;

    if( !$page_id ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'classified_index_view',
        'displayname' => 'Classified View Page',
        'title' => 'View Classified',
        'description' => 'This is the view page for a classified.',
        'custom' => 0,
        'provides' => 'subject=classified',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');
      
      // middle column content
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
      ));

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.comments',
        'parent_content_id' => $middle_id,
        'order' => 2,
        'params' => '',
      ));
    }
  }

  protected function _addUserProfileContent()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // classified.profile-classifieds
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'classified.profile-classifieds')
      ;
    $info = $select->query()->fetch();
    if( empty($info) ) {
    
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
        ->reset('where')
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type'    => 'widget',
        'name'    => 'classified.profile-classifieds',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Classifieds","titleCount":true}',
      ));

    }
  }
}
