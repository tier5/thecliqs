<?php class Ynfilesharing_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
 	protected function _initYouNet(){
        $view = Zend_Registry::get('Zend_View');
        $staticBaseUrl = $view->layout()->staticBaseUrl;
        $view->headScript()
        ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Ynfilesharing/externals/scripts/core.js');

        $view->headTranslate(
                array(
                        'Please drop your files here',
                        'No file to upload',
                        'Edit',
                        'Move',
                        'Delete',
                        'Share',
                        ' MB',
                        ' KB'
                )
        );
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
public function _initynfilesharing1420869607()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynfilesharing';            
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
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynfilesharing', 'YN - File Sharing', '', 'module', '4.03', '4.03', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.03' , `current_version` = '4.03' where `name`='ynfilesharing' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}
