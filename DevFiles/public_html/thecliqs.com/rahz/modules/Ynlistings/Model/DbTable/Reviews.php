<?php 
class Ynlistings_Model_DbTable_Reviews extends Engine_Db_Table {
	protected $_rowClass = 'Ynlistings_Model_Review';
    
    public function getReviewsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getTopicsSelect($params));
    }
  
    public function getTopicsSelect($params = array()){
        $table = Engine_Api::_()->getItemTable('ynlistings_review');
        $tableName = $table->info('name');
    
        $select = $table
          ->select()
          ->from($tableName);
        
        // User
        if( !empty($params['user_id']) ) {
          $select
            ->where("$tableName.user_id = ?", $params['user_id']);
        }
    
        //Listing
        if(isset ($params['listing_id'])){
            $select
            ->where("$tableName.listing_id = ?", $params['listing_id']);
        }
    
        // Order
        switch( $params['order'] ) {
          case 'modified_date':
              $select -> order ('modified_date DESC');
              break;
          case 'recent':
          default:
              $select -> order('creation_date DESC');
              break;
        }
        return $select;
  }
}
