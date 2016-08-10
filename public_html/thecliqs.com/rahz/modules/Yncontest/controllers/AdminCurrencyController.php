<?php

class Yncontest_AdminCurrencyController extends Core_Controller_Action_Admin {

	public function init() {
		parent::init();
		Zend_Registry::set('admin_active_menu', 'yncontest_admin_main_currencies');

	}

	public function indexAction() {
		
		$table = new Yncontest_Model_DbTable_Currencies;
		$select = $table -> select();

		$paginator = $this -> view -> paginator = Zend_Paginator::factory($select);
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		$paginator -> setItemCountPerPage(10);
	}

	public function editCurrencyAction() {
		//Get Form Edit Currency
		$form = $this -> view -> form = new Yncontest_Form_Admin_Currency();

		//Check post method
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {

			$values = $form -> getValues();
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try {
				// Edit currency in the database
				$code = $values["code"];
				$table = new Yncontest_Model_DbTable_Currencies;
				$select = $table -> select() -> where('code = ?', "$code");
				$row = $table -> fetchRow($select);

				$row -> name = $values["label"];
				$row -> symbol = $values["symbol"];
				$row -> precision = $values["precision"];
				$row -> display = $values["display"];
				
				if(isset($values["status"]))
					$row -> status = $values["status"];
				//Database Commit
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			//Close Form If Editing Successfully
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Get Code Id - Throw Exception If There Is No Code Id
		if(!($code = $this -> _getParam('code_id'))) {
			throw new Zend_Exception('No code id specified');
		}

		// Generate and assign form
		$table = new Yncontest_Model_DbTable_Currencies;
		$select = $table -> select() -> where('code = ?', "$code");
		$currency = $table -> fetchRow($select);
		$form -> populate(array('label' => $currency -> name, 'symbol' => $currency -> symbol, 'precision' => $currency -> precision, 'status' => $currency -> status, 'display' => $currency -> currencyDisplay(), 'code' => $currency -> code));

		//Hide Status Element if modifing the default currency
		if($code == Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contest.currency', 'USD')) {
			if($form -> getElement('status')) {
				$form -> removeElement('status');
			}
		}
		//Output
		$this -> renderScript('admin-currency/form.tpl');

	}

}
