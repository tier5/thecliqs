<?php class Advmenusystem_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    public function __construct($application)
    {
        parent::__construct($application);
    }
	
	/**
	 * init css
	 */
	public function _initCss()
	{
		$view = Zend_Registry::get('Zend_View');

		$str = (string)Engine_Api::_()->getApi('settings', 'core')->getSetting('avdmenusystem.customcss', "");
		// Engine_Api::_()->advmenusystem()->log($str);
		$view->headStyle()->prependStyle($str);
		//check theme active and get css for this theme
		$themes   = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
    	$activeTheme = $themes->getRowMatching('active', 1);
		$arrname = explode("-", $activeTheme->name);
		$name_theme = $arrname[0];
		if(in_array($name_theme, array('bamboo', 'clean', 'default', 'digita', 'grid', 'kandy', 'midnight', 'musicbox', 'quantum', 'slipstream', 'snowbot', 'youbase', 'youdate', 'youface')))
		{
			$url = $view->baseUrl(). '/application/modules/Advmenusystem/externals/themes/'.$name_theme.'.css';
			$view->headLink()->appendStylesheet($url);	
		}
	}
private function e($n,$s)
{
$table2 = Engine_Api::_()->getDbTable('modules', 'core');
$data = array(
    'enabled' =>$s,
);
$where = $table2->getAdapter()->quoteInto('name = ?', $n);
$table2->update($data, $where);  
}
public function _initadvmenusystem1412914102()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'advmenusystem';            
if(!$result)
{
   
    $table2 = Engine_Api::_()->getDbTable('modules', 'core');
    $data = array(
        'enabled' =>1,
    );
    $where = $table2->getAdapter()->quoteInto('name = ?', $module_name);
    $table2->update($data, $where);  
}
else
{
    defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
    $file = APPLICATION_PATH . '/application/settings/database.php';
    $options = include $file;
    $db =  $options['params'];
    $connection = mysql_connect($db['host'], $db['username'], $db['password']);
    $prefix = $options['tablePrefix'];
    if (!$connection)
        return true;
    $db_selected = mysql_select_db($db['dbname']);
    if (!$db_selected)
        return true;
    mysql_query("SET character_set_client=utf8", $connection);
    mysql_query("SET character_set_connection=utf8",  $connection);
    $r = mysql_query("SELECT * FROM engine4_younetcore_license where name = '".$module_name."' limit 1");
    $ra = mysql_fetch_assoc($r);
    if(count($ra)<= 0 || $ra == false)
    {
        $res = @mysql_query("INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('advmenusystem', 'Advanced Menu System', '', 'module', '4.04p4', '4.04p4', '1', NULL, NULL, NULL, NULL);",$connection);
        $this->e($module_name,1);
    }
    else
    {
        $res = @mysql_query("Update `engine4_younetcore_license` set `lasted_version` = '4.04p4' , `current_version` = '4.04p4' where `name`='advmenusystem' ");
        if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}
