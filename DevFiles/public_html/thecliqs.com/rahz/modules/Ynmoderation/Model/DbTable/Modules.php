<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynmoderation
 * @author     YouNet Company
 */

class Ynmoderation_Model_DbTable_Modules extends Engine_Db_Table {
	protected $_rowClass = "Ynmoderation_Model_Module";

	
  /**
   * Gets a paginator for ynmoderation_module
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getModulesPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getModulesSelect($params));
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
  
  
    /**
   * Gets a select object for the moderation modules
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getModulesSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('modules', 'ynmoderation');
    $tableName = $table->info('name');

    $select = $table->select()
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : $tableName.'.id DESC' );
    
    if( !empty($params['enabled']) && is_numeric($params['enabled']) ) {
       $select->where($tableName.'.enabled = ?', $params['enabled']);
    }

  	if( !empty($params['id']) && is_numeric($params['id']) ) {
       $select->where($tableName.'.id = ?', $params['id']);
    }
    
    if( !empty($params['name']) ) {
       $select->where($tableName.'.name = ?', $params['name']);
    }

    if( !empty($params['object_type']) ) {
       $select->where($tableName.'.object_type = ?', $params['object_type']);
    }
    
    if ( !empty($params['having_query']) && $params['having_query']) {
    	$select->where($tableName.".moderation_query != ''");	
    }
    
    
    return $select;
  }
  
  
  public function getTypesAssoc()
  {
    $types = $this->select()
        ->from($this, array('id', 'name'))
        ->where("moderation_query != ''")
        ->order(' id ASC ')
        ->query()
        ->fetchAll();
    
    $data = array();
    foreach( $types as $type ) {
      $data[$type['id']] = $type['name'];
    }
    
    return $data;
  }
  
  
  
  
  
  
  
}