<?php

class Ynbusinesspages_Api_Layout {

    public $view;
    
    public function __construct(){
        $this->view= Zend_Registry::get('Zend_View');
    }
    
    /**
     * @var Ynbusinesspages_Model_DbTable_Proxies
     */
    protected $_proxyTable = null;
    /**
     * @var Core_Model_DbTable_Content
     */
    protected $_contentTable = null;

    public function getProxyTable() {
        if ($this->_proxyTable === NULL) {
            $this->_proxyTable = Engine_Api::_()->getDbTable('Proxies', 'Ynbusinesspages');
        }
        return $this->_proxyTable;
    }

    /**
     * @return Ynbusinesspages_Model_DbTable_Pages
     */
    public function getPagesTable() {
        return Engine_Api::_()->getDbTable('Pages', 'Ynbusinesspages');
    }

    /**
     * get content table
     * @return Core_Model_DbTable_Content
     */
    public function getContentTable() {
        if ($this->_contentTable === NULL) {
            $this->_contentTable = Engine_Api::_()->getDbTable('content', 'core');
        }
        return $this->_contentTable;
    }

    /**
     * Genreate page id
     * @return int
     */
    protected function _generatePageId() {
        $proxyTable = $this->getProxyTable();
        $row = $proxyTable->fetchRow($proxyTable->select()->order('page_id desc'));
        $pageId = 0;
        if (is_object($row)) {
            $pageId = $row->page_id + 1;
        } else 
        {
            $pageId = 10000000;
        }
        return $pageId;
    }

    /**
     *  insert a page proxy object from clone page.
     * @params string $page
     * @params number $subject_type
     * @params number $subject_id
     * @params array $structure
     * @return Ynbusinesspages_Model_Proxy
     */
    public function createPageProxy($page, $subject_type, $subject_id, $structure = NULL) {
        $proxyTable = $this->getProxyTable();
        $db = $proxyTable->getAdapter();
        try {
            $db->beginTransaction();

            $item = $proxyTable->fetchNew();
            $item->page_name = $page;
            $item->subject_type = $subject_type;
            $item->subject_id = $subject_id;
            $item->page_id = $this->_generatePageId();
            $item->save();

            $pageTable = $this->getPagesTable();
            $contentTable = $this->getContentTable();

            if ($structure === NULL) {
                $structure = $pageTable->getStructure($page);
            }

            $this->copyRescusiveStructure($item->page_id, NULL, $structure, $contentTable);
            $db->commit();
            return $item;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     *  insert a page proxy object from clone page.
     * @params string $page
     * @params number $subject_type
     * @params number $subject_id
     * @params array $structure
     * @return Ynbusinesspages_Model_Proxy
     */
    public function createFlatenPageProxy($page, $subject_type, $subject_id, $structure) {
        $proxyTable = $this->getProxyTable();
        $db = $proxyTable->getAdapter();
        try {
            $db->beginTransaction();

            $item = $proxyTable->fetchNew();
            $item->page_name = $page;
            $item->subject_type = $subject_type;
            $item->subject_id = $subject_id;
            $item->page_id = $this->_generatePageId();
            $item->save();

            $pageTable = $this->getPagesTable();
            $contentTable = $this->getContentTable();

            if ($structure === NULL) {
                $structure = $pageTable->getStructure($page);
            }

            $page_id = $item->page_id;

            $pids = array();
            $contentTable = $this->getContentTable();

            foreach ($structure as $ele) {
                $id = @$ele['element']['id'];
                $pid = @$ele['parent']['id'];
                $pids[$pid]['c'] = @$pids[$pid]['c'] + 1;
                $order = @$pids[$pid]['c'];
                 if ( $ele['type'] == 'container') {
                            $order = array_search($ele['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
                 }
                        
                $data = array(
                    'page_id' => $page_id,
                    'type' => $ele['type'],
                    'name' => $ele['name'],
                    'parent_content_id' => @$pids[$pid]['id'],
                    'order' => $order,
                    'params' => @Zend_Json::encode(@$ele['params']),
                    'attribs' => @Zend_Json::encode(@$ele['attribs'])
                );
                $pids[$id] = array('id' => $contentTable->insert($data), 'c' => 0);
            }
            $db->commit();
            return $item;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @param    string   $page
     * @param    string   $subject_type
     * @param    mixed    $subject_id
     * @return   Ynbusinesspages_Model_Proxy
     */
    public function getProxyObject($page, $subject_type, $subject_id, $force_create = false) {
        $table = $this->getProxyTable();

        // check proxy object does exists
        $select = $table->select()->where('page_name=?', $page)->where('subject_type=?', $subject_type)->where('subject_id=?', $subject_id);
        $item = $table->fetchRow($select);
        if ($force_create && !is_object($item)) {
            $item = $this->createPageProxy($page, $subject_type, $subject_id);
        }

        return $item;
    }

    public function getProxyObjectById($proxy_id) {
        $table = $this->getProxyTable();

        // check proxy object does exists
        $select = $table->select()->where('proxy_id = ?', $proxy_id);
        $item = $table->fetchRow($select);

        return $item;
    }

    public function resetPageProxy($page_id, $page) 
    {
        $pageTable = $this->getPagesTable();
        $db = $pageTable->getAdapter();
        try {
            $db->beginTransaction();
            $structure = $pageTable->getStructure($page);
            $contentTable = $this->getContentTable();
            $this->copyRescusiveStructure($page_id, NULL, $structure, $contentTable);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    /**
     * @param    string   $page_name
     * @param    string   $subject_type
     * @param    mixed    $subject_id
     * @return   Ynbusinesspages_Model_Proxy
     */
    public function checkPagewidgetExist($page, $array) {
        $table_pagewidgets = Engine_Api::_()->getDbTable('pagewidgets', 'Ynbusinesspages');
        // check widget does exists
        $select = $table_pagewidgets->select()->where('widget_name=?', $array['name'])->where('page_name = ?', $page);
        $item = $table_pagewidgets->fetchRow($select);
        return $item;
    }

    public function getWidget($array) {
        $table = Engine_Api::_()->getDbTable('widgets', 'Ynbusinesspages');

        // check widget does exists
        $select = $table->select()->where('page_name=?', $array['name']);
        $item = $table_pagewidgets->fetchRow($select);
        if (!is_object($item)) {

            $widget = $table->fetchNew();
            $widget->name = $array['name'];
            $widget->title = $array['title'];
            $widget->description = $array['description'];
            $widget->category = $array['category'];
            $widget->save();
            return $widget;
        }

        return $item;
    }

    public function checkDraggable($page, $name) 
    {
        return true;
    }

    public function checkLocked($page_name, $name) 
    {
    	if(in_array($name, array('ynbusinesspages.main-menu')))
		{
			return true;
		}
        return false;
    }

    public function checkProxyObjectExist($page, $subject_type, $subject_id) {
        $table = $this->getProxyTable();

        // check proxy object does exists
        $select = $table->select()->where('page_name=?', $page)->where('subject_type=?', $subject_type)->where('subject_id=?', $subject_id);
        $item = $table->fetchRow($select);
        if (!is_object($item)) {
            return false;
        }
        return true;
    }

    /**
     * @return Ynbusinesspages_Model_Proxy|NULL
     */
    public function getPageProxy($page_name, $subject_type, $subject_id) {
        $proxyTable = $this->getProxyTable();
    }

    /**
     * check if page proxy
     * @return TRUE|FALSE
     */
    public function isValidPageProxy($pageProxy) {
        return false;
    }

    public function copyRescusiveStructure($page_id, $parent_content_id =NULL, $structure, $table) {

        if (!is_array($structure) || count($structure) == 0) {
            return;
        }
        $orderIndex = 0;
        foreach ($structure as $element) {
            if (!is_array($element)) {
                continue;
            }

            $order = @$element['order'];

            if (!$order) {
                $order = ++$orderIndex;
            }

            $data = array(
                'page_id' => $page_id,
                'type' => $element['type'],
                'name' => $element['name'],
                'parent_content_id' => $parent_content_id,
                'order' => $order,
                'params' => Zend_Json::encode($element['params']),
                'attribs' => @$element['attribs'],
            );

            $element_id = $table->insert($data);

            $childElements = array();
            if (isset($element['elements'])&& $element['elements']) {
                $childElements = $element['elements'];
            } elseif (isset($element['element']) && $element['element']) {
                $childElements = $element['element'];
            }

            if ($childElements) {
                $this->copyRescusiveStructure($page_id, $element_id, $childElements, $table);
            }
        }
    }


    public function getContentAreas() {
        $contentAreas = array();

        // From modules
        $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
        foreach ($modules as $module => $path) {
            $contentManifestFile = dirname($path) . '/settings/content.php';
            if (!file_exists($contentManifestFile))
                continue;
            $ret = include $contentManifestFile;
            $contentAreas = array_merge($contentAreas, (array) $ret);
        }

        // From widgets
        $it = new DirectoryIterator(APPLICATION_PATH . '/application/widgets');
        foreach ($it as $dir) {
            if (!$dir->isDir() || $dir->isDot())
                continue;
            $path = $dir->getPathname();
            $contentManifestFile = $path . '/' . 'manifest.php';
            if (!file_exists($contentManifestFile))
                continue;
            $ret = include $contentManifestFile;
            if (!is_array($ret))
                continue;
            array_push($contentAreas, $ret);
        }

        return $contentAreas;
    }

    public function buildCategorizedContentAreas($contentAreas) {
        $categorized = array();
        foreach ($contentAreas as $config) {
            // Check some stuff
            if (!empty($config['requireItemType'])) {
                if (is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType'])) {
                    $config['disabled'] = true;
                } else if (is_array($config['requireItemType'])) {
                    $tmp = array_map(array(Engine_Api::_(), 'hasItemType'), $config['requireItemType']);
                    $config['disabled'] = !(array_sum($tmp) == count($config['requireItemType']));
                }
            }

            // Add to category
            $category = ( isset($config['category']) ? $config['category'] : 'Uncategorized' );
            $categorized[$category][] = $config;
        }

        // Sort categories
        uksort($categorized, array($this, '_sortCategories'));

        // Sort items in categories
        foreach ($categorized as $category => &$items) {
            usort($items, array($this, '_sortCategoryItems'));
        }

        return $categorized;
    }

    //Make function to sort array
    public function makeSortFunction($field) {
        $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
        return create_function('$a,$b', $code);
    }

    public function getMemberLevelName($level_id) {
    	$levelTbl = Engine_Api::_()->getDbTable('levels', 'authorization');
    	$data = $levelTbl->getLevelsAssoc();
    	
    	return $data[$level_id]; 
    }

    /*
     * @Todo: implement this function once have the Page module
     */

    public function clearPageObjects() {
        if ($this->checkModuleExist('page')) {
            
        }
    }

    public function checkModuleExist($module_name) {
        // check module exist
        $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
        $mselect = $modulesTable->select()
                ->where('enabled = ?', 1)
                ->where('name  = ?', $module_name);
        if (count($modulesTable->fetchAll($mselect)) <= 0) {
            return false;
        }
        return true;
    }
}
