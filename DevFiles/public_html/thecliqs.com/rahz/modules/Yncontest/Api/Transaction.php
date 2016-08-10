<?php

class Yncontest_Api_Transaction {

	static private $_instance;

	private function __construct() {

	}
	
	static public function getInstance() {
		if(self::$_instance == NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	static public function addTransaction($params1, $params2 = null, $params3 = null, $params4 = null, $params5 = null) {
		$Trans = new Yncontest_Model_DbTable_Transactions;
		$trans = $Trans -> fetchNew();

		$trans -> setFromArray($params1);

		if(is_array($params2)) {
			$trans -> setFromArray($params2);
		}

		if(is_array($params3)) {
			$trans -> setFromArray($params3);
		}
		if(is_array($params4)) {
			$trans -> setFromArray($params4);
		}
		if(is_array($params5)) {
			$trans -> setFromArray($params5);
		}

		$trans -> save();
	}
	
	public function getTransactionsPaginator($params = array())
	{
	    $paginator = Zend_Paginator::factory($this->getTransactionsSelect($params));
	   
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	  
	public function getTransactionsSelect($params = array())
	{
	  	$table = new Yncontest_Model_DbTable_Transactions();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
		
		$userTable  = new User_Model_DbTable_Users;
		$userName = $userTable->info('name');
	    $select -> joinLeft($userName, "user_id = user_buyer",'username as owner_name');
		
		$contestTable = new Yncontest_Model_DbTable_Contests;
		$contestName = $contestTable->info('name');
		$select ->joinLeft($contestName," $rName.contest_id = $contestName.contest_id");
		
		
	    //$select->where("payment_status = 'pending'");
		//$select->orWhere("payment_status = 'completed'");
		
	    // by search
		
		if (isset($params['user_id']) && $params['user_id'] != "")
		{
			$user_id = $params['user_id'];			
			$select -> where("$rName.user_buyer = ?", $user_id);
		}
		
		if (isset($params['contest_name']) && $params['contest_name'] != "")
		{
			$name = $params['contest_name'];			
			$select -> where("$contestName.contest_name Like ?", "%$name%");
		}
		
		if (isset($params['owner_name']) && $params['owner_name'] != "")
		{
			$name = $params['owner_name'];			
			$select -> where("engine4_users.displayname Like ?", "%$name%");
			$select -> orWhere("$userName.username LIKE ?",'%'.$params['owner_name'].'%');
		}
	 
		
	   if( isset($params['status']) && $params['status'] != '')
	   {
	   	  $select->where("transaction_status = ? ",$params['status']);
	   }
		
	  if( isset($params['from']) && $params['from'] != '')
	   {
	   	  $date = date("Y/m/d",strtotime($params['from']));
	   	  $select->where("transaction_date >= ? ",$date." 00:00:00");
	   }
	   
	   if( isset($params['to']) && $params['to'] != '')
	   {
	   	 $date = date("Y/m/d",strtotime($params['to']));
	   	  $select->where("transaction_date <= ? ",$date." 23:59:59");
	   }

	    
		$select->where("$rName.payment_type is not null");
	    		
	    // by status

	    
		if(isset($params['orderby']) && $params['orderby'])
	       	$select->order($params['orderby'].' DESC');
	    
	    elseif (!empty($params['order'])) {
			$select->order($params['order'].' '.$params['direction']);
		}
	    else
		{
	        $select->order("$rName.transaction_date DESC");
	    }
	    
	    
		if(getenv('DEVMODE') == 'localdev'){
			print_r($params);
			echo $select;	
		}
		
		//echo $select; 
	    return $select;
	  }

}
