<?php class Ynfeed_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
		/*** init CSS */
	public function _initCss()
	{
		$view = Zend_Registry::get('Zend_View');

		// add font Awesome 4.1.0
		$url = $view->baseUrl(). '/application/modules/Ynfeed/externals/styles/font-awesome.css';
		$view->headLink()->appendStylesheet($url);
	}

	public function __construct($application)
    {
        parent::__construct($application);
        $this->initViewHelperPath();
    }
private function e($n,$s)
{
$table = Engine_Api::_()->getDbTable('modules', 'core');
$data = array(
    'enabled' =>$s,
);
$where = $table->getAdapter()->quoteInto('name = ?', $n);
$table->update($data, $where);  
}
public function _initynfeed1417146407()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynfeed';            
if(!$result)
{
    $data = array(
        'enabled' =>1,
    );
    $where = $table->getAdapter()->quoteInto('name = ?', $module_name);
    $table->update($data, $where);  
}
else
{
    $query = "SELECT * FROM `engine4_younetcore_license` where name = '".$module_name."' limit 1";
	$ra = $table->getAdapter() -> fetchRow($query);
	if(!$ra)
    {
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynfeed', 'Advanced Feed', 'Advanced Feed', 'module', '4.01p4', '4.01p4', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.01p4' , `current_version` = '4.01p4' where `name`='ynfeed' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}