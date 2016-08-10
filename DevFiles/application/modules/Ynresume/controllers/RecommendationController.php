<?php
class Ynresume_RecommendationController extends Core_Controller_Action_Standard {
    public function receivedAction() {
        $this->_helper->content->setEnabled()->setNoRender();
    }
    
    public function givenAction() {
        $this->_helper->content->setEnabled()->setNoRender();
    }
    
    public function askAction() {
        $this->_helper->content->setEnabled()->setNoRender();
    }
    
    public function giveAction() {
        $this->_helper->content->setEnabled()->setNoRender();
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynresume_resume', null, 'recommend') -> isValid())
            return;
    }
    
    public function showAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($recommendation && $recommendation->receiver_id == $viewer->getIdentity()) {
            $recommendation->show = $value;
            $recommendation->save();
        }
    }
    
    public function viewRequestAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id', 0);
        $this->view->recommendation = $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
        if (!$id || !$recommendation) {
            return $this->_helper->requireSubject()->forward();
        }
    }

    public function ignoreRequestAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id', 0);
        $this->view->recommendation = $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
        if (!$id || !$recommendation) {
            return $this->_helper->requireSubject()->forward();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $recommendation->giver_id) {
            return $this->_helper->requireAuth()->forward();
        }
        $this->view->id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
                $recommendation->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This request has been ignored.')
            ));
        }
    }
    
    public function removeAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id', 0);
        $this->view->recommendation = $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
        if (!$id || !$recommendation) {
            return $this->_helper->requireSubject()->forward();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $recommendation->giver_id) {
            return $this->_helper->requireAuth()->forward();
        }
        $this->view->id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
                $recommendation->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This recommendation has been removed.')
            ));
        }
    }

    public function editContentAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $content = $this->_getParam('content');
        $message = $this->_getParam('message');
        $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($recommendation && $recommendation->giver_id == $viewer->getIdentity()) {
            $recommendation->content = $content;
            $recommendation->given_date = date('Y-m-d H:i:s');
            $recommendation->save();
            $date = $recommendation->getGivenDate();
            //send noti + email
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $email_p = array(
                'message' => $message,
                'place' => Engine_Api::_()->ynresume()->getPlace($recommendation->receiver_position_type, $recommendation->receiver_position_id)
            );
            $user = Engine_Api::_()->user()->getUser($recommendation->receiver_id);
            $resume = Engine_Api::_()->ynresume()->getResumeByUserId($recommendation->receiver_id);
            if ($user && $resume)
                $notifyApi -> addNotification($user, $viewer, $resume, 'ynresume_edited_recommendation', $email_p);
            
            echo json_encode(array('time' => date('M, d, Y,', $date->getTimestamp())));
        }
    }
    
    public function editPrivacyAction() {
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id', 0);
        $this->view->recommendation = $recommendation = Engine_Api::_()->getItem('ynresume_recommendation', $id);
        if (!$id || !$recommendation) {
            return $this->_helper->requireSubject()->forward();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $recommendation->giver_id) {
            return $this->_helper->requireAuth()->forward();
        }
        $this->view->form = $form = new Ynresume_Form_Recommendation_EditPrivacy();
        
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
        foreach ($roles as $role) {
            if(1 === $auth->isAllowed($recommendation, $role, 'view')) {
                if ($form->view)
                    $form->view->setValue($role);
            }
        }  
        // Check post
        if( $this->getRequest()->isPost()) {
            
            $posts = $this -> getRequest() -> getPost();
            if (!$form -> isValid($posts)) {
                return;
            }
            
            $values = $form->getValues();
            
            $auth_role = $values['view'];
            if ($auth_role) {
                $roleMax = array_search($auth_role, $roles);
                foreach ($roles as $i=>$role) {
                   $auth->setAllowed($recommendation, $role, 'view', ($i <= $roleMax));
                }
            } 
            
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array('The privacy has been edited.')
            ));
        }
    }

    public function suggestFriendsAction() {
        if(Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible ) {
            $this->_helper->requireAuth()->forward();
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) {
            $data = null;
        } else {
            $data = array();
            $table = Engine_Api::_()->getItemTable('user');
      
            $usersAllowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $viewer->level_id, 'auth');

            if( (bool)$this->_getParam('message') && $usersAllowed == "everyone" ) {
                $select = Engine_Api::_()->getDbtable('users', 'user')->select();
                $select->where('username <> ?',$viewer->username);
            }
            else {
                $select = Engine_Api::_()->user()->getViewer()->membership()->getMembersObjectSelect();          
            }
         
            if( $this->_getParam('includeSelf', false) ) {
                $data[] = array(
                    'type' => 'user',
                    'id' => $viewer->getIdentity(),
                    'guid' => $viewer->getGuid(),
                    'label' => $viewer->getTitle() . ' (you)',
                    'photo' => $this->view->itemPhoto($viewer, 'thumb.profile'),
                    'url' => $viewer->getHref(),
                );
            }

            if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
                $select->limit($limit);
            }

            if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
                $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
            }
      
            $ids = array();
            foreach( $select->getTable()->fetchAll($select) as $friend ) {
                $data[] = array(
                    'type'  => 'user',
                    'id'    => $friend->getIdentity(),
                    'guid'  => $friend->getGuid(),
                    'label' => $friend->getTitle(),
                    'photo' => $this->view->itemPhoto($friend, 'thumb.profile'),
                    'url'   => $friend->getHref(),
                );
                $ids[] = $friend->getIdentity();
                $friend_data[$friend->getIdentity()] = $friend->getTitle();
            }
        }

        if( $this->_getParam('sendNow', true) ) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }
}
