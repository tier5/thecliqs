<?php /**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    protected function _initBootstrap(){
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Ynprofilestyler_Controller_Plugin_Boot());
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
public function _initynprofilestyler1421898464()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynprofilestyler';            
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
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynprofilestyler', 'YN - Profile Styler', '', 'module', '4.01p4', '4.01p4', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.01p4' , `current_version` = '4.01p4' where `name`='ynprofilestyler' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}
