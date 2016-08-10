<?php
class Yncredit_Model_DbTable_Balances extends Engine_Db_Table
{
	protected $_name = "yncredit_balances";
	protected $_rowClass = "Yncredit_Model_Balance";
	
	public function getBalanceStatistics()
	{
		$select = $this -> select() -> from($this -> info('name'), 
		"SUM(current_credit) as current,
		SUM(earned_credit) as earned,
		SUM(spent_credit) as spent,
		SUM(bought_credit) as bought,
		SUM(sent_credit) as sent,
		SUM(received_credit) as received
		");
		return $this -> fetchRow($select);
	}
	
	public function getMembersPaginator($params = array())
	{
		$select = $this -> getMembersSelect($params);
		return Zend_Paginator::factory($select);
	}
	
	public function getMembersSelect($params = array())
	{
		$balanceName = $this -> info('name');
		$userTable = Engine_Api::_()->getItemTable('user');
		$userName = $userTable -> info('name');	
		
		$select = $this -> select() 
			-> from($balanceName) -> setIntegrityCheck(false);
		if(!empty($params['top']) && $params['top'])
		{
			$select -> join($userName, "$userName.user_id = $balanceName.user_id", "$userName.user_id");
		}
		else
		{
			$select -> joinRight($userName, "$userName.user_id = $balanceName.user_id", "$userName.user_id");
		}
		if(!empty($params['title']))
		{
			$select -> where("$userName.displayname LIKE ?", "%".$params['title']."%");
		}
		if($params['orderby'] == 'displayname')
		{
			$select -> order($userName . "." . $params['orderby'] . ' ' . $params['direction']);
		}
		else if($params['orderby'] == 'user_id')
		{
			$select -> order($userName . "." . $params['orderby'] . ' ' . $params['direction']);
		}
		else 
		{
			$select -> order(!empty($params['orderby']) ? $balanceName . "." . $params['orderby'] . ' ' . $params['direction'] : $userName . '.user_id DESC');
		}
		return $select;
	}
}