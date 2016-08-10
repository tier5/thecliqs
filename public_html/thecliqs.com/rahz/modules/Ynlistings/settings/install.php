<?php
class Ynlistings_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
        $this->_addUserProfileContent();
        $this->_addHomeListingsPage();
        $this->_addManageListingsPage();
        $this->_addBrowseListingsPage();
        $this->_addPostListingPage();
        $this->_addFaqsPage();
		$this->_addImportListingsPage();
		$this->_addViewListingPage();
        $this->_addPrintListingPage();
		$this->_addEditListingPage();
		$this->_addMobileViewListingPage();
        parent::onInstall();
    }
    
    protected function _addUserProfileContent() {
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

    // ynlistings.profile-listings
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'ynlistings.profile-listings');
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
            'name'    => 'ynlistings.profile-listings',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 999,
        'params'  => '{"title":"Listings","titleCount":true}',
      ));

    }
  }
	
	protected function _addMobileViewListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_mobileview')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_mobileview',
                'displayname' => 'YN - Listings Mobile Profile Listing Page',
                'title' => 'Mobile Profile listing page',
                'description' => 'Mobile Profile listing page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
			
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
			//Insert location
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-location',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Location"}',
            ));
			
			//Insert about
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-about',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"About Us"}',
            ));
			
			//Insert tag
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listings-tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Tags"}',
            ));
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                      
			
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $main_middle_id,
				'order' => 2,
				'params' => '{"max":"8"}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');
			
			 //Insert Activity
			
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $tab_id,
				'order' => 1,
				'params' => '{"title":"Activity"}',
			));
			
			 //Insert Info
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-info',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 2,
                'params' => '{"title":"Info"}',
            ));     
			     
			 //Insert Review
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-reviews',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 3,
                'params' => '{"title":"Reviews"}',
            ));    
			
			 //Insert Album
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-albums',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 4,
                'params' => '{"title":"Albums"}',
            ));    
			
			 //Insert Video
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-videos',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 5,
                'params' => '{"title":"Videos"}',
            ));    
			
			 //Insert Discusstion
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-discussions',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 6,
                'params' => '{"title":"Discussion"}',
            ));
            
            //insert related listings
            $db -> insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynlistings.related-listings',
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));    
        }
    }
	
	protected function _addViewListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_view',
                'displayname' => 'YN - Listings Profile Listing Page',
                'title' => 'Profile listing page',
                'description' => 'Profile listing page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
			
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
			//Insert location
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-location',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Location"}',
            ));
			
			//Insert about
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-about',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"About Us"}',
            ));
			
			//Insert tag
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listings-tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Tags"}',
            ));
			
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                      
			
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $main_middle_id,
				'order' => 2,
				'params' => '{"max":"8"}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');
			
			 //Insert Info
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-info',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 1,
                'params' => '{"title":"Info"}',
            ));     
			     
			 //Insert Activity
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'activity.feed',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 2,
                'params' => '{"title":"Activity"}',
            ));        
			
			 //Insert Review
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-reviews',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 3,
                'params' => '{"title":"Reviews"}',
            ));    
			
			 //Insert Album
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-albums',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 4,
                'params' => '{"title":"Albums"}',
            ));    
			
			 //Insert Video
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-videos',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 5,
                'params' => '{"title":"Videos"}',
            ));    
			
			 //Insert Discusstion
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-discussions',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 6,
                'params' => '{"title":"Discussion"}',
            ));
            
            //insert related listings
            $db -> insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynlistings.related-listings',
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));    
        }
    }
	
    protected function _addPrintListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_print')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_print',
                'displayname' => 'YN - Listings Print Listing Page',
                'title' => 'Print listing page',
                'description' => 'Print listing page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert location
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-location',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Location"}',
            ));
            
            //Insert about
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-about',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"About Us"}',
            ));
            
            //Insert tag
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listings-tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Tags"}',
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                      
            
            $db -> insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"max":"8"}',
            ));
            $tab_id = $db -> lastInsertId('engine4_core_content');
            
             //Insert Info
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listing-info',
                'page_id' => $page_id,
                'parent_content_id' => $tab_id,
                'order' => 1,
                'params' => '{"title":"Info"}',
            ));     
        }
    }

    protected function _addHomeListingsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_index',
                'displayname' => 'YN - Listings Home Page',
                'title' => 'Home Listings',
                'description' => 'This page is listing home page.',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            // Insert search
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            // Insert most like
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.most-liked-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
                'params' => '{"title":"Most Liked"}',
            ));
            
            // Insert most discuss
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.most-discussion-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Most Discussion"}',
            ));
            
            // Insert most review
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.most-reviewed-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Most Reviewed"}',
            ));
            
            // Insert recently viewed
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.recently-viewed',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Recently Viewed"}',
            ));
            
            // Insert listings you may like
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listings-you-may-like',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 6,
                'params' => '{"title":"You May Like"}',
            ));
                        
            // Insert tag cloud
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.listings-tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 7,
                'params' => '{"title":"Tags"}',
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert feature listings
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.featured-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
            
            //Insert browse category
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.browse-category',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
            
            //Insert list most items
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.list-most-items',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 4,
            ));                           
        }
    }

    protected function _addManageListingsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_manage',
                'displayname' => 'YN - Listings My Listings Page',
                'title' => 'My Listings',
                'description' => 'This page lists a user\'s listings',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            // Insert search
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }
    
    protected function _addBrowseListingsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_browse')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_browse',
                'displayname' => 'YN - Listings Browse Listings Page',
                'title' => 'Browse Listings',
                'description' => 'This page lists search result listings',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId(); 
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            // Insert highlight listing
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.highlight-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            // Insert search
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.browse-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
            
            // Insert category
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.list-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));
            
            //Insert browse listings widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.browse-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }
	
	protected function _addImportListingsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_import')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_import',
                'displayname' => 'YN - Listings Import listings',
                'title' => 'Import listings',
                'description' => 'Import listings',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }
	
    protected function _addPostListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_create')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_create',
                'displayname' => 'YN - Listings Post a new listing',
                'title' => 'Post a new listing',
                'description' => 'Post a new listing',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }

	 protected function _addEditListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_index_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_index_edit',
                'displayname' => 'YN - Listings Edit Listing',
                'title' => 'Edit Listing',
                'description' => 'Edit listing',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }

    protected function _addFaqsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynlistings_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynlistings_faqs_index',
                'displayname' => 'YN - Listings FAQs Page',
                'title' => 'FAQs',
                'description' => 'This page show the FAQs',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynlistings.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }
}