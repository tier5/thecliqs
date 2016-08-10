<?php
class Ynlistings_AdminImportsController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynlistings_admin_main', array(), 'ynlistings_admin_main_imports');
    }    
    public function indexAction() {

        //get max import listings settings
        $this->view->max_import = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynlistings_max_listings', 100);
		$this -> view -> form = $form = new Ynlistings_Form_Admin_Import();
		if(!Engine_Api::_()->hasItemType('video'))
		{
			$form -> removeElement('upload_videos');
		}
        $this->view->has_video = Engine_Api::_()->hasItemType('video');
    }
	
    public function importOneByOneAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $can_import = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'import') -> checkRequire();
        if (!$viewer->getIdentity() || !$can_import) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'You don\'t have permission to do this.'));  
            return;
        }
        
        // If not post or form not valid, return
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'The request is invalid.'));  
            return;
        }

        $data = json_decode($this->_getParam('listing'));
        $auth_listing = json_decode($this->_getParam('auth'));
        $auto_improve = $this->_getParam('approved');
        if(isset($data[0]))
            $title = strip_tags($data[0]);
        if(isset($data[1]))
             $tag = strip_tags($data[1]);
        if(isset($data[2]))
            $short_description = $data[2];
        if(isset($data[3]))
            $description = $data[3];
        if(isset($data[4]))
            $about_us = $data[4];
        if(isset($data[5]))
            $price = strip_tags($data[5]);
        if(isset($data[6]))
            $location = strip_tags($data[6]);
        if(isset($data[7]))
            $category_id = strip_tags($data[7]);
        if(isset($data[8]))
            $end_date = strip_tags($data[8]);
        if(isset($data[9]))
            $email = strip_tags($data[9]);
        if(empty($title) || empty($short_description)){
            echo true;
            return;
        }
        //$cancelSetting = 'ynlistings_cancel_import_user'.$viewer->getIdentity();
        // Check max of listings can be add.
        $table = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
        $select = $table->select()->where('user_id = ?', $viewer->getIdentity());
        
        $db = Engine_Api::_()->getDbtable('listings', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        try {
            $listing = $table->createRow();
            $listing -> title = $title;
            $listing -> short_description = $short_description;
            if(!empty($description))
                $listing -> description = $description;
            if(!empty($about_us))
                $listing -> about_us = $about_us;
            if(!empty($price))
                $listing -> price = $price;
            if(!empty($location))
                $listing -> location = $location;
            
            $categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
            unset($categories[0]);
            if(!empty($category_id)) {
                $category = Engine_Api::_()->getItem('ynlistings_category', $category_id);
                if ($category) {
                    $listing->category_id = $category_id;
                    if ($category->level > 1)
                        $listing->theme = $category->getParentCategoryLevel1()->themes[0];
                    else
                        $listing->theme = $category->themes[0]; 
                }
                else {
                    $listing->category_id = $categories[1]->getIdentity();
                    $listing->theme = $categories[1]->themes[0];
                }
            }
            else {
                $listing->category_id = $categories[1]->getIdentity();
                $listing->theme = $categories[1]->themes[0];
            }
            
            if(!empty($end_date)) {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $end = strtotime($end_date);
                date_default_timezone_set($oldTz);
                $end_date = date('Y-m-d H:i:s', $end);
                $now = date('Y-m-d H:i:s');
                if (strtotime($now) > strtotime($end_date))
                    $listing -> end_date = NULL;
                else 
                    $listing -> end_date = $end_date;
            }
            
            $listing -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
            
            if(!empty($email)) {
                $tableUser = Engine_Api::_() -> getDbTable('users', 'user');
                $select = $tableUser -> select() -> where('email = ?', $email) -> limit(1);
                $user = $tableUser -> fetchRow($select);
                if($user){
                    $listing -> user_id = $user -> getIdentity();
                }
                else {
                    $listing -> user_id = $viewer -> getIdentity();
                }
            }
            
            if ($auto_improve == '1') {
                $listing -> approved_status = 'approved';
                $listing -> approved_date = date("Y-m-d H:i:s");
            }
            else {
                $listing -> approved_status = 'pending';
            }
            $listing -> status = 'open';
                 
            $listing -> save();
            
            if ($listing->isOverMax()) {
                $listing->delete();
                echo Zend_Json::encode(array('status' => true, 'message' => ''));  
                return;
            }
            
            if(!empty($tag)) {
                $tags = preg_split('/[,]+/', $tag);
                $listing -> tags() -> addTagMaps($viewer, $tags);
            }
            
            //set authorization
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'network', 'registered', 'everyone');
            $auth_arr = array('view', 'comment', 'share', 'upload_photos', 'discussion', 'print');
            if(Engine_Api::_()->hasItemType('video')) {
                array_push($auth_arr, 'upload_videos'); 
            }
            foreach ($auth_arr as $elem) {
                $auth_role = $auth_listing->$elem;
                if ($auth_role) {
                    $roleMax = array_search($auth_role, $roles);
                    foreach ($roles as $i=>$role) {
                       $auth->setAllowed($listing, $role, $elem, ($i <= $roleMax));
                    }
                }    
            }
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        echo Zend_Json::encode(array('status' => true, 'message' => '', 'id' => $listing->getIdentity()));  
        return;
    }
    
    public function rollbackImportAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $can_import = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'import') -> checkRequire();
        if (!$viewer->getIdentity() || !$can_import) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'You don\'t have permission to do this.'));  
            return;
        }
        
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'The request is invalid.'));  
            return;
        }

        $listings = json_decode($this->_getParam('listings'));
        foreach($listings as $listing_id) {
            $listing = Engine_Api::_()->getItem('ynlistings_listing', $listing_id);
            if (!$listing || !$listing->isDeletable()) {
                continue;
            }
            else {
                $listing->delete();
            }
        }
        echo Zend_Json::encode(array('status' => true, 'message' => ''));  
            return;
    }

    public function historyImportAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $can_import = $this->_helper->requireAuth()->setAuthParams('ynlistings_listing', null, 'import') -> checkRequire();
        if (!$viewer->getIdentity() || !$can_import) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'You don\'t have permission to do this.'));  
            return;
        }
        
        if (! $this->getRequest ()->isPost ()) {
            echo Zend_Json::encode(array('status' => false, 'message' => 'The request is invalid.'));  
            return;
        }

        $listings = json_decode($this->_getParam('listings'));
        $db = Engine_Api::_()->getDbtable('imports', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('imports', 'ynlistings');
            $history = $table->createRow();
            $history -> file_name = $this->_getParam('filename');
            $history -> number_listings = count($listings);
            $history -> list_listings = $listings;
            $history -> save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
        
        $db->commit();
        
        $auto_improve = $this->_getParam('approved');
        if ($auto_improve == '1') {
            foreach ($listings as $listing_id) {
                $listing = Engine_Api::_()->getItem('ynlistings_listing', $listing_id);
                //send notification to follower
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $owner = $listing -> getOwner();
                // get follower
                $tableFollow = Engine_Api::_() -> getItemTable('ynlistings_follow');
                $select = $tableFollow -> select() -> where('owner_id = ?', $owner -> getIdentity()) -> where('status = 1');
                $follower = $tableFollow -> fetchAll($select);
                foreach($follower as $row)
                {
                    $person = Engine_Api::_()->getItem('user', $row -> user_id);
                    $notifyApi -> addNotification($person, $owner, $listing, 'ynlistings_listing_follow');
                }
                
                //send notifications end add activity on feed
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $notifyApi -> addNotification($owner, $viewer, $listing, 'ynlistings_listing_approve');
                
                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                $action = $activityApi->addActivity($owner, $listing, 'ynlistings_listing_create');
                if($action) {
                    $activityApi->attachActivity($action, $listing);
                }
            }
        }
    }

	public function viewHistoryAction()
	{
		$tableImport = Engine_Api::_()->getItemTable('ynlistings_import');
		$page = $this -> _getParam('page', 1);
		$this->view->paginator = Zend_Paginator::factory($tableImport->select());
        $this -> view -> paginator -> setItemCountPerPage(10);
        $this -> view -> paginator -> setCurrentPageNumber($page);
	}
	
	public function viewListingAction()
	{
		$import = Engine_Api::_()->getItem('ynlistings_import', $this->_getParam('id'));
		$this-> view -> list_listings = $import -> list_listings;
	}
}