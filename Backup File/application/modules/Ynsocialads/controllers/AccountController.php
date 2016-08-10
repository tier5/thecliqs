<?php

class Ynsocialads_AccountController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynsocialads_account', array(), 'ynsocialads_account_payment_transactions');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getItemTable('ynsocialads_transaction');
		$select = $table -> select() -> where('user_id = ?', $viewer -> getIdentity());

		$view = $this -> view;
		$methods = array(
			'-1' => $view -> translate('Pay with Virtual Money'),
			'-2' => $view -> translate('Pay Later')
		);

		$this -> view -> form = $form = new Ynsocialads_Form_Transactions_Filter();
		if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
		{
			$form -> gateway_id -> addMultiOption(-3, $view -> translate('Pay with Credit'));
			$methods['-3'] = $view -> translate('Pay with Credit');
		}

		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
		$gateways = $gatewayTable -> fetchAll($gatewaySelect);
		foreach ($gateways as $gateway)
		{
			$form -> gateway_id -> addMultiOption($gateway -> gateway_id, $view -> translate('Pay with ' . $gateway -> title));
			$methods['' . $gateway -> gateway_id] = $view -> translate('Pay with ' . $gateway -> title);
		}

		$this -> view -> methods = $methods;
		$form -> populate($this -> _getAllParams());
		$values = $form -> getValues();

		if ($values['status'] == 'All')
		{
			$statusArr = array(
				'initialized',
				'expired',
				'pending',
				'completed',
				'canceled'
			);
		}
		else
		{
			$statusArr = array($values['status']);
		}
		$select = $select -> where('status IN (?)', $statusArr);

		if ($values['gateway_id'] != 'All')
		{
			$select = $select -> where('gateway_id = ?', $values['gateway_id']);
		}
		$this -> view -> formValues = $values;

		$transactions = $table -> fetchAll($select);

		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($transactions);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);

		$this -> _helper -> content -> setEnabled();
	}

	public function virtualMoneyAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;

		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynsocialads_account', array(), 'ynsocialads_account_virtual_money');

		$viewer = Engine_Api::_() -> user() -> getViewer();

		//TODO get total virtual Money and remaining
		//get max amount and max amount

		$virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
		$select = $virtualTable -> select() -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1);
		$virtual_money = $virtualTable -> fetchRow($select);
		$remaining = $virtual_money -> remain;
		$total = $virtual_money -> total;
        $remaining = ($remaining) ? $remaining : 0;
        $total = ($total) ? $total : 0;
        $this->view->total = $total;
        $this->view->remaining = $remaining;
        
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
		$min_amount = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynsocialads_money', 'min_amount');
        if ($min_amount == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $viewer->level_id)
            ->where('type = ?', 'ynsocialads_money')
            ->where('name = ?', 'min_amount'));
            if ($row) {
                $min_amount = $row->value;
            }
        }
		$max_amount = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynsocialads_money', 'max_amount');
        if ($max_amount == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $viewer->level_id)
            ->where('type = ?', 'ynsocialads_money')
            ->where('name = ?', 'max_amount'));
            if ($row) {
                $max_amount = $row->value;
            }
        }
        
		$this -> view -> max_amount = $max_amount;
		$this -> view -> min_amount = $min_amount;

		$this -> view -> form = $form = new Ynsocialads_Form_Moneyrequests_Filter();

		$form -> total -> setValue($total);
		$form -> remaining -> setValue($remaining);

		$table = Engine_Api::_() -> getItemTable('ynsocialads_moneyrequest');
		$select = $table -> select() -> where('user_id = ?', $viewer -> getIdentity());

		$form -> populate($this -> _getAllParams());
		$values = $form -> getValues();
		$this -> view -> formValues = $values;
		$view = $this -> view;
		if ($values['status'] == 'All')
		{
			$statusArr = array(
				$view -> translate('pending'),
				$view -> translate('approved'),
				$view -> translate('rejected')
			);
		}
		else
		{
			$statusArr = array($values['status']);
		}
		$select = $select -> where('status IN (?)', $statusArr);

		$requests = $table -> fetchAll($select);

		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($requests);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);

		$this -> _helper -> content
		// ->  setNoRender()
		-> setEnabled();

	}

	public function addRequestAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//TODO get total virtual Money and remaining
		//get max amount and max amount
		$virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
		$select = $virtualTable -> select() -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1);
		$virtual_money = $virtualTable -> fetchRow($select);

		$remaining = $virtual_money -> remain;
		$total = $virtual_money -> total;

		$this -> view -> total = $total;
		$this -> view -> remaining = $remaining;

		if ($remaining < $min_amount)
		{
			//TODO throw error: remaining < min amount
			$this -> _helper -> content -> setNoRender();
			return;
		}
		$this -> view -> form = $form = new Ynsocialads_Form_Moneyrequests_Add();

		if ($this -> getRequest() -> isPost())
		{
			if (!$form -> isValid($this -> getRequest() -> getPost()))
			{
				return;
			}
			$values = $this -> getRequest() -> getPost();

			$db = Engine_Db_Table::getDefaultAdapter();
			$table = Engine_Api::_() -> getItemTable('ynsocialads_moneyrequest');
			$db -> beginTransaction();

			try
			{
				$new_request = $table -> createRow();
				$new_request -> request_date = date('Y-m-d H:i:s');
				$new_request -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
				$new_request -> status = 'Pending';
				$new_request -> user_id = $viewer -> getIdentity();
				$new_request -> setFromArray($values);
				$new_request -> save();

				$virtual_money -> remain = $remaining - $values['amount'];
				$virtual_money -> save();

				$db -> commit();
			}

			catch(Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}
	}

}
