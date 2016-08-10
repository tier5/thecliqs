<?php

class Ynmediaimporter_Setup
{

    public function refresh()
    {
        $this -> _ynBuildStructure();
    }

    public function getDb()
    {
        return Zend_Db_Table::getDefaultAdapter();
    }

    /**
     * rebuild structure from structure file
     * structure file is builded from rip export
     * @return void
     */
    protected function _ynBuildStructure()
    {
        $filename = dirname(__FILE__) . '/structure.php';
        $structure =
        include $filename;

        if (isset($structure['module']) && !empty($structure['module']))
        {
            $this -> _ynBuildModule($structure['module']);
        }

        if (isset($structure['pages']) && !empty($structure['pages']))
        {
            $this -> _ynBuildPages($structure['pages']);
        }

        if (isset($structure['menus']) && !empty($structure['menus']))
        {
            $this -> _ynBuildMenus($structure['menus']);
        }

        if (isset($structure['menuitems']) && !empty($structure['menuitems']))
        {
            $this -> _ynBuildMenuItems($structure['menuitems']);
        }

        if (isset($structure['mails']) && !empty($structure['mails']))
        {
            $this -> _ynBuildMails($structure['mails']);
        }

        if (isset($structure['jobtypes']) && !empty($structure['jobtypes']))
        {
            $this -> _ynBuildJobTypes($structure['jobtypes']);
        }

        if (isset($structure['actiontypes']) && !empty($structure['actiontypes']))
        {
            $this -> _ynBuildActionTypes($structure['actiontypes']);
        }

        if (isset($structure['notificationtypes']) && !empty($structure['notificationtypes']))
        {
            $this -> _ynBuildNotificationTypes($structure['notificationtypes']);
        }

        if (isset($structure['permissions']) && !empty($structure['permissions']))
        {
            $this -> _ynBuildPermission($structure['permissions']);
        }

    }

    /**
     * update package information from this page, we are welcome all experted
     * information.
     */
    protected function _ynBuildModule($row)
    {
        $name = $row['name'];
        $db = $this -> getDb();

        if ($db -> fetchOne("select count(*) from engine4_core_modules where name='{$name}'"))
        {
            unset($row['name']);
            $db -> update('engine4_core_modules', $row, "name='{$name}'");
        }
        else
        {
            $db -> insert('engine4_core_modules', $row);
        }
    }

    /**
     * rebuild menu
     */
    protected function _ynBuildMenus($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_menus where name='" . $row['name'] . "'"))
            {
                unset($row['id']);
                $db -> insert('engine4_core_menus', $row);
            }
        }
    }

    /**
     * rebuild menu items
     */
    protected function _ynBuildMenuItems($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_menuitems where name='" . $row['name'] . "'"))
            {
                unset($row['id']);
                $db -> insert('engine4_core_menuitems', $row);
            }
        }

    }

    /**
     * rebuild mail
     */
    protected function _ynBuildMails($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_mailtemplates where type='" . $row['type'] . "'"))
            {
                unset($row['mailtemplate_id']);
                $db -> insert('engine4_core_mailtemplates', $row);
            }
        }
    }

    /**
     * rebuild mail
     */
    protected function _ynBuildJobTypes($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_core_jobtypes where type='" . $row['type'] . "'"))
            {
                unset($row['jobtype_id']);
                $db -> insert('engine4_core_jobtypes', $row);
            }
        }
    }

    /**
     * rebuild mail
     */
    protected function _ynBuildNotificationTypes($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_activity_notificationtypes where type='" . $row['type'] . "'"))
            {
                $db -> insert('engine4_activity_notificationtypes', $row);
            }
        }
    }

    /**
     * rebuild mail
     */
    protected function _ynBuildActionTypes($rows)
    {
        $db = $this -> getDb();
        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            if (!$db -> fetchOne("select count(*) from engine4_activity_actiontypes where type='" . $row['type'] . "'"))
            {
                $db -> insert('engine4_activity_actiontypes', $row);
            }
        }
    }

    protected function _ynBuildPermission($rows)
    {
        $db = $this -> getDb();

        foreach ($rows as $row)
        {
            if (empty($row))
            {
                continue;
            }
            list($level, $type, $name, $value, $params) = $row;

            if ($value === NULL)
            {
                $value = 'NULL';
            }

            if ($params == NULL)
            {
                $params = 'NULL';
            }
            else
            {
                $params = $db -> quote($params);
            }

            $sql = "INSERT IGNORE INTO `engine4_authorization_permissions`
                      SELECT
                        level_id as `level_id`,
                        '{$type}' as `type`,
                        '{$name}' as `name`,
                        '$value' as `value`,
                        $params as `params`
                      FROM `engine4_authorization_levels` WHERE `type` IN('$level');
                ";
            $db -> query($sql);
        }

    }

    /**
     * rebuidl pages
     */
    protected function _ynBuildPages($pageStructure)
    {
        $db = $this -> getDb();

        foreach ($pageStructure as $name => $page)
        {
            // check page
            $page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', $name) -> limit(1) -> query() -> fetchColumn();
            if ($page_id)
            {
                continue;
            }
            else
            {
                $this -> _ynAddOnePage($page);
            }
        }

    }

    protected function _ynAddOnePage($page)
    {
        $db = $this -> getDb();
        // Insert page
        $db -> insert('engine4_core_pages', array(
            'name' => $page['name'],
            'displayname' => $page['displayname'],
            //'url' => $page['url'],
            'title' => $page['title'],
            'description' => $page['description'],
            'keywords' => $page['keywords'],
            'custom' => $page['custom'],
            'fragment' => $page['fragment'],
            'layout' => $page['layout'],
            //'levels' => $page['levels'],
            'provides' => $page['provides']
        ));

        $page_id = $db -> lastInsertId();

        if (!$page_id)
        {
            return false;
        }

        if (isset($page['ynchildren']) && !empty($page['ynchildren']))
        {
            $this -> _ynAddPageContent($page_id, null, $page['ynchildren']);
        }
        return true;
    }

    protected function _ynAddPageContent($page_id, $parent_content_id = null, $contents)
    {
        $db = $this -> getDb();
        foreach ($contents as $content)
        {
            if (empty($content))
            {
                continue;
            }
            $db -> insert('engine4_core_content', array(
                'page_id' => $page_id,
                'parent_content_id' => $parent_content_id,
                'type' => $content['type'],
                'name' => $content['name'],
                'order' => $content['order'],
                'params' => $content['params'],
                'attribs' => $content['attribs']
            ));

            $pid = $db -> lastInsertId();

            if (!$pid)
            {
                throw new Engine_Package_Installer_Exception("can not insert to page content!");
            }

            /**
             * recursiver insert to content
             */
            if (isset($content['ynchildren']) && !empty($content['ynchildren']))
            {
                $this -> _ynAddPageContent($page_id, $pid, $content['ynchildren']);
            }
        }
    }

}
