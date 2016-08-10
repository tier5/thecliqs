<?php
class Yncontest_Api_FollowContests extends Core_Api_Abstract
{
	static private $_instance;
	
	static public function getInstance(){
		if(self::$_instance == null){
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	private function __construct(){}
	
	public function getDbAdapter(){
		return Engine_Db_Table::getDefaultAdapter();
	}
	
	
	
	public function getFollowContestsPaginators($params = array()){
	
		$paginator = Zend_Paginator::factory($this->getFollowContestsSelect($params));
   
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
	
	public function getFollowContestsSelect($params = array()) {
		$table = new Yncontest_Model_DbTable_Contests();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName, new Zend_Db_Expr("$rName.*,
				(SELECT COUNT(*) FROM engine4_yncontest_membership WHERE engine4_yncontest_membership.resource_id = $rName.contest_id GROUP BY engine4_yncontest_membership.resource_id )AS participants
				,(SELECT COUNT(*) FROM engine4_yncontest_entries en WHERE en.contest_id = $rName.contest_id) AS entries				
				,(TIMESTAMPDIFF(YEAR,now(),$rName.end_date)) AS yearleft				
				,(TIMESTAMPDIFF(MONTH,now(),$rName.end_date)) AS monthleft
				,(TIMESTAMPDIFF(DAY,now(),$rName.end_date)) AS dayleft				
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%H')) AS hourleft			
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%i')) AS minuteleft
				
				"))->setIntegrityCheck(false);
	    $select->joinLeft('engine4_yncontest_follows', "$rName.contest_id = engine4_yncontest_follows.contest_id", 'engine4_yncontest_follows.user_id as user_id');
	   	//$select->where('approve_status = ?', 'approved');
	   	//$select->where('contest_status	 =?', 'published');
	    if(isset($params['user_id']) && $params['user_id'] != "") {
	    	$select->where('engine4_yncontest_follows.user_id = ?', $params['user_id']);
	    }
		//echo $select;die;
	    return $select;	
	}
}