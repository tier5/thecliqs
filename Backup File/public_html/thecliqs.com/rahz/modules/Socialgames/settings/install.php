<?php

if (!class_exists('Sepcore_Installer')) {
	require_once(APPLICATION_PATH . '/application/modules/Sepcore/settings/installer.php');
}

class Socialgameswidget_Installer extends Sepcore_Installer {

	function _query(){	
		$this->_addGamesBrowsePage();
		$this->_addGamesFeaturedPage();
		$this->_addGamesFavouritePage();
		$this->_addGamesProfilePage();
		$this->_addUserProfileContent();
		parent::onInstall();
	}
	
	protected function _addGamesBrowsePage()
	{
		$db = $this->getDb();
		
		// profile page
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'socialgames_index_browse')
            ->limit(1)
            ->query()
            ->fetchColumn();
		if( !$page_id ) {
			$db->insert('engine4_core_pages', array(
                'name' => 'socialgames_index_browse',
                'displayname' => 'Socialgames Browse Page',
                'title' => 'Socialgames Browse',
                'description' => 'This page lists games.',
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
			
			// Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			 $main_right_id = $db->lastInsertId();
			 
			 // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 3,
            ));
			 $main_middle_id = $db->lastInsertId();
			 
			 //widgets
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.menu-main',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			 // Insert feature widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.games-featured',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
				'params'  => '{"title":"Featured games"}',
            ));
			
			//INSERT MOST PLAYERS
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.most-players',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
				'params'  => '{"title":"Top Players"}',
            ));
			
			 // Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
			// Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
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


		// blog.profile-blogs

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select
		  ->from('engine4_core_content')
		  ->where('page_id = ?', $page_id)
		  ->where('type = ?', 'widget')
		  ->where('name = ?', 'socialgames.profile-games')
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
			'name'    => 'socialgames.profile-games',
			'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
			'order'   => 6,
			'params'  => '{"title":"My Games"}',
		  ));

		}
	}
	protected function _addGamesFavouritePage()
	{
		$db = $this->getDb();
		
		// profile page
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'socialgames_index_favourite')
            ->limit(1)
            ->query()
            ->fetchColumn();
		if( !$page_id ) {
			$db->insert('engine4_core_pages', array(
                'name' => 'socialgames_index_favourite',
                'displayname' => 'Socialgames Favourites Page',
                'title' => 'Socialgames Favourite',
                'description' => 'This page lists favourite games.',
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
			
			// Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			 $main_right_id = $db->lastInsertId();
			 
			 // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 3,
            ));
			 $main_middle_id = $db->lastInsertId();
			 
			 //widgets
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.menu-main',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			 // Insert feature widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.games-featured',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
				'params'  => '{"title":"Featured games"}',
            ));
			
			//INSERT MOST PLAYERS
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.most-players',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
				'params'  => '{"title":"Top Players"}',
            ));
			
			 // Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
			// Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
		}
	}
	protected function _addGamesFeaturedPage()
	{
		$db = $this->getDb();
		
		// profile page
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'socialgames_index_featured')
            ->limit(1)
            ->query()
            ->fetchColumn();
		if( !$page_id ) {
			$db->insert('engine4_core_pages', array(
                'name' => 'socialgames_index_featured',
                'displayname' => 'Socialgames Featured Page',
                'title' => 'Featured games',
                'description' => 'This page lists featured games.',
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
			
			// Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
			 $main_right_id = $db->lastInsertId();
			 
			 // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 3,
            ));
			 $main_middle_id = $db->lastInsertId();
			 
			 //widgets
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.menu-main',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			//INSERT MOST PLAYERS
			$db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.most-players',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
				'params'  => '{"title":"Top Players"}',
            ));
			
			 // Insert feature widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.games-featured',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
				'params'  => '{"title":"Featured games"}',
            ));
			
			 // Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'socialgames.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
			// Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
		}
		}
		
		protected function _addGamesProfilePage()
		{
			$db = $this->getDb();
			
			// profile page
			$page_id = $db->select()
				->from('engine4_core_pages', 'page_id')
				->where('name = ?', 'socialgames_game_index')
				->limit(1)
				->query()
				->fetchColumn();
			if( !$page_id ) {
				$db->insert('engine4_core_pages', array(
					'name' => 'socialgames_game_index',
					'displayname' => 'Socialgames Profile',
					'title' => '',
					'description' => 'game profile',
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
					'order' => 3,
				));
				 $main_middle_id = $db->lastInsertId();
				 
				 //widgets
				$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'socialgames.menu-main',
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
					'order' => 2,
				));
				
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'socialgames.similar-games',
					'parent_content_id' => $main_middle_id,
					'order' => 4,
					'params'  => '{"title":"Similar games"}',
				));
			}
	}
}