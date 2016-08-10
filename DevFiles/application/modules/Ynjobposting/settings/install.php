<?php
class Ynjobposting_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
		$this -> _addJobPostingHomePage();
		$this -> _addJobPostingListingPage();
		$this -> _addJobPostingCompanyPage();
		$this -> _addCreateCompanyPage();
		$this -> _addEditCompanyPage();
        $this -> _addCreateJobPage();
        $this -> _addEditJobPage();
        $this -> _addJobDetailPage();
        $this -> _addCompanyDetailPage();
        $this -> _addCompanyManagePage();
        $this -> _addCompanySubmissionPage();
		$this -> _addCompanyManageJobsPage();
		$this -> _addCompanyManageFollowPage();
		$this -> _addJobViewApplicationPage();
        $this -> _addFaqsPage();
        $this -> _addJobManagePage();
        $this -> _addJobApplyPage();
        $this -> _addUserProfileContent();
		parent::onInstall();
		
		$this ->_addColumns();
    }
    
	protected function _addColumns() {
		$sql = "ALTER TABLE  `engine4_ynjobposting_jobapplies` ADD  `resume` tinyint(1) NOT NULL DEFAULT '0'";
		$db = $this -> getDb();
		try {
			$info = $db -> describeTable('engine4_ynjobposting_jobapplies');
			if ($info && !isset($info['resume'])) {
				try {
					$db -> query($sql);
				} catch( Exception $e ) {
				}
			}
		} catch (Exception $e) {
		}
	}
	
	protected function _addJobViewApplicationPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_jobs_applications')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_jobs_applications',
                'displayname' => 'Job Posting View Application',
                'title' => 'Job Posting View Application',
                'description' => 'Job Posting View Application',
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
                'name' => 'ynjobposting.browse-menu',
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
	
	protected function _addJobPostingListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_jobs_listing')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_jobs_listing',
                'displayname' => 'Job Posting Job Listing Page',
                'title' => 'Job Posting Job Listing Page',
                'description' => 'Job Posting Job Listing Page',
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
            
            
            //Insert search-job
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.job-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.jobs-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                   
			
        }
    }
	
	
    /**
     * Build Browse Companies page
     */
	protected function _addJobPostingCompanyPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_company_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_company_index',
                'displayname' => 'Job Posting Browse Company Page',
                'title' => 'Job Posting Browse Company Page',
                'description' => 'Job Posting Browse Company Page',
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
                'name' => 'ynjobposting.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert ynjobposting.company-listing
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.company-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert ynjobposting.company-search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.company-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert ynjobposting.list-industries widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.list-industries',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            	'params' => '{"title":"Industries"}',
            ));
            
            //Insert ynjobposting.job-you-may-be-interested widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.job-you-may-be-interested',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
            	'params' => '{"title":"Interesting Jobs"}',
            ));
            
            //Insert ynjobposting.jobs-tags widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.jobs-tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
            ));
        }
    }
    
	protected function _addCompanyManagePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_company_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_company_manage',
                'displayname' => 'Job Posting Company Manage Page',
                'title' => 'Job Posting Company Manage Page',
                'description' => 'Job Posting Company Manage Page',
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
                'name' => 'ynjobposting.browse-menu',
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
	
	protected function _addCompanyManageFollowPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_company_manage-follow')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_company_manage-follow',
                'displayname' => 'Job Posting Company Manage Following Page',
                'title' => 'Job Posting Company Manage Following Page',
                'description' => 'Job Posting Company Manage Following Page',
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
                'name' => 'ynjobposting.browse-menu',
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
	
	protected function _addCompanySubmissionPage()
    {
    	$db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_submission_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_submission_edit',
                'displayname' => 'Job Posting Company Submission Page',
                'title' => 'Job Posting Company Submission Page',
                'description' => 'Job Posting Company Submission Page',
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
                'name' => 'ynjobposting.browse-menu',
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
    
	protected function _addCompanyManageJobsPage()
    {
    	$db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_company_manage-jobs')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_company_manage-jobs',
                'displayname' => 'Job Posting Company Manage Posted Page',
                'title' => 'Job Posting Company Manage Posted Page',
                'description' => 'Job Posting Company Manage Posted Page',
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
                'name' => 'ynjobposting.browse-menu',
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
    
    
    protected function _addCompanyDetailPage()
    {
    	$db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_company_detail')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_company_detail',
                'displayname' => 'Job Posting Company Detail Page',
                'title' => 'Job Posting Company Detail Page',
                'description' => 'Job Posting Company Detail Page',
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
                'name' => 'ynjobposting.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert profile cover
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.company-profile-cover',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            )); 
            
            //Insert profile description
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.company-profile-description',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            )); 
            
            // Insert tab container 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 3,
				'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
			));
			$main_container_id = $db -> lastInsertId();

			// Insert company profile info
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.company-profile-info',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 1,
				'params' => '{"title":"General Information"}',
			));
				
			// Insert activity.feed
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'activity.feed',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 2,
				'params' => '{"title":"Updates"}',
			));
			
			// Insert company profile info
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.company-profile-jobs',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 3,
				'params' => '{"title":"Jobs"}',
			));
        }
    }
    
    protected function _addJobDetailPage() {
		$db     = $this->getDb();
		$page_id = $db 
		-> select() 
		-> from('engine4_core_pages', 'page_id') 
		-> where('name = ?', 'ynjobposting_jobs_view') 
		-> limit(1) 
		-> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynjobposting_jobs_view',
				'displayname' => 'Job Posting Job View Page',
				'title' => 'Job Posting Job View Page',
				'description' => 'Job Posting Job View Page',
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
				'order' => 1,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));
			
			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 6,
			));
			$main_middle_id = $db -> lastInsertId();

			//Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
			// Insert main-left
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'left',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 5,
			));
			$main_left_id = $db -> lastInsertId();
			
			// Insert ynjobposting.job-profile-photo
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.job-profile-photo',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 6,
			));
			
			// Insert ynjobposting.job-profile-option
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.job-profile-option',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 7,
			));
            
            // Insert ynjobposting.job-profile-addthis
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.job-profile-addthis',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 8,
            ));
			
			// Insert ynjobposting.job-profile-info
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.job-profile-info',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 9,
			));
			
			// Insert ynjobposting.job-related
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.job-related',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 10,
				'params' => '{"title":"Job Related"}',
			));
			
			// Insert ynjobposting.job-company
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynjobposting.job-company',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 11,
				'params' => '{"title":"More Jobs"}',
			));
			
		}
    }
    
	protected function _addEditCompanyPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_company_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_company_edit',
                'displayname' => 'Job Posting Company Edit Page',
                'title' => 'Job Posting Company Edit Page',
                'description' => 'Job Posting Company Edit Page',
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
                'name' => 'ynjobposting.browse-menu',
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
	
	protected function _addCreateCompanyPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_company_create')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_company_create',
                'displayname' => 'Job Posting Company Create Page',
                'title' => 'Job Posting Company Create Page',
                'description' => 'Job Posting Company Create Page',
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
                'name' => 'ynjobposting.browse-menu',
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
	
	protected function _addJobPostingHomePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_index_index',
                'displayname' => 'Job Posting Home Page',
                'title' => 'Job Posting Home Page',
                'description' => 'Job Posting Home Page',
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
            
            
            //Insert search-job
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.job-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert ynjobposting.job-alert widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.job-alert',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            	'params' => '{"title":"Job Alert"}',
            ));
            
            //Insert industries
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.list-industries',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Industries"}',
            ));
            
            //Insert interesting jobs
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.job-you-may-be-interested',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Interesting Jobs"}',
            ));
            
            //Insert jobs tag
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.jobs-tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Tags"}',
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
            //Insert featured jobs
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.featured-jobs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
                'params' => '{"title":"Featured Jobs"}',
            )); 
            
            //Insert Sponsored Companies
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.sponsored-companies',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"title":"Sponsored Companies"}',
            )); 

            //Insert tab container
            $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'core.container-tabs',
            	'page_id' => $page_id,
		        'parent_content_id' => $main_middle_id,
		        'order' => 3,
		        'params' => '{"max":"6"}',
		      ));
		      $tab_id = $db->lastInsertId('engine4_core_content');

		    // Insert Newest Jobs
			$db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'ynjobposting.newest-job',
			 	'page_id' => $page_id,
		        'parent_content_id' => $tab_id,
		        'order' => 1,
		        'params' => '{"title":"Newest Jobs"}',
		      ));
		      
		    // Insert Most Viewed Jobs
			$db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'ynjobposting.most-viewed-job',
			 	'page_id' => $page_id,
		        'parent_content_id' => $tab_id,
		        'order' => 2,
		        'params' => '{"title":"Most Viewed Jobs"}',
		      ));
		      
		    // Insert Hot Jobs  
		   	$db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'ynjobposting.hot-job',
			 	'page_id' => $page_id,
		        'parent_content_id' => $tab_id,
		        'order' => 3,
		        'params' => '{"title":"Hot Jobs"}',
		      ));
		      
		    //Insert Job Posting Hot Companies
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynjobposting.hot-companies',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 4,
                'params' => '{"title":"Hot Companies"}',
            ));
        }
    }

    protected function _addCreateJobPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_jobs_create')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_jobs_create',
                'displayname' => 'Job Posting Job Create Page',
                'title' => 'Job Posting Job Create Page',
                'description' => 'Job Posting Job Create Page',
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
                'name' => 'ynjobposting.browse-menu',
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

    protected function _addEditJobPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_jobs_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_jobs_edit',
                'displayname' => 'Job Posting Job Edit Page',
                'title' => 'Job Posting Job Edit Page',
                'description' => 'Job Posting Job Edit Page',
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
                'name' => 'ynjobposting.browse-menu',
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
            ->where('name = ?', 'ynjobposting_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_faqs_index',
                'displayname' => 'Job Posting FAQs Page',
                'title' => 'Job Posting FAQs Page',
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
                'name' => 'ynjobposting.browse-menu',
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

    protected function _addjobManagePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_jobs_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_jobs_manage',
                'displayname' => 'Job Posting Job Manage Page',
                'title' => 'Job Posting Job Manage Page',
                'description' => 'Job Posting Job Manage Page',
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
                'name' => 'ynjobposting.browse-menu',
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

    protected function _addjobApplyPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynjobposting_jobs_apply')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynjobposting_jobs_apply',
                'displayname' => 'Job Posting Job Apply Page',
                'title' => 'Job Posting Job Apply Page',
                'description' => 'Job Posting Job Apply Page',
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
                'name' => 'ynjobposting.browse-menu',
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

        // applied job
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'ynjobposting.applied-jobs');
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
                'name'    => 'ynjobposting.applied-jobs',
                'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                'order'   => 999,
                'params'  => '{"title":"Applied Jobs","titleCount":true}',
            ));
        }

        // following company
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'ynjobposting.following-companies');
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
                'name'    => 'ynjobposting.following-companies',
                'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
                'order'   => 999,
                'params'  => '{"title":"Following Companies","titleCount":true}',
            ));

        }
    }     
}