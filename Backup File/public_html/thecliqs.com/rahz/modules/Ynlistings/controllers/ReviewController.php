<?php
class Ynlistings_ReviewController extends Core_Controller_Action_Standard {
	
    public function indexAction() {
        
    }
    
    public function createAction() {
        $this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $listing = Engine_Api::_() -> getItem('ynlistings_listing', $this -> getRequest() -> getParam('listing_id'));
        $tab = $this -> getRequest() -> getParam('tab');
        $page = $this -> getRequest() -> getParam('page');
        if (!$listing) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists.");
            return;
        }
        
        $can_rate = $this -> _helper -> requireAuth() -> setAuthParams('ynlistings_listing', null, 'rate')->checkRequire();
        if (!$viewer->getIdentity() || $listing->isOwner($viewer) || !$can_rate) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to add review to this listing.");
            return;
        }
        $rated = $listing->checkRated();
        if ($rated) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You have added a review already.");
            return;
        }
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $values = $this->getRequest()->getPost();
        $db = Engine_Api::_()->getDbtable('reviews', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('reviews', 'ynlistings');
            $review = $table->createRow();
            $review->listing_id = $listing->getIdentity();
            $review->user_id = $viewer->getIdentity();
            $review->body = strip_tags($values['review_body']);
            $review->rate_number = $values['review_rating'];
            $review->save();
            
            // Add activity and notification
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $action = $activityApi -> addActivity($viewer, $listing, 'ynlistings_review_create');
            if ($action) {
                $action -> attach($listing);
            }
            
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($listing->getOwner(), $viewer, $listing, 'ynlistings_listing_add_review');
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'view', 'id' => $listing->getIdentity(), 'tab' => $tab, 'page' => $page), 'ynlistings_general', true),
            'messages' => Zend_Registry::get('Zend_Translate') -> _('Your review has been created.')
        ));
    }
    
    public function editAction() {
        $this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $this->view->review = $review = Engine_Api::_() -> getItem('ynlistings_review', $this -> getRequest() -> getParam('id'));
        
        $tab = $this -> getRequest() -> getParam('tab');
        $page = $this -> getRequest() -> getParam('page');
        if (!$review) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists.");
            return;
        }
        
        if (!$viewer->getIdentity() || !$review->isEditable()) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to edit this listing.");
            return;
        }
        
        $listing = Engine_Api::_() -> getItem('ynlistings_listing', $review->listing_id);
        
        if (!$listing) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists.");
            return;
        }
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        $values = $this->getRequest()->getPost();
        $db = Engine_Api::_()->getDbtable('reviews', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        try {
            $review->body = $values['review_body'];
            $review->rate_number = $values['review_rating'];
            $review->save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'view', 'id' => $listing->getIdentity(), 'tab' => $tab, 'page' => $page), 'ynlistings_general', true),
            'messages' => Zend_Registry::get('Zend_Translate') -> _('This review has been edited.')
        ));
    }

    public function deleteAction() {
        $tab = $this -> getRequest() -> getParam('tab');
        $page = $this -> getRequest() -> getParam('page');
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $review = Engine_Api::_() -> getItem('ynlistings_review', $this -> getRequest() -> getParam('id'));
        $listing = Engine_Api::_() -> getItem('ynlistings_listing', $review->listing_id);
        if (!$review) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Review doesn't exists.");
            return;
        }
        
        if (!$viewer->getIdentity() || !$review->isDeletable()) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("You don\'t have permission to delete this listing.");
            return;
        }
        
        if (!$listing) {
            $this -> view -> error = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Listing doesn't exists.");
            return;
        }
        
        $this -> _helper -> layout -> setLayout('default-simple');
        $this -> view -> form = $form = new Ynlistings_Form_Review_Delete();
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }

        $db = $review -> getTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            $review -> delete();
            $db -> commit();
        }
        catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This review has been deleted.');
        
         return $this -> _forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'view', 'id' => $listing->getIdentity(), 'tab' => $tab, 'page' => $page), 'ynlistings_general', true),
            'messages' => Array($this -> view -> message)
        ));
    }

    
}
