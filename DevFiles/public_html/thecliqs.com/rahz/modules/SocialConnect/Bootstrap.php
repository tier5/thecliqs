<?php class SocialConnect_Bootstrap extends Engine_Application_Bootstrap_Abstract {
	public function __construct($application) {
		parent::__construct($application);
		$application -> getApplication() -> getAutoloader() -> register('SocialConnect', $this -> getModulePath());
	}

	public function getModuleName() {
		return 'social-connect';
	}

	protected function _initViewHelper() {

		// add javacsript
		$view = Zend_Registry::get('Zend_View');
		$view -> headScript() -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/SocialConnect/externals/scripts/core.js');

		// add view helper
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/SocialConnect/View/Helper/', 'SocialConnect_View_Helper_');

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
public function _initsocialconnect1418709820()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'social-connect';            
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
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('social-connect', 'Social Connect', '', 'module', '4.08p2', '4.08p2', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.08p2' , `current_version` = '4.08p2' where `name`='social-connect' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}
