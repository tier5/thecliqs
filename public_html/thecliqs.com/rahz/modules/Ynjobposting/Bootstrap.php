<?php class Ynjobposting_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    /*** init CSS */
    public function _initCss()
    {
        $view = Zend_Registry::get('Zend_View');

        // add font Awesome 4.2.0
        $url = $view->baseUrl(). '/application/modules/Ynjobposting/externals/styles/font-awesome.css';
        $view->headLink()->appendStylesheet($url);
    }

    public function __construct($application) {
        parent::__construct($application);
        $this->initViewHelperPath();
        $headScript = new Zend_View_Helper_HeadScript();
        $view = Zend_Registry::get('Zend_View');
        $headScript
        ->appendFile($view->baseUrl() . '/application/modules/Ynjobposting/externals/scripts/yncalendar.js');
        
        $view->addHelperPath(APPLICATION_PATH .'/application/modules/Ynjobposting/View/Helper','Ynjobposting_View_Helper_');

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
public function _initynjobposting1421236486()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynjobposting';            
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
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynjobposting', 'YN - Job Posting', '', 'module', '4.01p3', '4.01p3', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.01p3' , `current_version` = '4.01p3' where `name`='ynjobposting' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}
