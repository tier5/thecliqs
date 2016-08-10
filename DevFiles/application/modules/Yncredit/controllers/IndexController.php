<?php

class Yncredit_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
  	if( !$this->_helper->requireUser->isValid() ) return;
	if( !$this->_helper->requireAuth()->setAuthParams('yncredit', null, 'use_credit')->isValid() ) return;
  	if( !$this->_helper->requireAuth()->setAuthParams('yncredit', null, 'general_info')->isValid() ) return;
     // Landing page mode
	$this->_helper->content->setEnabled ();
	$levelId = Engine_Api::_()->user()->getViewer() -> level_id;
	$this->view->credits = $credits = Engine_Api::_() -> getDbTable('credits', 'yncredit') -> getAllActionEnableByLevel($levelId, 'user');
  }
  public function checkSendCreditAction()
  {
  	$this -> _helper -> layout -> disableLayout();
	$this -> _helper -> viewRenderer -> setNoRender(TRUE);
  	$user_id = $this -> _getParam('user_id');
	$user = Engine_Api::_() -> getItem('user', $user_id);
	$viewer = Engine_Api::_() -> user() -> getViewer();
	echo Engine_Api::_() -> yncredit() -> checkSendCredit($viewer, $user);
	return;
  }
  
  public function sendCreditAction()
  {
  	$user_id = $this -> _getParam('user_id');
  	$credits = $this -> _getParam('credits',0);
	$user = Engine_Api::_() -> getItem('user', $user_id);
	$viewer = Engine_Api::_() -> user() -> getViewer();
	if(!$user -> getIdentity() || !$viewer -> getIdentity())
	{
		$this -> view -> error_msg = $this -> view -> translate("Please select one friend to send credits.");
		return;
	}
	if(!$credits)
	{
		$this -> view -> error_msg = $this -> view -> translate("Please enter any credit to send to your friend.");
		return;
	}
	$params = Zend_Json::decode(Engine_Api::_() -> yncredit() -> checkSendCredit($viewer, $user));
	if($params['max'] < $credits)
	{
		$this -> view -> error_msg = $this -> view -> translate("You cannot send over %s credits to this friend.", $credits);
	}
	if($params['fail'])
	{
		$this -> view -> error_msg = $params['message'];
		return;
	}
	else 
	{
		$this -> view -> confirm = $this -> view -> translate(array("Are you sure you want to send %1s credit to %2s.","Are you sure you want to send %1s credits to %2s.", $credits), $credits, $user);
	}
	if($this -> getRequest() -> isPost())
	{
		Engine_Api::_() -> yncredit() -> sendCredits($viewer, $user, $credits);
		return $this->_forward('success', 'utility', 'core', array(
	          'smoothboxClose' => true,
	          'parentRefresh'=> true,
	          'messages' => array($this -> view -> translate("Send credits successfully!"))
	      ));
	}
  }
  public function profileSendCreditAction()
  {
	$viewer = Engine_Api::_() -> user() -> getViewer();
	$user_id = $this -> _getParam('user_id');
	$user = Engine_Api::_() -> getItem('user', $user_id);
	$json = Engine_Api::_() -> yncredit() -> checkSendCredit($viewer, $user);
	$respone = Zend_Json::decode($json);
	$this -> view -> message = $respone['message'];
	if(isset($respone['fail']))
		$this -> view -> fail = $respone['fail'];
	else
		$this -> view -> fail = 1;
	if(isset($respone['fail']))
		$this -> view -> max = $respone['max'];
	else 
	{
		$this -> view -> max = 0;
	}
	$this -> view -> user = $user -> getTitle();
	$this -> view -> user_id = $user -> getIdentity();
	
	if (!$this->getRequest()->isPost()) {
      return ;
    }
	$this -> view -> credit = $credits = $this -> _getParam('credit');
	if(!$user -> getIdentity() || !$viewer -> getIdentity())
	{
		$this -> view -> error_msg = $this -> view -> translate("Please select one friend to send credits.");
		return;
	}
	if(!$credits)
	{
		$this -> view -> error_msg = $this -> view -> translate("Please enter any credit to send to your friend.");
		return;
	}
	if($respone['max'] < $credits)
	{
		$this -> view -> error_msg = $this -> view -> translate("You cannot send over %s credits to this friend.", $credits);
	}
	if($respone['fail'])
	{
		$this -> view -> error_msg = $params['message'];
		return;
	}
	else 
	{
		$this -> view -> confirm = $this -> view -> translate(array("Are you sure you want to send %1s credit to %2s.","Are you sure you want to send %1s credits to %2s.", $credits), $credits, $user);
	}
	$post = $this -> getRequest() -> getPost();
	if(isset($post['confirm']))
	{
		Engine_Api::_() -> yncredit() -> sendCredits($viewer, $user, $credits);
		return $this->_forward('success', 'utility', 'core', array(
		          'smoothboxClose' => true,
		          'parentRefresh'=> false,
		          'messages' => array($this -> view -> translate("Send credits successfully!"))
		      ));
	}
  }
}
