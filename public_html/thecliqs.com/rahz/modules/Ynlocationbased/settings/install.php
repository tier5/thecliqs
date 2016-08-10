<?php

class Ynlocationbased_Installer extends Engine_Package_Installer_Module
{
    public function onInstall()
    {
        $this->_addWidgetToHeader();
        parent::onInstall();
    }

    protected function _addWidgetToHeader()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'header')->limit(1);
        $page_id = $select->query()->fetchObject()->page_id;
        $select = new Zend_Db_Select($db);
        $select -> from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'ynlocationbased.location-search');
        $locationSearch = $select->query()->fetch();
        if (empty($locationSearch)) {
            $select = new Zend_Db_Select($db);
            $select -> from('engine4_core_content')
                -> where('page_id = ?', $page_id)
                -> where('type = ?', 'container')
                -> limit(1);
            $container_id = $select -> query() -> fetchObject() -> content_id;
            $db -> insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynlocationbased.location-search',
                'parent_content_id' => $container_id,
                'order' => 99,
                'params' => '[]'));
        }
    }
}