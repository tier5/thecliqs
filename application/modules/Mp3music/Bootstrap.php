<?php class Mp3music_Bootstrap extends Engine_Application_Bootstrap_Abstract{
	public function __construct($application) {
		parent::__construct($application);
		$this->initViewHelperPath();
	}
	
	/**
	 * init js
	 */
	public function _initJs() {
		$view = Zend_Registry::get('Zend_View');
		$view->headScript()
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/m2bmusic_class.js')
		->appendFile($view->baseUrl().'/externals/smoothbox/smoothbox4.js')
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/music_function.js')
		
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/jquery.js')
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/noconflict.js')
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/jquery-ui.js')
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/slimScroll.js')
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/mediaelement-and-player.min.js')
		->appendFile($view->baseUrl().'/application/modules/Mp3music/externals/scripts/mp3music.js');
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
public function _initmp3music1363167083()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'mp3music';            
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
        $res = @mysql_query("INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('mp3music', 'Mp3 Music', 'This is module Mp3 Music.', 'module', '4.04p4', '4.04p4', '1', NULL, NULL, NULL, NULL);",$connection);
        $this->e($module_name,1);
    }
    else
    {
        $res = @mysql_query("Update `engine4_younetcore_license` set `lasted_version` = '4.04p4' , `current_version` = '4.04p4' where `name`='mp3music' ");
        if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}
