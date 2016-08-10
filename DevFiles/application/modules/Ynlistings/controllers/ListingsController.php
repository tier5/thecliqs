<?php
class Ynlistings_ListingsController extends Core_Controller_Action_Standard {
	public function indexAction() {
		$this -> view -> someVar = 'someVal';
	}
    
    public function directionAction() {
        $listingId = $this -> _getParam('id', 0);
        if (!$listingId) {
            return $this->_helper->requireAuth()->forward();
        }
        
        $this->view->listing = $listing = Engine_Api::_()->getItem('ynlistings_listing', $listingId);
        if (is_null($listing)) {
            return $this->_helper->requireAuth()->forward();
        }   
    }
    
    public function emailToFriendsAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;
        $viewer = Engine_Api::_() -> user() -> getViewer();
       
        $this->view->listing = $listing = Engine_Api::_()->getItem('ynlistings_listing', $this -> _getParam('id'));
        if (!$listing) {
            return $this->_helper->requireSubject()->forward();
        }   
        $this->view->form = $form = new Ynlistings_Form_EmailToFriends();
        
        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        
        if (!$form -> isValid($this -> getRequest() -> getPost())) {
            return;
        }
        $values = $form -> getValues();
        $sentEmails = $listing -> sendEmailToFriends($values['recipients'], @$values['message']);
        
        $message = Zend_Registry::get('Zend_Translate') -> _("$sentEmails email(s) have been sent.");
        return $this -> _forward('success', 'utility', 'core', array(
            'parentRefresh' => false,
            'smoothboxClose' => true,
            'messages' => $message
        ));
    }

    public function transferOwnerAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $listing = Engine_Api::_() -> getItem('ynlistings_listing', $this ->_getParam('id'));

        if (!$listing) {
            return $this -> _helper -> requireSubject -> forward();
        }

        if (!$viewer -> isAdmin() && !$listing -> isOwner($viewer)) {
            return $this -> _helper -> requireAuth -> forward();
        }

        $this -> view -> form = $form = new Ynlistings_Form_TransferOwner();

        if (!$this -> getRequest() -> getPost()) {
            return;
        }

        if (!$form -> isValid($this -> getRequest() -> getPost())) {
            return;
        }
        //Process
        $values = $form -> getValues();
        $db = Engine_Api::_() -> getDbtable('listings', 'ynlistings') -> getAdapter();
        $db -> beginTransaction();
        $friend = Engine_Api::_() -> user() -> getUser($values['toValues']);
        $tranfer_self = false;
        try {
            if ($listing -> user_id != $values['toValues']) {
                $listing -> user_id = $values['toValues'];
                $listing -> save();
                $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
                $action = $activityApi -> addActivity(Engine_Api::_()->getItem('user', $values['toValues']), $listing, 'ynlistings_listing_transfer');
                if ($action) {
                    $action -> attach($listing);
                }
            }
            else {
                $tranfer_self = true;
            }

            $db -> commit();
        } catch(Exception $e) {
            $db -> rollback();
            throw $e;
        }
        if ($tranfer_self) {
            return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => false, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('You already is owner of this listing.')), ));
        }
        else return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('This listing is tranferred successfully.')), ));
    }
}
