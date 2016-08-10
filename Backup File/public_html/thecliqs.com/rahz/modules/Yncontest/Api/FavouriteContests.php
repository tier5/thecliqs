<?php
class Yncontest_Api_FavouriteContests extends Core_Api_Abstract
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
	
	public function isFavourited($user_id, $contest_id) {
		$sql  = "select * from engine4_yncontest_favourites where user_id=%d and contest_id=%d";
		$row = $this->getDbAdapter()->fetchRow(sprintf($sql, $user_id, $contest_id));
		return (bool)$row;
	}
	
	public function addFavouriter($user_id, $contest_id){
		try{
			$this->getDbAdapter()->insert('engine4_yncontest_favourites', array(
				'contest_id'=>$contest_id,
				'user_id'=>$user_id,			
			));
			$contest = Engine_Api::_()->getItem('contest', $contest_id);
			$contest->favourite_count++;
			$contest->save();
		}
		catch(Exception $e){
		
		}
	}
	
	public function deleteFavouriter($user_id, $contest_id){
		try{
			$this->getDbAdapter()->delete('engine4_yncontest_favourites', array(
				'contest_id=?'=>$contest_id,
				'user_id=?'=>$user_id,
			));
			$contest = Engine_Api::_()->getItem('contest', $contest_id);
			$contest->favourite_count--;
			$contest->save();
		}
		catch(Exception $e){
					
		}
	}
	
	public function getFavouritedContestsPaginators($params = array()){
	
		$paginator = Zend_Paginator::factory($this->getFavouritedContestsSelect($params));
   
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
	
	public function getFavouritedContestsSelect($params = array()) {
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
	    $select->joinLeft('engine4_yncontest_favourites', "$rName.contest_id = engine4_yncontest_favourites.contest_id", 'engine4_yncontest_favourites.user_id as user_id');
	   	//$select->where('approve_status = ?', 'approved');
	   	//$select->where('contest_status	 =?', 'published');
	    if(isset($params['user_id']) && $params['user_id'] != "") {
	    	$select->where('engine4_yncontest_favourites.user_id = ?', $params['user_id']);
	    }
		
	    return $select;	
	}
}