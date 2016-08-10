<?php
class Ynbusinesspages_AdminClaimsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_main_claims');
        
        $table = Engine_Api::_()->getDbTable('claimrequests', 'ynbusinesspages');
		$select = $table -> select();
        $tableName = $table -> info('name');
        
		$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
		$userTblName = $userTbl -> info('name');
		
        $businessTbl = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $businessTblName = $businessTbl -> info('name');
        
        $select = $table -> select() -> from(array('claimrequest' => $tableName));
        $select -> setIntegrityCheck(false) 
		        -> joinLeft("$userTblName as user", "user.user_id = claimrequest.user_id", "")
       			-> joinLeft("$businessTblName as business", "business.business_id = claimrequest.business_id", "");
				
        $methods = array();
        
        $this->view->form = $form = new Ynbusinesspages_Form_Admin_Claims_Search();
        
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $this->view->formValues = $values;
        
		if ($values['name'] != '') {
            $select->where('business.name LIKE ?', '%'.$values['name'].'%');
        }
		
		if ($values['claimant'] != '') {
            $select->where('user.displayname LIKE ?', '%'.$values['claimant'].'%');
        }
		
		$sysTimezone = date_default_timezone_get();
        if ($values['from_date']) {
            $from_date = new Zend_Date(strtotime($values['from_date']));
			$from_date->setTimezone($sysTimezone);
			$select->where('claimrequest.creation_date >= ?', $from_date->get('yyyy-MM-dd'));
        }
	    if ($values['to_date']) {
	    	$to_date = new Zend_Date(strtotime($values['to_date']));
			$to_date->setTimezone($sysTimezone);
			$select->where('claimrequest.creation_date <= ?', $to_date->get('yyyy-MM-dd'));
	    }
       
	    if (isset($values['order'])) {
	        if (empty($values['direction'])) {
	            $values['direction'] = ($values['order'] == 'business.name') ? 'ASC' : 'DESC';
	        }
            $select->order($values['order'].' '.$values['direction']);
		}
		else {
	        if (!empty($values['direction'])) {
	            $select->order('claimrequest.transaction_id'.' '.$values['direction']);
	        }
	    }
		$select -> where('claimrequest.status = ?', 'pending');
        $claimrequests = $table->fetchAll($select);
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($claimrequests);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

	public function updateAction()
	{
		$type = $this ->_getParam('type');
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		// Make form
		$this -> view -> form = $form = new Ynbusinesspages_Form_Admin_Claims_Update(array('action' => $type));
		
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		
		$tableClaimRequest = Engine_Api::_() -> getItemTable('ynbusinesspages_claimrequest');
		$claim = Engine_Api::_() -> getItem('ynbusinesspages_claimrequest', $this ->_getParam('id'));
		
		switch ($type) {
			case 'approve':
					$tableClaimRequest -> approveClaim($claim);
					$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $claim -> business_id);
					$user = Engine_Api::_() -> getItem('user', $claim -> user_id);
					//send email
					$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
					$params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
					$href =  				 
						'http://'. @$_SERVER['HTTP_HOST'].
						Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $business -> getIdentity(), 'slug' => $business -> getSlug()),'ynbusinesspages_profile',true);
					$params['business_link'] = $href;	
					$params['business_name'] = $business -> getTitle();
					if(!empty($user))
					{
						try{
							Engine_Api::_()->getApi('mail','ynbusinesspages')->send($user -> email, 'ynbusinesspages_claim_approved',$params);
						}
						catch(exception $e)
						{
							
						}
					}
				break;
			case 'deny':
					$claim -> status = 'denied';
					$claim -> save();
				break;
			default:
				
				break;
		}
		$this -> view -> message = ucfirst($type). ' sucessfully!';
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array($this -> view -> message),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}
}