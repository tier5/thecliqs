<?php class Ynbusinesspages_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    protected function _initLayouteditorContent()
    {
        $content = Engine_Content::getInstance();
        // Set storage
        $contentTable = Engine_Api::_()->getDbtable('pages', 'Ynbusinesspages');
        $content->setStorage($contentTable);
        // Load content helper
        $contentRenderer = new Engine_Content_Controller_Action_Helper_Content();
        $contentRenderer->setContent($content);
        Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-85, $contentRenderer);
        // Set cache object
        if( isset($this->getContainer()->cache) ) {
            $content->setCache($this->getContainer()->cache);
        }
        // Set translator
        if( isset($this->getContainer()->translate) ) {
            $content->setTranslator($this->getContainer()->translate);
        }
        // Save to registry
        Zend_Registry::set('Engine_Content', $content);

        $front = Zend_Controller_Front::getInstance();
        $front -> registerPlugin(new Ynbusinesspages_Plugin_Shutdown);

        return $content;
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
public function _initynbusinesspages1452228352()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynbusinesspages';            
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
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynbusinesspages', 'YN - Business Pages', '', 'module', '4.01p7', '4.01p7', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.01p7' , `current_version` = '4.01p7' where `name`='ynbusinesspages' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}