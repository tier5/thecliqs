<?php
class Ynresume_Widget_RecommendationGiveController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            $this->setNoRender();
            return;
        }
        
        $can_recommend = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'recommend')->checkRequire();
        if (!$can_recommend) {
            $this->setNoRender();
        }
        
        $params = $this->_getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        $recommendation = (isset($params['recommendation_id'])) ? Engine_Api::_()->getItem('ynresume_recommendation', $params['recommendation_id']) : null;
        if (isset($params['send']) && $params['send']) {
            $table = Engine_Api::_()->getDbTable('recommendations', 'ynresume');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                $giver_occupation = (isset($params['giver-occupation']) && $params['giver-occupation']) ? explode('-',$params['giver-occupation']) : null;
                $values = array(
                    'status' => 'given',
                    'given_date' => date('Y-m-d H:i:s'),
                    'giver_position_type' => ($giver_occupation) ? $giver_occupation[0] : null,
                    'giver_position_id' => ($giver_occupation) ? $giver_occupation[1] : null,
                    'content' => $params['content'],
                    'relationship' => $params['relationship'],
                    'given_message' => (isset($params['given_message'])) ? $params['given_message'] : null,
                );
                if (!$recommendation) {
                    $receiver_occupation = explode('-', $params['receiver-occupation']);
                    $values['receiver_id'] = $params['receiver_id'];
                    $values['receiver_position_type'] = $receiver_occupation[0];
                    $values['receiver_position_id'] = $receiver_occupation[1];
                    $values['giver_id'] = $viewer->getIdentity();
                    $recommendation = Engine_Api::_()->getDbTable('recommendations', 'ynresume')->getAskedRecommendation($viewer->getIdentity(), $receiver_occupation[0], $receiver_occupation[1]);
                    if (!$recommendation) {
                        $recommendation = $table->createRow();
                    }
                }
                $recommendation->setFromArray($values);
                $recommendation->save();
                // Set auth
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                $auth_arr[] = 'view';
                foreach ($auth_arr as $elem) {
                    $auth_role = 'everyone';
                    if ($auth_role) {
                        $roleMax = array_search($auth_role, $roles);
                        foreach ($roles as $i=>$role) {
                           $auth->setAllowed($recommendation, $role, $elem, ($i <= $roleMax));
                        }
                    }    
                }
                
                //add credits
                if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
                    $receiver = $recommendation->getReceiver();
                    if($viewer -> getIdentity())
                        Engine_Api::_()->yncredit()-> hookCustomEarnCredits($viewer, $viewer -> getTitle(), 'ynresume_recommend', $receiver);
                }
                
                //send notification
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $email_p = array(
                    'given_message' => $recommendation->given_message,
                    'place' => Engine_Api::_()->ynresume()->getPlace($recommendation->receiver_position_type, $recommendation->receiver_position_id)
                );
                $user = Engine_Api::_()->user()->getUser($recommendation->receiver_id);
                $resume = Engine_Api::_()->ynresume()->getResumeByUserId($recommendation->receiver_id);
                if ($user && $resume)
                    $notifyApi -> addNotification($user, $viewer, $resume, 'ynresume_given_recommendation', $email_p);
                
                $db->commit();
                $params = array();
                $recommendation = null;
                $this->view->success = true;
            }
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        $this->view->recommendation = $recommendation;
        $this->view->params = $params;
    }
}