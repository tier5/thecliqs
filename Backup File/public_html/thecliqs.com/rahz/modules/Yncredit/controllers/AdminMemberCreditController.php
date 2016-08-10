<?php
class Yncredit_AdminMemberCreditController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('yncredit_admin_main', array(), 'yncredit_admin_main_browse_member_credits');
	
	$this -> view -> form = $form = new Yncredit_Form_Admin_SearchMembers();
	$form->isValid($this->_getAllParams());
    $params = $form->getValues();
    $this->view->formValues = $params;
    $balanceTable = Engine_Api::_()->getDbTable('balances', 'yncredit');
    $this->view->members = $members = $balanceTable -> getMembersPaginator($params);
	$members -> setCurrentPageNumber($this -> _getParam('page', 1));
  }
  public function transactionsAction()
  {
  	// In smoothbox
	$this -> _helper -> layout -> setLayout('admin-simple');
	$user_id = $this->_getParam('id', 0);
	if(!$user_id)
	{
		return;
	}
	$user = Engine_Api::_() -> getItem('user', $user_id);
	$this -> view -> user_name = $user -> getTitle();
	$params['user_id'] = $user_id;
	$this -> view -> transactions = $transactions = Engine_Api::_() -> getDbTable('logs', 'yncredit') -> getTranactionsPaginator($params);
	$transactions -> setCurrentPageNumber($this -> _getParam('page'), 1);
	$transactions -> setItemCountPerPage(10);
  }
  public function sendMassCreditsAction()
  {
  	// In smoothbox
	$this -> _helper -> layout -> setLayout('admin-simple');
	$user_ids = $this->_getParam('ids', '');
	$users = Engine_Api::_() -> user() -> getUserMulti(explode(',', $user_ids));
	$this -> view -> users = $users;
	$this -> view -> levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
	if (!$this->getRequest()->isPost())
	{
      return ;
    }
	
	$sender = Engine_Api::_() -> user() -> getViewer();
    $values = $this->getRequest()->getPost();
	if(empty($values['credit']) || $values['credit'] == 0)
	{
		if(!empty($values['levels']))
		{
			$this -> view -> selected_levels = $values['levels'];
		}
		return;
	}
	$credits = $values['credit'];
	$members = array();
	if(!empty($values['members']) && $values['members'])
	{
		$receivers = Engine_Api::_() -> user() -> getUserMulti($values['members']);
		foreach($receivers as $receiver)
		{
			$members[] = $receiver -> getIdentity();
			if($values['credit_type'] == 1)
			{
				Engine_Api::_() -> yncredit() -> sendCredits($sender, $receiver, $credits, 'admin');
			}
			else 
			{
				Engine_Api::_() -> yncredit() -> debitCredits($sender, $receiver, $credits);
			}
		}
	}
	if(!empty($values['levels']))
	{
		$receivers = Engine_Api::_() -> yncredit() -> getUsersByLevels($values['levels']);
		foreach($receivers as $receiver)
		{
			if(!in_array($receiver -> getIdentity(), $members))
			{
				if($values['credit_type'] == 1)
				{
					Engine_Api::_() -> yncredit() -> sendCredits($sender, $receiver, $credits, 'admin');
				}
				else 
				{
					Engine_Api::_() -> yncredit() -> debitCredits($sender, $receiver, $credits);
				}
			}
		}
	}
	return $this -> _forward('success', 'utility', 'core', 
		  array('smoothboxClose' => true, 
		  'parentRefresh' => true, 
		  'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Send mass credits/debits successfully.'))));
  }
}