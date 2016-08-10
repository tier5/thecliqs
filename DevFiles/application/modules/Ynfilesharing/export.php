<?php

class Ynexport_Package_Exporter
{

    protected $_db = null;

    public function getDb()
    {
        if ($this -> _db == null)
        {
            $this -> _db = Engine_Db_Table::getDefaultAdapter();
        }
        return $this -> _db;
    }

    public function __construct()
    {

    }

    /**
     * export structure to a file.
     */
    public function exportStructure($filename = null)
    {

        if ($filename == null)
        {
            $filename = dirname(__FILE__) . '/settings/structure.php';
        }

        $module = $_REQUEST['module'];
        $structure = array();
        $structure['menus'] = $this -> _exportMenus($module);
        $structure['menuitems'] = $this -> _exportMenuItems($module);
        $structure['mails'] = $this -> _exportMails($module);
        $structure['jobtypes'] = $this -> _exportJobTypes($module);
        $structure['notificationtypes'] = $this -> _exportNotificationTypes($module);
        $structure['actiontypes'] = $this -> _exportActionTypes($module);
        $structure['permissions'] = $this -> _exportPermissions($module);
        $structure['pages'] = $this -> _exportLandingPage($module);
        $this -> _writeFile($structure, $filename);
        $this -> _updateManifest($module);
        return $structure;
    }

    protected function _updateManifest($module)
    {
        $file = dirname(__FILE__) . '/settings/manifest.php';
        $manifest =
        include $file;
        $dir = Engine_Api::inflect($module);
        $manifest['package']['callback'] = array(
            'path' => 'application/modules/' . $dir . '/settings/install.php',
            'class' => $dir . '_Package_Installer'
        );

        $this -> _writeFile($manifest, $file);
    }

    /**
     * export menu
     * @param string $module
     * @return array list of rows
     */
    protected function _exportMenus($module)
    {
        $db = $this -> getDb();
        $sql = "select * from engine4_core_menus  where name like '{$module}_%'";
        return (array)$this -> getDb() -> fetchAll($sql);
    }

    protected function _exportMenuItems($module)
    {
        $db = $this -> getDb();
        $sql = "select * from engine4_core_menuitems  where module = '{$module}'";
        return (array)$this -> getDb() -> fetchAll($sql);
    }

    protected function _exportActionTypes($module)
    {
        $db = $this -> getDb();
        $sql = "select * from engine4_activity_actiontypes  where module = '{$module}'";
        return (array)$this -> getDb() -> fetchAll($sql);
    }

    protected function _exportMails($module)
    {
        $db = $this -> getDb();
        $sql = "select * from engine4_core_mailtemplates  where module = '{$module}'";
        return (array)$this -> getDb() -> fetchAll($sql);
    }

    protected function _exportJobTypes($module)
    {
        $db = $this -> getDb();
        $sql = "select * from engine4_core_jobtypes  where module = '{$module}'";
        return (array)$this -> getDb() -> fetchAll($sql);
    }

    /**
     * fetch permission
     * @return array
     */
    protected function _exportPermissions($module)
    {
        $result = array();
        $scans = array(
            '2' => 'admin',
            '3' => 'moderator',
            '4' => 'user',
            '5' => 'public'
        );
        $type = array();

        $file = dirname(__FILE__) . '/settings/manifest.php';
        $manifest =
        include $file;
        $items = isset($manifest['items']) ? $manifest['items'] : array();

        if (empty($items))
        {
            return array();
        }
        else
        {
            $items = "'" . implode("','", $items) . "'";
        }

        $db = $this -> getDb();
        foreach ($scans as $level_id => $level_type)
        {
            $sql = "select * from engine4_authorization_permissions where level_id='{$level_id}' and type IN ($items)";
            $rows = (array)$db -> fetchAll($sql);
            if (empty($rows))
            {
                continue;
            }
            else
            {
                foreach ($rows as $row)
                {
                    $result[] = array(
                        $level_type,
                        $row['type'],
                        $row['name'],
                        $row['value'],
                        $row['params']
                    );
                }
            }
        }
        return $result;

    }

    protected function _exportNotificationTypes($module)
    {
        $db = $this -> getDb();
        $sql = "select * from engine4_activity_notificationtypes  where module = '{$module}'";
        return (array)$this -> getDb() -> fetchAll($sql);
    }

    protected function _exportLandingPage($module)
    {

        $pageStructure = array();

        // get all page in current module.
        $sql = "select * from engine4_core_pages where name like '{$module}_%'";

        $pages = $this -> getDb() -> fetchAll($sql);

        $contents = array();
        foreach ($pages as $index => $page)
        {
            $page_id = (int)$page['page_id'];
            $sql = "select * from engine4_core_content where page_id = {$page_id} order by `parent_content_id`,`order` asc;";

            $rows = $this -> getDb() -> fetchAll($sql);

            $pages[$index]['ynchildren'] = $this -> _rebuildContentPage($page_id);
        }

        foreach ($pages as $page)
        {
            $pageStructure[$page['name']] = $page;
        }

        return $pageStructure;
    }

    protected function _writeFile($data, $filename)
    {
        $fp = @fopen($filename, 'w');
        if (!$fp)
        {
            throw new Exception("$filename is not writeable!");
        }

        fwrite($fp, '<?php defined("_ENGINE") or die("access denied"); return ' . var_export($data, true) . ';?>');
    }

    /**
     * rebuild content structure
     */
    protected function _rebuildContentPage($page_id, $parent_content_id = null)
    {

        if ($parent_content_id == null)
        {
            $sql = "select * from engine4_core_content where page_id = {$page_id} and parent_content_id is NULL order by `order` asc;";
        }
        else
        {
            $sql = "select * from engine4_core_content where page_id = {$page_id} and parent_content_id = {$parent_content_id} order by `order` asc;";
        }
        $rows = $this -> getDb() -> fetchAll($sql);
        foreach ($rows as $index => $row)
        {
            $rows[$index]['ynchildren'] = $this -> _rebuildContentPage($page_id, (int)$row['content_id']);
        }

        return $rows;
    }

}

$api = new Ynexport_Package_Exporter();

$structure = $api -> exportStructure();
var_dump($structure);

//
// require_once dirname(__FILE__) . '/settings/install.php';
//
// $install = new Younetco_Package_Installer();
//
// $install -> onInstall();
