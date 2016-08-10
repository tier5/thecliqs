<?php
class Ynresume_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
    	$this -> _addResumeBrowsePage();
    	$this -> _addResumeListingPage();
		$this -> _addWhoViewedMePage();
		$this -> _addMyFavouriteResumePage();
        $this -> _addMySavedResumePage();
        $this -> _addMyResumePage();
        $this -> _addResumeDetailPage();
        $this -> _addResumeEditPrivacyPage();
        
        $this->_addReceivedRecommendationPage();
        $this->_addGivenRecommendationPage();
        $this->_addAskRecommendationPage();
        $this->_addGiveRecommendationPage();
        $this->_addImportResumePage();
        $this -> _addFaqsPage();
		parent::onInstall();
    }
    
    protected function _addImportResumePage()
    {
    	$db = $this -> getDb();
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynresume_index_import') -> limit(1) -> query() -> fetchColumn();
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynresume_index_import',
				'displayname' => 'Resume Import and Export Page',
				'title' => 'Resume Import and Export Page',
				'description' => 'This page allows users to import resume from LinkedIn or export resume to PDF or Docx files.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();
			
			//Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
    }
    
    protected function _addResumeBrowsePage()
    {
    	$db = $this->getDb();
    	
	    $page_id = $db->select()
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ynresume_index_index')
	      ->limit(1)
	      ->query()
	      ->fetchColumn();
	      
		if(!$page_id) 
		{
	      	$db->insert('engine4_core_pages', array(
                'name' => 'ynresume_index_index',
                'displayname' => 'Resume Browse Page',
                'title' => 'Resume Browse Page',
                'description' => 'Resume Browse Page',
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
            
            // Insert main-right
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
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert featured resume
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.featured-resume',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
                'params' => '{"title":"Featured Resume"}',
            ));
            
            //Insert Tab Container
            $db->insert('engine4_core_content', array(
		        'page_id' => $page_id,
		        'type' => 'widget',
		        'name' => 'core.container-tabs',
		        'parent_content_id' => $main_middle_id,
		        'order' => 2,
		        'params' => '{"max":"4"}',
			));
			$tab_id = $db->lastInsertId('engine4_core_content');
			
			//Insert Newest Resumes widget			
			$db->insert('engine4_core_content', array(
		        'page_id' => $page_id,
		        'type' => 'widget',
		        'name' => 'ynresume.newest-resume',
		        'parent_content_id' => $tab_id,
		        'order' => 1,
		        'params' => '{"title":"Newest Resumes"}',
			));
			
			//Insert Most Viewed Resumes widget			
			$db->insert('engine4_core_content', array(
		        'page_id' => $page_id,
		        'type' => 'widget',
		        'name' => 'ynresume.most-viewed-resume',
		        'parent_content_id' => $tab_id,
		        'order' => 2,
		        'params' => '{"title":"Most Viewed Resumes"}',
			));
			
			//Insert Most Endorsed Resumes widget			
			$db->insert('engine4_core_content', array(
		        'page_id' => $page_id,
		        'type' => 'widget',
		        'name' => 'ynresume.most-endorsed-resume',
		        'parent_content_id' => $tab_id,
		        'order' => 3,
		        'params' => '{"title":"Most Endorsed"}',
			));
			
			//Insert Most Favorite Resumes widget			
			$db->insert('engine4_core_content', array(
		        'page_id' => $page_id,
		        'type' => 'widget',
		        'name' => 'ynresume.most-favorite-resume',
		        'parent_content_id' => $tab_id,
		        'order' => 4,
		        'params' => '{"title":"Most Favorite"}',
			));
			
			// Insert search
			$db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'ynresume.resume-search',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_right_id,
		        'order' => 1,
				'params' => '{"title":"Search Resume"}',
			));
			
            //Insert Who View Your Resume
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.who-viewed-your-resume',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            	'params' => '{"title":"Who Viewed Your Resume"}',
            ));
		}
    }
    
	protected function _addResumeListingPage()
    {
    	$db = $this->getDb();
    	
	    $page_id = $db->select()
	      ->from('engine4_core_pages', 'page_id')
	      ->where('name = ?', 'ynresume_index_listing')
	      ->limit(1)
	      ->query()
	      ->fetchColumn();
	      
		if(!$page_id) 
		{
	      	$db->insert('engine4_core_pages', array(
                'name' => 'ynresume_index_listing',
                'displayname' => 'Resume Listing Page',
                'title' => 'Resume Listing Page',
                'description' => 'Resume Listing Page',
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
            
            // Insert main-right
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
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            // Insert search
			$db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'ynresume.resume-search',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_right_id,
		        'order' => 1,
				'params' => '{"title":"Search Resume"}',
			));
			
            //Insert Browse Resumes widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
		        'name' => 'ynresume.resume-listing',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_middle_id,
		        'order' => 1,
            ));
		}
    }
    
	protected function _addWhoViewedMePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_index_who-viewed-me')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_index_who-viewed-me',
                'displayname' => 'Resume Who Viewed Me Page',
                'title' => 'Resume Who Viewed Me Page',
                'description' => 'Resume Who Viewed Me Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
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
	
	protected function _addMyFavouriteResumePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_index_my-favourite')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_index_my-favourite',
                'displayname' => 'Resume My Favourite Resumes Page',
                'title' => 'Resume My Favourite Resumes Page',
                'description' => 'Resume My Favourite Resumes Page',
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
            
            // Insert main-right
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
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.resume-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Search Resume"}',
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
    
    protected function _addMySavedResumePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_index_my-saved')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_index_my-saved',
                'displayname' => 'Resume My Saved Resumes Page',
                'title' => 'Resume My Saved Resumes Page',
                'description' => 'Resume My Saved Resumes Page',
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
            
            // Insert main-right
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
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            // Insert search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.resume-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Search Resume"}',
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

    protected function _addMyResumePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_index_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_index_manage',
                'displayname' => 'Resume My Resume Page',
                'title' => 'Resume My Resume Page',
                'description' => 'Resume My Resume Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
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
            
            //Insert my resume cover widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.my-resume-cover',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            )); 
            
            //Insert manage sections of my resume
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.manage-sections',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));                      
        }
    }

    protected function _addResumeDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_resume_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_resume_view',
                'displayname' => 'Resume Detail Page',
                'title' => 'Resume Detail Page',
                'description' => 'Resume Detail Page',
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
			
			// Insert main-right
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
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            
            //Insert endorse suggestion widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.endorse-suggestion',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            )); 
            
            //Insert my resume cover widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.view-resume-cover',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            )); 
            
            //Insert view sections of resume
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.view-sections',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 4,
            ));
			
			//Insert suggestions widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.profile-suggestions',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Suggestions"}',
            ));
			
			//Insert interested jobs widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.interested-jobs',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
                'params' => '{"title":"Job You May Interested In"}',
            ));                      
        }
    }

    protected function _addResumeEditPrivacyPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_resume_edit-privacy')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_resume_edit-privacy',
                'displayname' => 'Resume Edit Resume Privacy Page',
                'title' => 'Resume Edit Resume Privacy Page',
                'description' => 'Resume Edit Resume Privacy Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
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
    
    protected function _addReceivedRecommendationPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_recommendation_received')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_recommendation_received',
                'displayname' => 'Resume Received Commendations Page',
                'title' => 'Resume Received Commendations Page',
                'description' => 'Resume Received Commendations Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert recommendation menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert received recommendations widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-received',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
        }
    }

    protected function _addGivenRecommendationPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_recommendation_given')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_recommendation_given',
                'displayname' => 'Resume Given Commendations Page',
                'title' => 'Resume Given Commendations Page',
                'description' => 'Resume Given Commendations Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert recommendation menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert given recommendations widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-given',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
        }
    }
    
    protected function _addAskRecommendationPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_recommendation_ask')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_recommendation_ask',
                'displayname' => 'Resume Ask for Commendations Page',
                'title' => 'Resume Ask for Commendations Page',
                'description' => 'Resume Ask for Commendations Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert recommendation menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert ask recommendations widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-ask',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
        }
    }

    protected function _addGiveRecommendationPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_recommendation_give')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_recommendation_give',
                'displayname' => 'Resume Give Commendations Page',
                'title' => 'Resume Give Commendations Page',
                'description' => 'Resume Give Commendations Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert recommendation menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert request recommendations widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-request',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            )); 
            
            //Insert give recommendations widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.recommendation-give',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
            ));
        }
    }

    protected function _addFaqsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynresume_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynresume_faqs_index',
                'displayname' => 'Resume FAQs Page',
                'title' => 'Resume FAQs Page',
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynresume.main-menu',
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