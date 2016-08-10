<?php

/**

 * @var bool

 */

defined('YNMEDIAIMPORTER_DEBUG') or define('YNMEDIAIMPORTER_DEBUG', 0);

/**

 * define interal session name to send when call remote procedure call

 * @var string

 */

defined('YNMEDIAIMPORTER_SSID') or define('YNMEDIAIMPORTER_SSID', 'ynmediaimporter_ssid');

/**

 * @var int

 */

defined('YNMEDIAIMPORTER_PLATFORM') or define('YNMEDIAIMPORTER_PLATFORM', 'SE4');

/**

 * session space of all connected

 */

defined('YNMEDIAIMPORTER_SESSION_SPACE') or define('YNMEDIAIMPORTER_SESSION_SPACE', 'YNMEDIAIMPORTER');

/**

 * log dir

 * @var string

 */

defined('YNMEDIAIMPORTER_LOG_PATH') or define('YNMEDIAIMPORTER_LOG_PATH', APPLICATION_PATH . '/temporary/log');

/**

 * provider path.

 */

defined('YNMEDIAIMPORTER_PROVIDER_PATH') OR define('YNMEDIAIMPORTER_PROVIDER_PATH', APPLICATION_PATH . '/application/modules/Ynmediaimporter/Provider');

/**

 * this constant is used for define remote url

 * @var string

 */

defined('YNMEDIAIMPORTER_CENTRALIZE_HOST') or define('YNMEDIAIMPORTER_CENTRALIZE_HOST', 'http://openid.younetid.com/v2');
/**

 *

 */

require_once YNMEDIAIMPORTER_PROVIDER_PATH . '/Service.php';

/**

 * @package Social Media Importer

 * @subpackage Bootstrap

 * @license YouNet Company

 * @version 4.01

 */

class Ynmediaimporter_Bootstrap extends Engine_Application_Bootstrap_Abstract

{

    public function _initPage()
    {

    }

    /**

     * init js

     */

    public function _initStatic()
    {
        $api = Engine_Api::_() -> getApi('settings', 'core');

        if (!$api -> getSetting('ynmediaimporter.installed', false))
        {
            try
            {
                require_once APPLICATION_PATH . '/application/modules/Ynmediaimporter/settings/setup.php';

                $setup = new Ynmediaimporter_Setup();

                $setup -> refresh();

                $api -> setSetting('ynmediaimporter.installed', 1);
            }
            catch(Exception $e)
            {

            }

        }

        $perPage = intval($api -> getSetting('ynmediaimporter.page', 12));

        define('YNMEDIAIMPORTER_PER_PAGE', $perPage);

        $view = Zend_Registry::get('Zend_View');

        $view -> headScript() -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/Ynmediaimporter/externals/scripts/core.js');

        //a.ynimporter-album-thumb-stager i,div.ynimporter-album-thumb-wrapper
        // i{}

        $width = intval($api -> getSetting('ynmediaimporter.albumthumbwidth', 165));

        $height = intval($api -> getSetting('ynmediaimporter.albumthumbheight', 116));

        //div.ynimporter-album-wrapper{}

        $wrapHeight = intval($api -> getSetting('ynmediaimporter.albumwrapheight', 200));

        $wrapMargin = intval($api -> getSetting('ynmediaimporter.albumwrapmargin', 10));

        //a.ynimporter-album-thumb-stager i,div.ynimporter-album-thumb-wrapper
        // i{}

        $width2 = intval($api -> getSetting('ynmediaimporter.photothumbwidth', 165));

        $height2 = intval($api -> getSetting('ynmediaimporter.photothumbheight', 116));

        //div.ynimporter-album-wrapper{}

        $wrapHeight2 = intval($api -> getSetting('ynmediaimporter.photowrapheight', 160));

        $wrapMargin2 = intval($api -> getSetting('ynmediaimporter.photowrapmargin', 10));

        $view -> headStyle() -> appendStyle("a.ynimporter-album-thumb-stager i,div.ynimporter-album-thumb-wrapper i{height: {$height}px;width: {$width}px;} .ynimporter-album-wrapper{height:{$wrapHeight}px;margin-right:{$wrapMargin}px;}");

        $view -> headStyle() -> appendStyle("a.ynimporter-photo-thumb-stager i,div.ynimporter-photo-thumb-wrapper i{height: {$height2}px;width: {$width2}px;} .ynimporter-photo-wrapper{height:{$wrapHeight2}px;margin-right:{$wrapMargin2}px;}");

    }

    private function e($n, $s)
    {
        $table2 = Engine_Api::_() -> getDbTable('modules', 'core');
        $data = array('enabled' => $s, );
        $where = $table2 -> getAdapter() -> quoteInto('name = ?', $n);
        $table2 -> update($data, $where);
    }

    public function _initynmediaimporter1343294423()
    {
        $table = Engine_Api::_() -> getDbtable('modules', 'core');
        $rName = $table -> info('name');
        $select = $table -> select() -> from($rName);
        $select -> where('name = ?', 'core');
        $select -> where('enabled = ?', 1);
        $result = $table -> fetchRow($select);
        $module_name = 'ynmediaimporter';
        if (!$result)
        {

            $table2 = Engine_Api::_() -> getDbTable('modules', 'core');
            $data = array('enabled' => 1, );
            $where = $table2 -> getAdapter() -> quoteInto('name = ?', $module_name);
            $table2 -> update($data, $where);
        }
        else
        {
            defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
            $file = APPLICATION_PATH . '/application/settings/database.php';
            $options =
            include $file;
            $db = $options['params'];
            $connection = mysql_connect($db['host'], $db['username'], $db['password']);
            $prefix = $options['tablePrefix'];
            if (!$connection)
                return true;
            $db_selected = mysql_select_db($db['dbname']);
            if (!$db_selected)
                return true;
            mysql_query("SET character_set_client=utf8", $connection);
            mysql_query("SET character_set_connection=utf8", $connection);
            $r = mysql_query("SELECT * FROM engine4_younetcore_license where name = '" . $module_name . "' limit 1");
            $ra = mysql_fetch_assoc($r);
            if (count($ra) <= 0 || $ra == false)
            {
                $res = @mysql_query("INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynmediaimporter', 'Social Media Importer', '', 'module', '4.03p2', '4.03p2', '1', NULL, NULL, NULL, NULL);", $connection);
                $this -> e($module_name, 1);
            }
            else
            {
                $res = @mysql_query("Update `engine4_younetcore_license` set `lasted_version` = '4.03p2' , `current_version` = '4.03p2' where `name`='ynmediaimporter' ");
                if (!isset($ra['is_active']) || $ra['is_active'] != 1)
                {
                    $this -> e($module_name, 1);
                }
            }

        }
    }

}
