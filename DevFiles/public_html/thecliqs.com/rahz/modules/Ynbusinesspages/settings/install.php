<?php
class Ynbusinesspages_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
    	
		$this -> _addBrowseBusinessesPage();
		$this -> _addBusinessesListingPage();
		$this -> _addMyBusinessesPage();
		$this -> _addMyClaimBusinessesPage();
		$this -> _addMyFavouriteBusinessesPage();
		$this -> _addMyFollowBusinessesPage();
		
		$this -> _addCreateNewBusinessPage();
		$this -> _addCreateNewClaimBusinessPage();
		$this -> _addCreateStep1BusinessPage();
		$this -> _addCreateStep2BusinessPage();
        $this -> _addFaqsPage();
        $this -> _addComparePage();
		
		$this -> _addBusinessDetailPage();
		
		$this -> _addDashboardStatisticsPage();
		$this -> _addDashboardContactPage();
		$this -> _addDashboardAnnouncementPage();
		$this -> _addDashboardTransferOwnerPage();
		$this -> _addDashboardThemePage();
		$this -> _addDashboardFeaturePage();
		$this -> _addDashboardPackagePage();
		$this -> _addDashboardEditBusinessPage();
        $this -> _addDashboardModulePage();
		$this -> _addDashboardManageLayoutPage();
        $this -> _addDashboardCoverPage();
        $this -> _addDashboardManageRolePage();
        $this -> _addDashboardRoleSettingPage();
        
		parent::onInstall();
		
		$this ->_addMethodColumns();
    }
    
	protected function _addMethodColumns() {
		$sql = "ALTER TABLE  `engine4_ynbusinesspages_business` ADD  `never_expire` TINYINT( 1 ) NOT NULL DEFAULT  '0'";
		$sql2 = "ALTER TABLE  `engine4_ynbusinesspages_packages` ADD  `category_id` TEXT NOT NULL";
		$db = $this -> getDb();
		try {
			$info = $db -> describeTable('engine4_ynbusinesspages_business');
			if ($info && !isset($info['never_expire'])) {
				$db -> query($sql);
			}
			
			$info2 = $db -> describeTable('engine4_ynbusinesspages_packages');
			if ($info2 && !isset($info2['category_id'])) {
				$db -> query($sql2);
			}
			
		} catch (Exception $e) {
		}
	}
	
	protected function _addDashboardContactPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_contact_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_contact_edit',
                'displayname' => 'YN - Business Edit Contact Form Page',
                'title' => 'Business Pages Edit Contact Form Page',
                'description' => 'Business Pages Edit Contact Form Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                    
        }
    }  
	
	protected function _addDashboardAnnouncementPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_announcement_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_announcement_manage',
                'displayname' => 'YN - Business Manage Announcement Page',
                'title' => 'Business Pages Manage Announcement Page',
                'description' => 'Business Pages Manage Announcement Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                    
        }
    }  
	
	protected function _addDashboardTransferOwnerPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_business_transfer')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_business_transfer',
                'displayname' => 'YN - Business Transfer Owner Page',
                'title' => 'Business Pages Transfer Owner Page',
                'description' => 'Business Pages Transfer Owner Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                    
        }
    }  
	
	protected function _addDashboardThemePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_theme')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_theme',
                'displayname' => 'YN - Business Theme Page',
                'title' => 'Business Pages Theme Page',
                'description' => 'Business Pages Theme Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                    
        }
    }  
	
	protected function _addDashboardFeaturePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_feature')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_feature',
                'displayname' => 'YN - Business Feature Page',
                'title' => 'Business Pages Package Page',
                'description' => 'Business Pages Feature Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                    
        }
    }  
	
	protected function _addDashboardPackagePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_package')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_package',
                'displayname' => 'YN - Business Package Page',
                'title' => 'Business Pages Package Page',
                'description' => 'Business Pages Package Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                    
        }
    }  
	
	protected function _addDashboardEditBusinessPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_business_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_business_edit',
                'displayname' => 'YN - Business Edit Business Page',
                'title' => 'Business Pages Edit Business Page',
                'description' => 'Business Pages Edit Business Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));                    
        }
    }   
	
	protected function _addCreateStep2BusinessPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_create-step-two')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_create-step-two',
                'displayname' => 'YN - Business Create New Business Main Page',
                'title' => 'Business Pages Create New Business Main Page',
                'description' => 'Business Pages Create New Business Main Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

	protected function _addCreateStep1BusinessPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_create-step-one')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_create-step-one',
                'displayname' => 'YN - Business Choose Package Page',
                'title' => 'Business Pages Choose Package Page',
                'description' => 'Business Pages Choose Package Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
	
	protected function _addBrowseBusinessesPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_index',
                'displayname' => 'YN - Business Browse Businesses',
                'title' => 'Browse Businesses',
                'description' => 'Business pages Browse Businesses',
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
                'name' => 'ynbusinesspages.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert featured businesses widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.featured-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"title":"Featured Business"}',
            )); 
            
            //Insert browse categories widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.browse-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"Categories"}',
            ));
            
            //Insert newest businesses widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.newest-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 4,
                'params' => '{"title":"Newest Businesses"}',
            )); 
            
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
			//Insert profile quick create link
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-create-link',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
			
            //Insert categories list widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.list-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Categories"}',
            ));
            
            //Insert most liked businesses widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.most-liked-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Most Liked Businesses"}',
            ));
            
            //Insert most viewed businesses widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.most-viewed-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Most Viewed Businesses"}',
            ));
            
            //Insert most rated businesses widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.most-rated-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 6,
                'params' => '{"title":"Most Rated Businesses"}',
            ));
            
            //Insert recent reviews widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.recent-reviews',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 7,
                'params' => '{"title":"Recent Reviews"}',
            ));
            
            //Insert businesses you may like widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.businesses-you-may-like',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 8,
                'params' => '{"title":"Businesses You May Like"}',
            ));
            
            //Insert most discussed businesses widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.most-discussed-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 9,
                'params' => '{"title":"Most Discussed Businesses"}',
            ));
            
            //Insert most checked-in businesses widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.most-checkedin-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 10,
                'params' => '{"title":"Most Checked-in Businesses"}',
            ));
            
            //Insert businesses tags widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.businesses-tags',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 11,
                'params' => '{"title":"Tags"}',
            ));                       
        }
    }
	
	protected function _addBusinessesListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_listing')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_listing',
                'displayname' => 'YN - Business Businesses Listing Page',
                'title' => 'Business Pages Businesses Listing Page',
                'description' => 'Business Pages Businesses Listing Page',
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
                'name' => 'ynbusinesspages.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.businesses-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                   
			
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
			//Insert profile quick create link
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-create-link',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
			
            //Insert categories list widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.list-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Categories"}',
            ));
        }
    }
	
	protected function _addMyBusinessesPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_manage',
                'displayname' => 'YN - Business My Businesses Page',
                'title' => 'Business Pages My Businesses Page',
                'description' => 'Business Pages My Businesses Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
            
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
        }
    }

	protected function _addMyClaimBusinessesPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_manage-claim')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_manage-claim',
                'displayname' => 'YN - Business My Claim Businesses Page',
                'title' => 'Business Pages My Claim Businesses Page',
                'description' => 'Business Pages My Claim Businesses Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
            
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
        }
    }

	protected function _addMyFollowBusinessesPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_manage-follow')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_manage-follow',
                'displayname' => 'YN - Business My Following Businesses Page',
                'title' => 'Business Pages My Following Businesses Page',
                'description' => 'Business Pages My Following Businesses Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
            
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
        }
    }

	protected function _addMyFavouriteBusinessesPage() { 
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_manage-favourite')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_manage-favourite',
                'displayname' => 'YN - Business My Favourite Businesses Page',
                'title' => 'Business Pages My Favourite Businesses Page',
                'description' => 'Business Pages My Favourite Businesses Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
            
			//Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
        }
    }
    
	protected function _addCreateNewClaimBusinessPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_create-for-claiming')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_create-for-claiming',
                'displayname' => 'YN - Business Create New Claim Business Page',
                'title' => 'Business Pages Create New Claim Business Page',
                'description' => 'Business Pages Create New Claim Business Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
	
    protected function _addCreateNewBusinessPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_index_create')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_index_create',
                'displayname' => 'YN - Business Create New Business Page',
                'title' => 'Business Pages Create New Business Page',
                'description' => 'Business Pages Create New Business Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
            ->where('name = ?', 'ynbusinesspages_faqs_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_faqs_index',
                'displayname' => 'YN - Business FAQs Page',
                'title' => 'Business Pages FAQs Page',
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
                'name' => 'ynbusinesspages.main-menu',
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
	
	protected function _addBusinessDetailPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_profile_index',
                'displayname' => 'YN - Business Detail Page',
                'title' => 'Business Pages Detail Page',
                'description' => 'This page show the informations of Business Pages',
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
                'name' => 'ynbusinesspages.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
			//Insert cover style 2 widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-cover-style2',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 2,
            ));
            
            //Insert cover style 1 widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-cover-style1',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
			
			//Insert cover style 1 widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-cover-style3',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            // Insert tab container 
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $main_container_id = $db -> lastInsertId();
            
             // Insert businesses may like widget
            $db -> insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-related-businesses',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"Related Businesses"}',
            ));
            
            //Insert overview widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-overview',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 1,
                'params' => '{"title":"Overview"}',
            ));
            
            //Insert activity widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'activity.feed',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 2,
                'params' => '{"title":"Activity"}',
            ));
            
            //Insert review widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-reviews',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 3,
                'params' => '{"title":"Reviews", "titleCount": true}',
            ));
            
            //Insert contact us widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-contact',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 4,
                'params' => '{"title":"Contact Us"}',
            ));
            
            //Insert profile members widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-members',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 5,
                'params' => '{"title":"Members", "titleCount": true}',
            ));
            
            
            //Insert profile blog widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-blogs',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 6,
                'params' => '{"title":"Blogs", "titleCount": true}',
            ));
            
            //Insert profile classified widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-classifieds',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 7,
                'params' => '{"title":"Classifieds", "titleCount": true}',
            ));
            
            //Insert profile contest widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-contests',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 8,
                'params' => '{"title":"Contests", "titleCount": true}',
            ));
            
            //Insert profile discussion widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-discussions',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 9,
                'params' => '{"title":"Discussions", "titleCount": true}',
            ));
            
            //Insert profile event widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-events',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 10,
                'params' => '{"title":"Events", "titleCount": true}',
            ));
            
            //Insert profile file widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-files',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 11,
                'params' => '{"title":"Folders", "titleCount": true}',
            ));
            
            //Insert profile groupbuy widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-groupbuys',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 12,
                'params' => '{"title":"Deals", "titleCount": true}',
            ));
            
            //Insert profile job widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-jobs',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 13,
                'params' => '{"title":"Jobs", "titleCount": true}',
            ));
            
            //Insert profile listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 14,
                'params' => '{"title":"Listings", "titleCount": true}',
            ));
            
            //Insert profile mp3music widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-mp3musics',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 15,
                'params' => '{"title":"Mp3 Musics", "titleCount": true}',
            ));
            
            //Insert profile music widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-musics',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 16,
                'params' => '{"title":"Musics", "titleCount": true}',
            ));
            
            //Insert profile photo widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-photos',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 17,
                'params' => '{"title":"Photos", "titleCount": true}',
            ));
            
            //Insert profile video widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-videos',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 18,
                'params' => '{"title":"Videos", "titleCount": true}',
            ));
            
            //Insert profile wiki widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-wikis',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 19,
                'params' => '{"title":"Wikis", "titleCount": true}',
            ));
            
            //Insert profile poll widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-polls',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 20,
                'params' => '{"title":"Polls", "titleCount": true}',
            ));
            
            //Insert profile followers widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-followers',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 21,
                'params' => '{"title":"Followers", "titleCount": true}',
            ));
            
			//Insert profile quick create link
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-create-link',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
			
            //Insert profile announcement widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-announcements',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
            ));
            
			//Insert profile operating hours widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-operating-hours',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Operating Hours"}',
            ));
			
			//Insert profile operating hours widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-contact-info',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Contact Information"}',
            ));
			
            //Insert profile check-in widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-checkins',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
            ));
            
            //Insert newest blogs widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-blogs',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
            	'params' => '{"title":"Newest Blogs"}',
            ));
            
            //Insert newest classifieds widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-classifieds',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
            	'params' => '{"title":"Newest Classifieds"}',
            ));
            
            //Insert newest contests widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-contests',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 6,
            	'params' => '{"title":"Newest Contest"}',
            ));
            
            //Insert newest discussions widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-discussions',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 7,
            	'params' => '{"title":"Newest Discussions"}',
            ));
            
            //Insert newest events widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-events',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 8,
            	'params' => '{"title":"Newest Events"}',
            ));
            
            //Insert newest files widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-files',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 9,
            	'params' => '{"title":"Newest Files"}',
            ));
            
            //Insert newest groupbuys widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-groupbuys',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 10,
            	'params' => '{"title":"Newest Groupbuys"}',
            ));
            
            //Insert newest jobs widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-jobs',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 11,
				'params' => '{"title":"Newest Jobs"}',            
            ));
            
            //Insert newest listings widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-listings',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 12,
            	'params' => '{"title":"Newest Listings"}',
            ));
            
            //Insert newest mp3musics widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-mp3musics',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 13,
            	'params' => '{"title":"Newest Musics"}',
            ));
            
            //Insert newest musics widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-musics',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 14,
            	'params' => '{"title":"Newest Musics"}',
            ));
            
            //Insert newest photos widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-photos',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 15,
            	'params' => '{"title":"Newest Photos"}',
            ));
            
            //Insert newest polls widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-polls',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 16,
            	'params' => '{"title":"Newest Polls"}',
            ));
            
            //Insert newest videos widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-videos',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 17,
            	'params' => '{"title":"Newest Videos"}',
            ));
            
            //Insert newest wikis widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-wikis',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 18,
            	'params' => '{"title":"Newest Wikis"}',
            ));

            //Insert newest ultimate video
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-newest-ynultimatevideo-video',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 19,
            	'params' => '{"title":"Newest Videos"}',
            ));

            //Insert profile Ultimate video widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.business-profile-ynultimatevideo-videos',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => 20,
                'params' => '{"title":"Ultimate Video", "titleCount": true}',
            ));
        }
    }

    protected function _addComparePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_compare_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_compare_index',
                'displayname' => 'YN - Business Compare Page',
                'title' => 'Business Pages Compare Page',
                'description' => 'This page show the businesses for comparation',
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
                'name' => 'ynbusinesspages.main-menu',
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

    protected function _addDashboardModulePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_module')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_module',
                'displayname' => 'YN - Business Dashboard Modules',
                'title' => 'Business Pages Dashboard Modules',
                'description' => 'Business Pages Dashboard Modules',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }

	protected function _addDashboardManageLayoutPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_layout_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_layout_index',
                'displayname' => 'YN - Business Dashboard Manage Page',
                'title' => 'Business Pages Dashboard Manage Page',
                'description' => 'Business Pages Dashboard Manage Page',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }
    
	protected function _addDashboardCoverPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_cover')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_cover',
                'displayname' => 'YN - Business Dashboard Cover Photos',
                'title' => 'Business Pages Dashboard Cover Photos',
                'description' => 'Business Pages Dashboard Cover Photos',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }
    
	protected function _addDashboardManageRolePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_manage-role')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_manage-role',
                'displayname' => 'YN - Business Dashboard Manage Roles',
                'title' => 'Business Pages Dashboard Manage Roles',
                'description' => 'Business Pages Dashboard Manage Roles',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }
    
	protected function _addDashboardRoleSettingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_role-setting')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_role-setting',
                'displayname' => 'YN - Business Dashboard Role Setting',
                'title' => 'Business Pages Dashboard Role Setting',
                'description' => 'Business Pages Dashboard Role Setting',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }
    
	protected function _addDashboardStatisticsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynbusinesspages_dashboard_statistics')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynbusinesspages_dashboard_statistics',
                'displayname' => 'YN - Business Dashboard Statistics',
                'title' => 'Business Pages Dashboard Statistics',
                'description' => 'Business Pages Dashboard Statistics',
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
                'name' => 'ynbusinesspages.main-menu',
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

            //Insert dashboard menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynbusinesspages.dashboard-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
        }
    }
}