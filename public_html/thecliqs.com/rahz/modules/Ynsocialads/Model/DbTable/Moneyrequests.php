<?php
 
class Ynsocialads_Model_DbTable_Moneyrequests extends Engine_Db_Table
{
  protected $_rowClass = 'Ynsocialads_Model_Moneyrequest';
  
    public function getMoneyRequestsSelect($params = array())
  {
	$tableMoneyrequestsTable = Engine_Api::_()->getItemTable('ynsocialads_moneyrequest');
    $tableMoneyrequestsName= $tableMoneyrequestsTable->info('name');
	
	$tableUserTable = Engine_Api::_() -> getDbtable('users', 'user');
    $tableUserName = $tableUserTable->info('name');
	
	$select = $tableMoneyrequestsTable->select()->from(array('moneyreq' => $tableMoneyrequestsName ));
	$select -> setIntegrityCheck(false)
                -> joinLeft("$tableUserName as user","user.user_id = moneyreq.user_id",'');
				
	if (!empty($params['status']) && $params['status'] != 'all') {
			$select -> where('moneyreq.status = ?',$params['status']);
	}		
	if (!empty($params['name'])) {
			$select -> where('user.displayname LIKE ?',"%".$params['name']."%");
	}
	if(!empty($params['from']))
	{
		    $from = strtotime($params['from']);
   	 		$from_date = date('Y-m-d H:i:s', $from);
			$select -> where('moneyreq.request_date >= ?', $from_date);
	}
	if(!empty($params['to']))
	{
		    $to = strtotime($params['to']);
   	 		$to_date = date('Y-m-d H:i:s', $to);
			$select -> where('moneyreq.request_date <= ?', $to_date);
	}
	if (empty($params['direction'])) {
			$params['direction'] = 'DESC';
	}
    if (!empty($params['order'])) {
		$select -> order($params['order'] . ' ' . $params['direction']);
	} else {
		$select -> order('moneyreq.moneyrequest_id DESC');
	}
	return $select;
  }
  
  public function getMoneyRequestsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getMoneyRequestsSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    return $paginator;
  }
}
