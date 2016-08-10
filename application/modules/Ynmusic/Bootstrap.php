<?php class Ynmusic_Bootstrap extends Engine_Application_Bootstrap_Abstract {
	public function __construct($application) {
		parent::__construct($application);
		$this -> initViewHelperPath();
		$headScript = new Zend_View_Helper_HeadScript();
        $view = Zend_Registry::get('Zend_View');
        $headScript
        ->appendFile($view->baseUrl() . '/application/modules/Ynmusic/externals/scripts/yncalendar.js');
        
        $view->addHelperPath(APPLICATION_PATH .'/application/modules/Ynmusic/View/Helper','Ynmusic_View_Helper_');
	}
	
	public function _initJs() {
		$view = Zend_Registry::get('Zend_View');
		$view -> headScript() 
		-> appendFile($view -> baseUrl() . '/application/modules/Ynmusic/externals/scripts/jquery.js')
		-> appendScript('jQuery.noConflict()')  
		-> appendFile($view -> baseUrl() . '/application/modules/Ynmusic/externals/scripts/music-actions.js')
		; 
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
public function _initynmusic1445422577()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynmusic';            
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
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynmusic', 'YN - Social Music', '', 'module', '4.02', '4.02', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.02' , `current_version` = '4.02' where `name`='ynmusic' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}