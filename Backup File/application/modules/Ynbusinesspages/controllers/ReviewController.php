<?php
class Ynbusinesspages_ReviewController extends Core_Controller_Action_Standard {
	
    public function indexAction() {
        
    }
    
    public function createAction() {
        $this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> getRequest() -> getParam('business_id'));
        $tab = $this -> getRequest() -> getParam('tab');
        $page = $this -> getRequest() -> getParam('page');
        $refresh = $this -> getRequest() -> getParam('refresh', false);
        $can_review = $this->_helper->requireAuth()->setAuthParams('ynbusinesspages_business', null, 'rate') -> checkRequire();
        if (!$can_review) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to add review to this business.");
            return;
        }
        
        if (!$business) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists.");
            return;
        }
        if (!$viewer->getIdentity() || $business -> is_claimed) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to add review to this business.");
            return;
        }
        $rated = $business->checkRated();
        if ($rated) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You have added a review already.");
            return;
        }
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $values = $this->getRequest()->getPost();
        $db = Engine_Api::_()->getDbtable('reviews', 'ynbusinesspages')->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('reviews', 'ynbusinesspages');
            $review = $table->createRow();
            $review->business_id = $business->getIdentity();
            $review->user_id = $viewer->getIdentity();
			$review->title = strip_tags($values['review_title']);
            $review->body = strip_tags($values['review_body']);
            $review->rate_number = $values['review_rating'];
            $review->save();
            
			$business -> review_count += 1;
			$business -> rating  = $business -> getRating();
			$business -> save();
			
            // Add activity and notification
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $action = $activityApi -> addActivity($viewer, $business, 'ynbusinesspages_review_create');
            if ($action) {
                $action -> attach($review);
            }
            
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($business->getOwner(), $viewer, $business, 'ynbusinesspages_business_add_review');
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        if ($refresh) {
            return $this -> _forward('success', 'utility', 'core', array(
            'parentRefresh' => true,
            'messages' => Zend_Registry::get('Zend_Translate') -> _('Your review has been created.')
        ));
        }
        
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id' => $business->getIdentity(), 'slug' => $business -> getSlug(), 'tab' => $tab, 'page' => $page), 'ynbusinesspages_profile', true),
            'messages' => Zend_Registry::get('Zend_Translate') -> _('Your review has been created.')
        ));
    }
    
    public function editAction() {
        $this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->review = $review = Engine_Api::_() -> getItem('ynbusinesspages_review', $this -> getRequest() -> getParam('id'));
        
        $tab = $this -> getRequest() -> getParam('tab');
        $page = $this -> getRequest() -> getParam('page');
        if (!$review) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists.");
            return;
        }
        
        if (!$viewer->getIdentity()) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to edit this review.");
            return;
        }
        
		if(!$viewer -> isSelf($review -> getOwner()))
		{
			if(!$review->isEditable())
			{
				$this -> view -> error = true;
	            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to edit this review.");
	            return;
			}
		}
		
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $review->business_id);
        
        if (!$business) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists.");
            return;
        }
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $values = $this->getRequest()->getPost();
        $db = Engine_Api::_()->getDbtable('reviews', 'ynbusinesspages')->getAdapter();
        $db->beginTransaction();
        try {
        	$review->title = strip_tags($values['review_title']);
            $review->body = strip_tags($values['review_body']);
            $review->rate_number = $values['review_rating'];
            $review->save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id' => $business->getIdentity(), 'slug' => $business -> getSlug(), 'tab' => $tab, 'page' => $page), 'ynbusinesspages_profile', true),
            'messages' => Zend_Registry::get('Zend_Translate') -> _('This review has been edited.')
        ));
    }

    public function deleteAction() {
        $tab = $this -> getRequest() -> getParam('tab');
        $page = $this -> getRequest() -> getParam('page');
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $review = Engine_Api::_() -> getItem('ynbusinesspages_review', $this -> getRequest() -> getParam('id'));
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $review->business_id);
        if (!$review) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists.");
            return;
        }
        
        if (!$viewer->getIdentity()) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to delete this review.");
            return;
        }
        
        if(!$viewer -> isSelf($review -> getOwner()))
		{
			if(!$review->isDeletable())
			{
				$this -> view -> error = true;
	            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to delete this review.");
	            return;
			}
		}
        
        if (!$business) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Business doesn't exists.");
            return;
        }
        
        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> view -> form = $form = new Ynbusinesspages_Form_Review_Delete();
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }

        $db = $review -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            $review -> delete();
            $business -> review_count -= 1;
            $business -> rating  = $business -> getRating();
			$business -> save();
            $db -> commit();
        }
        catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This review has been deleted.');
        
         return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id' => $business->getIdentity(), 'slug' => $business -> getSlug(), 'tab' => $tab, 'page' => $page), 'ynbusinesspages_profile', true),
            'messages' => Array($this -> view -> message)
        ));
    }

    public function listUserAction()
    {
    	$viewer = Engine_Api::_() -> user() -> getViewer();
    	$businessId = $this -> getRequest() -> getParam('business_id');
        $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $businessId);
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_review');
		$select = $table->select();
		$select->where('business_id = ?', $business->getIdentity());
		$reviews = $table -> fetchAll($select);
		$userIds = array();
		foreach ($reviews as $review){
			$userIds[] = $review -> user_id;
		}
		$this -> view -> users = Engine_Api::_()->getItemMulti('user', $userIds);
    }
    
    
}
