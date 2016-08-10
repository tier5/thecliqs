<?php
class Yncredit_SpendCreditController extends Core_Controller_Action_Standard
{
  /**
   * @var User_Model_User
   */
  protected $_user;

  public function init()
  {
  	if( !$this->_helper->requireAuth()->setAuthParams('yncredit', null, 'use_credit')->isValid() ) return;
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('yncredit_main');

    // Get user
    $this->_user = Engine_Api::_()->user()->getViewer();

    // Check viewer and user
    if (!$this->_user || !$this->_user->getIdentity()) {
      $this->_helper->redirector->gotoRoute(array(), 'yncredit_general', true);
    }
  }

  public function detailsAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->error = false;
    $package_id = $this->_getParam('package_id', 0);
    // Get package
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->package = $package = $packagesTable->fetchRow(array('enabled = ?' => true, 'package_id = ?' => $package_id));
    if (!$package) {
      $this->view->error = true;
      return;
    }
    $this->view->packageDescription = Engine_Api::_()->credit()->getPackageDescription($package);
  }

  public function confirmAction()
  {
    $this-> view -> item_id = $item_id = $this->_getParam('item_id', null);
	$action_type = $this->_getParam('action_type', null);
	$numbers = $this->_getParam('number_item', 1);
	$security = $this->_getParam('id', null);
    // Process
    $this->view->result = true;
	$options = Engine_Api::_()->authorization()->getPermission($this->_user -> level_id, 'yncredit', 'spend');
	if(!in_array($action_type, Zend_Json::decode($options)))
	{
		return $this->_helper->requireAuth->forward ();
	}
	$settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
	$credits = 0;
	$cancel_url = "";
	$item = array();
    switch ($action_type) 
    {
        case 'publish_deal':
			 $item = Engine_Api::_() -> getItem('deal', $item_id);
			// Check if it exists
		    if (!$item) 
		    {
		      $this-> view -> message = Zend_Registry::get('Zend_View')->translate('Please choose one now below.');
		      return;
		    }
			$cancel_url = Zend_Controller_Front::getInstance()->getRouter()
				        ->assemble(
				          array(
				            'action' => 'publish',
				            'deal' => $item -> getIdentity()
				          ), 'groupbuy_general', true);
			$credits = ceil($item->total_fee * $defaultPrice);
			break;
		
		case 'buy_deal':
            $item = Engine_Api::_() -> getItem('deal', $item_id);
			// Check if it exists
		    if (!$item) 
		    {
		      $this-> view -> message = Zend_Registry::get('Zend_View')->translate('Please choose one now below.');
		      return;
		    }
			$maxbought = $item->getMaxBought($this -> _user);
			if(($maxbought < $numbers && $item -> max_bought > 0) || $numbers <= 0)
			{
				return $this->_helper->redirector->gotoRoute(
				            array(
				            'action' => 'buy-deal',
				            'deal' => $item -> getIdentity()
				          ), 'groupbuy_general', true);
			}
			$cancel_url = Zend_Controller_Front::getInstance()->getRouter()
				        ->assemble(
				          array(
				            'action' => 'buy-deal',
				            'deal' => $item -> getIdentity()
				          ), 'groupbuy_general', true);
			$credits = ceil($item->price * $defaultPrice * $numbers);
            break;
			
		case 'publish_contest':
			$item = Engine_Api::_()->getItem('yncontest_contest', $item_id);
			// Check if it exists
		    if (!$item) 
		    {
		      $this-> view -> message = Zend_Registry::get('Zend_View')->translate('Please choose one now below.');
		      return $this->_helper->requireAuth->forward ();
		    }
			$cancel_url = Zend_Controller_Front::getInstance()->getRouter()
				        ->assemble(
				          array(
				            'action' => 'method',
				            'contestId' => $item -> getIdentity(),
				            'id' => $security
				          ), 'yncontest_payment', true);
			if($security == null)
			{
				return $this->_helper->requireAuth->forward ();
			}
				
			$table = Engine_Api::_() -> getDbTable('transactions', 'yncontest');
			$transactions = $table->getTranBySec($security);
					
			if(count($transactions) == 0)
			{
				return $this->_helper->requireAuth->forward ();
			}
			
			$final_price = 0;
			foreach($transactions as $transaction){
				$final_price +=$transaction->amount;
			}
			$credits = ceil($final_price * $defaultPrice);
			break;
    }
	$this -> view -> item = $item;
	$this -> view -> cancel_url = $cancel_url;
    $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
    if (!$balance) 
    {
      $currentBalance = 0;
    } else 
    {
      $currentBalance = $balance->current_credit;
    }
    $this->view->currentBalance = $currentBalance;
    $this->view->credits = $credits;
    $this->view->enoughCredits = $this->_checkEnoughCredits($credits);

    // Check method
    if (!$this->getRequest()->isPost()) 
    {
      return;
    }

    // Redirect to spend handler
    return $this->_helper->redirector->gotoRoute(array('action' => 'process'));
  }

  public function processAction()
  {
    // Get item
    $item_id = $this->_getParam('item_id', 0);
	$action_type = $this->_getParam('action_type', null);
	$numbers = $this->_getParam('number_item', 1);
	$security = $this->_getParam('id', null);
	$options = Engine_Api::_()->authorization()->getPermission($this->_user -> level_id, 'yncredit', 'spend');
	if(!in_array($action_type, Zend_Json::decode($options)))
	{
		return $this->_helper->requireAuth->forward ();
	}
	$settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
	$module = 'groupbuy';
	$type = 0;
	$item = array();
	$option_service = 0;
	$transactions = array();
    switch ($action_type) 
    {
        case 'publish_deal':
			 $item = Engine_Api::_() -> getItem('deal', $item_id);
			// Check if it exists
		    if (!$item) 
		    {
		      return $this->_helper->redirector->gotoRoute( array(
				            'action' => 'publish',
				            'deal' => $item -> getIdentity()
				          ), 'groupbuy_general', true);
		    }
			$credits = ceil($item->total_fee * $defaultPrice);
			$module = 'groupbuy';
			$type = 0;
			$params = $this -> view -> translate("Pay publishing fee by credits");
			break;
		case 'buy_deal':
            $item = Engine_Api::_() -> getItem('deal', $item_id);
			// Check if it exists
		    if (!$item) 
		    {
		      return $this->_helper->redirector->gotoRoute(
				            array(
				            'action' => 'buy-deal',
				            'deal' => $item -> getIdentity()
				          ), 'groupbuy_general', true);
		    }
			$maxbought = $item->getMaxBought($this -> _user);
			if($maxbought < $numbers && $item -> max_bought > 0)
			{
				return $this->_helper->redirector->gotoRoute(
				            array(), 'groupbuy_general', true);
			}
			$credits = ceil($item->price * $defaultPrice * $numbers);
			$module = 'groupbuy';
			$type = 1;
			$params = $this -> view -> translate("Pay deal by credits");
            break;
		case 'publish_contest':
			$item = Engine_Api::_()->getItem('yncontest_contest', $item_id);
			// Check if it exists
		    if (!$item) 
		    {
		      	return $this->_helper->redirector->gotoRoute(
				            array(
				            'action' => 'method',
				            'contestId' => $item -> getIdentity(),
				            'id' => $security
				          ), 'yncontest_payment', true);
		    }
			if($security == null)
			{
				return $this->_helper->requireAuth->forward ();
			}
				
			$table = Engine_Api::_() -> getDbTable('transactions', 'yncontest');
			$transactions = $table->getTranBySec($security);
					
			if(count($transactions) == 0)
			{
				return $this->_helper->requireAuth->forward ();
			}
			$firstTransaction = $transactions[0];
			if($firstTransaction->transaction_status != 'pending')
			{
				return $this->_helper->requireAuth->forward ();
			}	
			if($firstTransaction->user_buyer != $this->_user->getIdentity())
			{
				return $this->_helper->requireAuth->forward ();
			}
			$option_service = $firstTransaction -> option_service;
			$final_price = 0;
			foreach($transactions as $transaction){
				$final_price +=$transaction->amount;
			}
			$credits = ceil($final_price * $defaultPrice);
			$module = 'yncontest';
			$type = 0;
			$params = $this -> view -> translate("Pay publishing fee by credits");
			break;
    }
    $this -> view -> item = $item;

    if (!$this->_checkEnoughCredits($credits)) 
    {
      return $this->_forward('success', 'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()
          ->getRouter()
          ->assemble(
            array(),
            'yncredit_general', true
          ),
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('CREDIT_not-enough-credit'))
      ));
    }
	
	// Process
	Engine_Api::_()->yncredit()-> spendCredits($this->_user, (-1) * $credits, $item->getTitle(), $action_type, $item);
	
	if($module == 'groupbuy')
	{
		$receiver = $_SESSION['receiver']; 
	    //create bill
	    $bill =  Groupbuy_Api_Cart::makeBillFromCart($item, $receiver[0], $type, $numbers);  
		if(!$bill || $bill < 0)
		{
			return $this->_forward('success', 'utility', 'core', array(
		        'parentRedirect' => Zend_Controller_Front::getInstance()
		          ->getRouter()
		          ->assemble(
		            array(),
		            'yncredit_general', true
		          ),
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('You can not buy deal, please contact with admin to resolve it!'))
		      ));
		}
	    //update status bill
	    $bill->bill_status = 1;
	    $bill->save();
	    $spent_type = '';
		if($type == 0)
		{
	    	$account_seller = Groupbuy_Api_Cart::getFinanceAccount($bill->owner_id, 1);
			if($account_seller)
	    		Groupbuy_Api_Account::updateAmount($account_seller['paymentaccount_id'], $bill->amount, 1);
			if($item)
		    {
		    	$auto = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.approveAuto', 0);
		        if($auto > 0)
		        {
		            Engine_Api::_()->groupbuy()->approveDeal($item_id);
		        }
		        else
		        {
		            $item -> published = 10;
		            $item -> status = 10;
		            $item -> save(); 
		        }
		    } 
			$spent_type = 'groupbuy_new';
		}
		else 
		{
			$account_seller = Groupbuy_Api_Cart::getFinanceAccount($item->user_id, 2);
    		Groupbuy_Api_Account::updateAmount($account_seller['paymentaccount_id'], $bill->amount - $bill->commission_fee,1);
			
			//check number sell
		    $item -> current_sold = $item -> current_sold + $numbers;
		    if($item->current_sold >= $item->max_sold)
		    {
		        $item->status = 40;
		        $item->end_time = date("Y-m-d H:i:s");
		    }
		    $item->save();
			$spent_type = 'groupbuy_buy';
		}
	    
	    //Save transaction tracking
	    $tttable = Engine_Api::_()->getDbtable('transactionTrackings','groupbuy');
	    $ttdb = $tttable->getAdapter();
	    $ttdb->beginTransaction();
	    try
	    {
	        $ttvalues = array('transaction_date' => date('Y-m-d H:i:s'),
	                          'user_seller' => $item->user_id,
	                          'user_buyer' => $this -> _user ->getIdentity(),
	                          'item_id' => $item_id,
	                          'commission_fee' => $bill->commission_fee,
	                          'currency' => $bill->currency,
	                          'amount' => $bill->amount,
	                          'account_seller_id' => $account_seller['paymentaccount_id'],
	                          'account_buyer_id' => 0,
	                          'number' => $numbers,
	                          'transaction_status' => '1',
	                          'params' => $params,
	        );
	        $ttrow = $tttable->createRow();
	        $ttrow->setFromArray($ttvalues);
	        $ttrow->save();
			$tranid = $ttrow->transactiontracking_id;
	        $ttdb->commit();
			
	        /**
	         * Call Event from Affiliate
	         */
	        $module = 'ynaffiliate';
	        $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
	            $mselect = $modulesTable->select()
	            ->where('enabled = ?', 1)
	            ->where('name  = ?', $module);
	        $module_result = $modulesTable->fetchRow($mselect);
			$params = array();
	        if(count($module_result) > 0)    {
	            $params['module'] = 'groupbuy';
	            $params['user_id'] = $this -> _user->getIdentity();
	            $params['rule_name'] = $action_type;
	            $params['currency'] = $bill->currency;
	            $params['total_amount'] = number_format($bill->amount,2);
	            Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
	        }
	        /**
	         * End Call Event from Affiliate
	         */
	        // User credit integration
            $module = 'yncredit';
            $mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
            $module_result = $modulesTable->fetchRow($mselect);
            if(count($module_result) > 0)    
            {
               $deal = Engine_Api::_()->getItem('deal', $bill->item_id);
               $params['rule_name'] = $spent_type;
               $params['item_id'] = $deal -> getIdentity();
               $params['item_type'] = $deal -> getType();
               Engine_Hooks_Dispatcher::getInstance()->callEvent('onPurchaseItemAfter', $params);
            } 
	    }
	    catch (exception $e) {
	        $ttdb->rollBack();
	        throw $e;
	    }
		if($type == 0)
		{
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage-selling'), 'groupbuy_general', true);
		}
		else 
		{
			//create coupon code
		    for ($i = 1; $i <= $bill->number; $i++) {
		         $coupon_code =  Engine_Api::_()->getDbTable('coupons','groupbuy')->addCoupon($bill->user_id,$bill->item_id,$bill->bill_id, 0, $tranid);
		    }
		    //save to table buy deal
		    $bdtable = Engine_Api::_()->getDbtable('buyDeals','groupbuy');
		    $bddb = $bdtable->getAdapter();
		    $bddb->beginTransaction();
		    try
		    {
		        $bdvalues = array('item_id' => $item_id,
		                          'owner_id' => $item->user_id,
		                          'user_id' => $this -> _user ->getIdentity(),
		                          'amount' => $bill->amount,
		                          'number' => $numbers,
		                          'status' => '2',
		                          'buy_date' => date('Y-m-d H:i:s'),
		        );
		        $bdrow = $bdtable->createRow();
		        $bdrow->setFromArray($bdvalues);
		        $bdrow->save();
		        $bddb->commit();
		    }
		    catch (exception $e) {
		        $bddb->rollBack();
		        throw $e;
		    }
			// send a bill to user.
	        $billInfo =  $bill->toArray();
	        $billInfo['code'] = '';
	        //get all coupon code to seand mail
	        $billInfo['coupon_codes'] =  $bill->getCoupons(' - ');
	        
	        $buyer = Engine_Api::_()->getItem('user', $bill->user_id);
	        $seller = Engine_Api::_()->getItem('user', $bill->owner_id);
	        
	        // get mail service object
	        $mailService = Engine_Api::_()->getApi('mail','groupbuy');
	        // send notification to seller.
	        $mailService->send($seller->email, 'groupbuy_buydealseller',$billInfo);
	        // send notification to buyer
	        $mailService->send($buyer->email, 'groupbuy_buydealbuyer',$billInfo);   
	        $_SESSION['buy_succ'] = true;   	
			return $this->_helper->redirector->gotoRoute(array('action' => 'manage-buying'), 'groupbuy_general', true);
		}
	}
	else if($module == 'yncontest')
	{
		if($option_service == 1)
		{	
			$approve = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.approval', 0);		
			if($approve == 1)
			{
				$item -> approve_status ='approved';
				$item -> contest_status = 'published';
				$item -> approved_date = date('Y-m-d H:i:s');				
			
				//send notification & mail
				$admin = Engine_Api::_() -> user() -> getSuperAdmins() -> getRow(0);
				$owner = Engine_Api::_() -> user() -> getUser($item -> user_id);
				$item -> sendNotMailOwner($owner, $admin, 'contest_approved', 'yncontest_new' );
				
			}	
			else{
				$item -> contest_status = 'waiting';
				$item -> approve_status = 'pending';
			}
			$item -> save();
			foreach($transactions as $transaction)
			{				
				$transaction->transaction_status = 'success';
				switch ($transaction->option_service) 
				{
					case 2:
						$item -> featured_id = 1;
						break;
					case 3:
						$item -> premium_id = 1;
						break;
					case 4:
						$item -> endingsoon_id = 1;
						break;
				}	
						
				$transaction->payment_type = 2;
				$transaction->params = $this -> view -> translate('Credits');				
				$transaction->save();
			}
			$item -> save();
		}
		else
		{
			if($item -> contest_status = "draft")
			{
				$approve = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.approval', 0);		
				if($approve == 1)
				{
					$item -> approve_status ='approved';
					$item -> contest_status = 'published';
					$item -> approved_date = date('Y-m-d H:i:s');				
				
					//send notification & mail
					$admin = Engine_Api::_() -> user() -> getSuperAdmins() -> getRow(0);
					$owner = Engine_Api::_() -> user() -> getUser($item -> user_id);
					$item -> sendNotMailOwner($owner, $admin, 'contest_approved', 'yncontest_new' );
					
				}	
				else{
					$item -> contest_status = 'waiting';
					$item -> approve_status = 'pending';
				}
				$item -> save();
			}
			else 
			{
				//send notification & mail
				$admin = Engine_Api::_() -> user() -> getSuperAdmins() -> getRow(0);
				$owner = Engine_Api::_() -> user() -> getUser($item->user_id);
				$item -> sendNotMailOwner($owner, $admin, 'register_service', null );
			}
			
			foreach($transactions as $transaction)
			{
				$transaction->transaction_status = 'success';
				//update transaction
				$transaction->approve_status = 'approved';
				//update contest
				switch ($transaction -> option_service) {
					case 2:
						$item->featured_id = 1;
						break;
					case 3:
						$item->premium_id = 1;
						break;
					case 4:
						$item->endingsoon_id = 1;
						break;
				}
				
				$transaction->payment_type = 2;
				$transaction->params = $this -> view -> translate('Credits');;
				$transaction->save();
				
			}
			$item -> save();
		}
		return $this->_helper->redirector->gotoRoute(array(), 'yncontest_mycontest', true);			
	}
  }


  protected function _checkEnoughCredits($credits)
  {
    $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
    if (!$balance) {
      return false;
    }
    $currentBalance = $balance->current_credit;
    if ($currentBalance < $credits) {
      return false;
    }
    return true;
  }
}