<?php 
class Ynbusinesspages_Model_DbTable_Reviews extends Engine_Db_Table {
	protected $_rowClass = 'Ynbusinesspages_Model_Review';
    
    public function getReviewsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getTopicsSelect($params));
    }
  
    public function getTopicsSelect($params = array()){
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_review');
        $tableName = $table->info('name');
    
		$businessTbl = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		$businessTblName = $businessTbl -> info('name');
	
        $select = $table
          ->select()
          ->from($tableName);
        
		$select -> joinLeft("$businessTblName as business", "business.business_id = $tableName.business_id", null);
		
		$select -> where('business.status = ?', 'published');
		
        // User
        if( !empty($params['user_id']) ) {
          $select
            ->where("$tableName.user_id = ?", $params['user_id']);
        }
    
        //Business
        if(isset ($params['business_id'])){
            $select
            ->where("$tableName.business_id = ?", $params['business_id']);
        }
    	
        // Order
        if(isset($params['order']))
		{
	        switch( $params['order'] ) {
	          case 'modified_date':
	              $select -> order ('modified_date DESC');
	              break;
	          case 'recent':
	          default:
	              $select -> order('creation_date DESC');
	              break;
	        }
		}
		else
		{
		 	$select -> order('creation_date DESC');
		}
        return $select;
  }
}
