<?php 
class Yncontest_Api_Statistic extends Core_Api_Abstract {  
	
	static private $_instance;
	
	static public function getInstance() {
		if(self::$_instance == NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
	public function getTotalContest() {
		$sql = " select count(*) from engine4_yncontest_contests";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	public function getContestAlbum() {
		$sql = " select count(*) from engine4_yncontest_contests WHERE contest_type = 'advalbum'";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getContestVideo() {
		$sql = " select count(*) from engine4_yncontest_contests WHERE contest_type = 'ynvideo'";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne($sql);
		return round((double)$result, 2);
	}
	
	public function getContestBlog() {
		$sql = " select count(*) from engine4_yncontest_contests WHERE contest_type = 'ynblog'";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	public function getApprovedContest() {
		$sql = "select count(*) from engine4_yncontest_contests contest
				where
				contest.approve_status = 'approved'
			   	";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getFeaturedContest() {
		$sql = "select count(*) from engine4_yncontest_contests contest
				where
				contest.featured_id = '1' and contest.approve_status = 'approved'				
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
		
	
	public function getPremiumContest() {
		$sql = "select count(*) from engine4_yncontest_contests contest
				where
				contest.premium_id = '1' and contest.approve_status = 'approved'	
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getEndingSoonContest() {
		$sql = "select count(*) from engine4_yncontest_contests contest
				where
				contest.endingsoon_id = '1' and contest.approve_status = 'approved'	
				";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getFollowContest() {
		$sql = "select sum(follow_count) from engine4_yncontest_contests contest";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getFavoriteContest() {
		$sql = "select sum(favourite_count) from engine4_yncontest_contests contest";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	
	public function getTotalEntries() {
		$sql = "
				select count(*) from engine4_yncontest_entries
			   	";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getFollowEntries() {
		$sql = "
				select sum(follow_count) from engine4_yncontest_entries
			   	";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getFavoriteEntries() {
		$sql = "
				select sum(favourite_count) from engine4_yncontest_entries
			   	";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
		
	
	
	public function getPublishFee() {
		$sql = "select 
				sum(amount) as total_amount
				from engine4_yncontest_transactions as transaction
				where transaction.option_service =  '1'	AND transaction.payment_type is not NULL			
			  ";		
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	public function getFeaturedFee() {
		$sql = "select 
				sum(amount) as total_amount
				from engine4_yncontest_transactions as transaction
				where transaction.option_service =  '2' AND transaction.payment_type is not NULL
			  ";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);		
	}
	
	public function getPremiumFee() {
		$sql = "select 
				sum(amount) as total_amount
				from engine4_yncontest_transactions as transaction
				where transaction.option_service =  '3' AND transaction.payment_type is not NULL
			  ";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);		
	}
	
	public function getEndingSoonFee() {
		$sql = "select 
				sum(amount) as total_amount
				from engine4_yncontest_transactions as transaction
				where transaction.option_service =  '4' AND transaction.payment_type is not NULL
			  ";		
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
	
	
	public function getContestOwner($user_id){
					$sql = " select count(*) from engine4_yncontest_contests where user_id =".$user_id;
					$db = Engine_Db_Table::getDefaultAdapter();
					$result = $db -> fetchOne(new Zend_Db_Expr($sql));
					return round((double)$result, 2);
	}
	public function getMemberOwner($user_id){
				$table = Engine_Api::_()->getDbtable('contests', 'yncontest');
  				$Name = $table->info('name');
				
				$select = $table->select()->from($Name,new Zend_Db_Expr("
				(SELECT COUNT(*) FROM engine4_yncontest_membership WHERE engine4_yncontest_membership.resource_id = $Name.contest_id GROUP BY engine4_yncontest_membership.resource_id )AS participants				
				"))->setIntegrityCheck(false);
				
				$select->where("$Name.user_id = ?",$user_id);

				$db = Engine_Db_Table::getDefaultAdapter();
				$result = $db -> fetchOne($select);
				return round((double)$result, 2);
	}
	
	public function getEntriesOwner($user_id){
				$sql = "
				select count(*) from engine4_yncontest_entries where user_id = $user_id
			   	";
				$db = Engine_Db_Table::getDefaultAdapter();
				$result = $db -> fetchOne(new Zend_Db_Expr($sql));
				return round((double)$result, 2);
	}
	
	public function getLikeOwner($user_id)
	{
					$sql = " select sum(like_count) from engine4_yncontest_contests where user_id =".$user_id;
					$db = Engine_Db_Table::getDefaultAdapter();
					$result = $db -> fetchOne(new Zend_Db_Expr($sql));
					return round((double)$result, 2);
	}
	
	public function getViewOwner($user_id)
	{
					$sql = " select sum(view_count) from engine4_yncontest_contests where user_id =".$user_id;
					$db = Engine_Db_Table::getDefaultAdapter();
					$result = $db -> fetchOne(new Zend_Db_Expr($sql));
					return round((double)$result, 2);
	}
	
	public function getTotalWinner() {
		$sql = "
				select count(DISTINCT(user_id)) as count from engine4_yncontest_entries where entry_status = 'win' 
			   	";
		$db = Engine_Db_Table::getDefaultAdapter();
		$result = $db -> fetchOne(new Zend_Db_Expr($sql));
		return round((double)$result, 2);
	}
}