<?php
/**
 * @category   Application_Extensions
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/

class Welcomepagevk_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'welcomepagevk_index_index')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'welcomepagevk_index_index',
        'displayname' => 'Welcome VK Page ',
        'title' => 'Welcome VK Page ',
        'description' => 'This is the Welcome VK Page .',
        'custom' => 0,
        'layout' => 'default-simple'
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers top
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_top_id = $db->lastInsertId('engine4_core_content');

      // containers top middle
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_top_id,
        'order' => 6,
        'params' => '',
      ));
      $container_top_middle_id = $db->lastInsertId('engine4_core_content');

      // containers main
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
      ));
      $container_main_id = $db->lastInsertId('engine4_core_content');

      // containers main middle
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_main_id,
        'order' => 6,
        'params' => '',
      ));
      $container_main_middle_id = $db->lastInsertId('engine4_core_content');

      // containers main right
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_main_id,
        'order' => 5,
        'params' => '',
      ));
      $container_main_right_id = $db->lastInsertId('engine4_core_content');

      // containers bottom
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'bottom',
        'parent_content_id' => null,
        'order' => 6,
        'params' => '',
      ));
      $container_bottom_id = $db->lastInsertId('engine4_core_content');

      // containers bottom middle
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_bottom_id,
        'order' => 2,
        'params' => '',
      ));
      $container_bottom_middle_id = $db->lastInsertId('engine4_core_content');

      // widget top middle 1
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.menu-logo',
        'parent_content_id' => $container_top_middle_id,
        'order' => 3,
        'params' => '{"title":"","name":"core.menu-logo","logo":""}',
      ));
      $widget_top_middle_id_1 = $db->lastInsertId('engine4_core_content');

      // widget top middle 2
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'welcomepagevk.login',
        'parent_content_id' => $container_top_middle_id,
        'order' => 4,
        'params' => '',
      ));
      $widget_top_middle_id_2 = $db->lastInsertId('engine4_core_content');

      // widget main middle
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.html-block',
        'parent_content_id' => $container_main_middle_id,
        'order' => 4,
        'params' => '{"title":"","data":"<img src=\'application/modules/Welcomepagevk/externals/images/bg-welcome.png\' \/>","name":"core.html-block"}',
      ));
      $widget_main_middle_id = $db->lastInsertId('engine4_core_content');

      // widget main right
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'welcomepagevk.signup',
        'parent_content_id' => $container_main_right_id,
        'order' => 3,
        'params' => '',
      ));
      $widget_main_right_id = $db->lastInsertId('engine4_core_content');

	  
	  $select1 = new Zend_Db_Select($db);
	  $select1
	    ->from('engine4_core_pages')
	    ->where('name = ?', 'footer');
	  $info1 = $select1->query()->fetch();      
	  $footer_page_id = $info1['page_id'];
	  
	  $select2 = new Zend_Db_Select($db);
	  $select2
	    ->from('engine4_core_content')
	    ->where('page_id = ?', $footer_page_id)
		->where('type = ?', 'container')
		->where('name = ?', 'main');
		
	  $info2 = $select2->query()->fetch();      
	  $parent_footer_content_id = $info2['content_id'];	  
	  

      // widget footer url
      $db->insert('engine4_core_content', array(
        'page_id' => $footer_page_id,
        'type' => 'widget',
        'name' => 'welcomepagevk.footer-url',
        'parent_content_id' => $parent_footer_content_id,
        'order' => 3,
        'params' => '',
      ));
	  

	  
	  // widget bottom middle 1
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.html-block',
        'parent_content_id' => $container_bottom_middle_id,
        'order' => 3,
        'params' => '{"title":"Lorem ipsum 1","data":"Lorem ipsum."}',
      ));
      $widget_bottom_middle_id_1 = $db->lastInsertId('engine4_core_content');

      // widget bottom middle 2
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.html-block',
        'parent_content_id' => $container_bottom_middle_id,
        'order' => 4,
        'params' => '{"title":"Lorem ipsum 2","data":"Lorem ipsum."}',
      ));
      $widget_bottom_middle_id_2 = $db->lastInsertId('engine4_core_content');

      // widget bottom middle 3
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.html-block',
        'parent_content_id' => $container_bottom_middle_id,
        'order' => 5,
        'params' => '{"title":"Lorem ipsum 3","data":"Lorem ipsum."}',
      ));
      $widget_bottom_middle_id_3 = $db->lastInsertId('engine4_core_content');

      
    }

    // insert redirector to home page
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = 3 AND name = ?', 'welcomepagevk.redirect')
      ->limit(1);
      ;
    $info = $select->query()->fetch();
    if( empty($info) ) {
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = 3 AND type = "container"	AND name = "middle"')
        ->limit(1);
        ;
      $home_page_main_middle_id = $select->query()->fetchObject()->content_id;

      $db->insert('engine4_core_content', array(
        'page_id' => 3,
        'type' => 'widget',
        'name' => 'welcomepagevk.redirect',
        'parent_content_id' => $home_page_main_middle_id,
        'order' => 3,
        'params' => '',
      ));
    }

	$select3 = new Zend_Db_Select($db);
	$select3
	    ->from('engine4_core_themes')
		->where('active = ?', '1');

	$info3 = $select3->query()->fetch();    		
		
	if ( !empty($info3))	
    {
		$name = $info3['name'];
		$db->update('engine4_core_themes', 
			array(
				'active' => '0',
			), 
			array(
				'name = ?' => $name
			)
		);	  
	}	

	// Activate vk-theme
	$db->insert('engine4_core_themes', array(
		'name' => 'vk',
		'title' => 'Vk',
		'active' => '1',
	));
		
    parent::onInstall();

		$db->delete('engine4_core_modules',
			array(
				'name = ?' => 'Theme'
				)					
			);		
  }
}
?>
