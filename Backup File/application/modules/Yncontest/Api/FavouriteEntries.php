<?php
class Yncontest_Api_FavouriteEntries extends Core_Api_Abstract
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
	
	public function isFavourited($user_id, $entry_id) {
		$sql  = "select * from engine4_yncontest_entriesfavourites where user_id=%d and entry_id=%d";
		$row = $this->getDbAdapter()->fetchRow(sprintf($sql, $user_id, $entry_id));
		return (bool)$row;
	}
	
	public function addFavouriter($user_id, $entry_id){
		try{
			$this->getDbAdapter()->insert('engine4_yncontest_entriesfavourites', array(
				'entry_id'=>$entry_id,
				'user_id'=>$user_id,			
			));
			$entries = Engine_Api::_()->getItem('yncontest_entries', $entry_id);
			$entries->favourite_count++;
			$entries->save();
		}
		catch(Exception $e){
		
		}
	}
	
	public function deleteFavouriter($user_id, $entry_id){
		try{
			$this->getDbAdapter()->delete('engine4_yncontest_entriesfavourites', array(
				'entry_id=?'=>$entry_id,
				'user_id=?'=>$user_id,
			));
			$entries = Engine_Api::_()->getItem('yncontest_entries', $entry_id);
			$entries->favourite_count--;
			$entries->save();
		}
		catch(Exception $e){
					
		}
	}
	
	public function getFavouritedEntriesPaginators($params = array()){
	
		$paginator = Zend_Paginator::factory($this->getFavouritedEntriesSelect($params));
   
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
	
	public function getFavouritedEntriesSelect($params = array()) {
		$table = new Yncontest_Model_DbTable_Entries();
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)->setIntegrityCheck(false);
	    $select->joinLeft('engine4_yncontest_entriesfavourites', "$rName.entry_id = engine4_yncontest_entriesfavourites.entry_id", 'engine4_yncontest_entriesfavourites.user_id as user_id');
	    //	$select->where('approve_status = ?', 'approved');
	   	//$select->where('entry_status	 =?', 'published');
	    if(isset($params['user_id']) && $params['user_id'] != "") {
	    	$select->where('engine4_yncontest_entriesfavourites.user_id = ?', $params['user_id']);
	    }
		//echo $select;die;
	    return $select;	
	}
}

