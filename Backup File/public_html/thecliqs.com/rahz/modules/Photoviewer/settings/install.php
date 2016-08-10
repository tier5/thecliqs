<?php

class Photoviewer_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $db = $this->getDb();


    // Add a new page "Photo Viewer"

    $db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('photoviewer_index_comments', 'Photo Viewer', NULL, 'Photo Viewer', 'Photo Viewer', NULL, '1', NULL, NULL, NULL, 'no-subject', NULL)");

    $page_id = $db->lastInsertId();

    if ($page_id) {

      $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'main', 'NULL', '2', '[]', NULL)");
      $parent_content_id = $db->lastInsertId();
      $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[]', NULL)");
      $parent_content_id_0 = $db->lastInsertId();
      $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'core.content', '$parent_content_id_0', '3', '[]', NULL)");
      $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'core.comments', '$parent_content_id_0', '4', '{\"title\":\"Comments\"}', NULL)");

    }


    parent::onInstall();
    
  }



}
