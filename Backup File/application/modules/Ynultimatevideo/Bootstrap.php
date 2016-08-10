<?php defined('GOOGLE_LIBS_PATH') or define('GOOGLE_LIBS_PATH', APPLICATION_PATH . '/application/modules/Ynultimatevideo/Api/Google');
    class Ynultimatevideo_Bootstrap extends Engine_Application_Bootstrap_Abstract
    {
        public function __construct($application)
        {
            parent::__construct($application);
            $this->initViewHelperPath();

            $headScript = new Zend_View_Helper_HeadScript();
            $headLink = new Zend_View_Helper_HeadLink();

            $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynultimatevideo/externals/scripts/jquery-1.10.2.min.js');
            $headScript->appendScript('jQuery.noConflict()');

            $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynultimatevideo/externals/scripts/video-actions.js');
            $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynultimatevideo/externals/scripts/video.js');
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $view = $viewRenderer->view;
            $view->headScript()->appendScript($view->partial('_types.tpl', 'ynultimatevideo', array()));
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
public function _initynultimatevideo1460081951()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynultimatevideo';            
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
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynultimatevideo', 'YN - Ultimate Video', '', 'module', '4.02', '4.02', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.02' , `current_version` = '4.02' where `name`='ynultimatevideo' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}