<?php
 
class Ynsocialads_Model_DbTable_Packages extends Engine_Db_Table
{
  protected $_rowClass = 'Ynsocialads_Model_Package';
  protected $_serializedColumns = array('modules','allowed_ad_types');
   public function getPackagesSelect($params = array())
  {
	$select = $this-> select();
	$select -> where('deleted <> 1');
	if (!empty($params['name'])) {
			$select -> where('title LIKE ?', '%'.$params['name'].'%');
	}
	if (empty($params['direction'])) {
			$params['direction'] = 'DESC';
	}
		
    if (!empty($params['order'])) {
    	if($params['order'] == 'benefit_type'){
    		$select -> order($params['order'] . ' ' . $params['direction']);
			$select -> order('benefit_type' . ' ' . $params['direction']);
    	}
		else{
			$select -> order($params['order'] . ' ' . $params['direction']);
		}
		
	} else {
		$select -> order('order ASC');
	}
	return $select;
  }
  
  public function getPackagesPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getPackagesSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    return $paginator;
  }
}
