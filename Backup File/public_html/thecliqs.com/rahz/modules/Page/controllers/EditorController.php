<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: EditorController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_EditorController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $page_id = (int)$this->_getParam('page');
    
    if ($page_id == null){
      $this->_redirectCustom(array('route' => 'page_browse'));
    }
    
    /**
     * @var $page Page_Model_Page
     */
    $this->view->page = $page = Engine_Api::_()->getItem('page', $page_id);

    if ($page == null){
      $this->_redirectCustom(array('route' => 'page_browse'));      
      return ;
    }
    
    if (!$page->isEnabled()){
      $this->_redirectCustom(array('route' => 'page_package_choose', 'page_id'=>$page_id));
      return ;
    }

    if( !$this->_helper->requireUser()->isValid() || !$page->isAdmin() ) {
      $this->_redirectCustom(array('route' => 'page_browse'));
      return ;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$page->isDefaultPackageEnabled() ) {

      if( $page->isOwner($viewer) ) {
        $this->_redirectCustom(array('route' => 'page_package_choose', 'page_id'=>$page_id));
      } else {
        $this->_redirectCustom(array('route' => 'page_browse'));
      }
    }

    if(!$page->isAllowLayout() && $this->getRequest()->getParam('p') != 'switch' )
    {
      $this->_redirectCustom(array('route' => 'page_team', 'action'=>'edit', 'page_id'=>$page_id));
      return ;
    }
  }
  
  public function indexAction()
  {
    $page = $this->_getParam('page', 0);
    $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
    $contentTable = Engine_Api::_()->getDbtable('content', 'page');
    
    $this->view->pageList = $pageList = $pageTable->fetchAll($pageTable->select()->where("user_id = ? AND name <> 'footer' AND name <> 'header'", Engine_Api::_()->user()->getViewer()->getIdentity())->limit(10));
    
    /**
     * @var $pageObject Page_Model_Page
     */
    $this->view->pageObject = $pageObject = $this->view->page;
    $this->owner = $pageObject->owner;

    $this->view->isAllowColsEdit = $pageObject->isAllowCols();

    if (!$pageObject){
      if (isset($pageList[0]->page_id)){
        $this->view->pageObject = $pageObject = Engine_Api::_()->getItem('page', (int)$pageList[0]->page_id);
      }
      if (!$pageObject){
        $this->_redirectCustom(array('route' => 'page_browse'));
        return;
      }
    }
    $istimeline = $pageObject->is_timeline;
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline'))
      $istimeline = false;

    $this->view->page = $pageObject->page_id;
    $this->view->contentAreas = $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas($istimeline));

    $contentByName = array();
    foreach( $contentAreas as $category => $categoryAreas ) {
      foreach( $categoryAreas as $info ) {
        $info['description'] = $this->view->translate($info['description']);
        $contentByName[$info['name']] = $info;
      }
    }

    $this->view->contentByName = $contentByName;

    if ($istimeline && $pageObject->timeline_converted) {
      $select = $contentTable->select()
        ->where('page_id = ?', $pageObject->page_id)
        ->where('is_timeline = ?', true)
        ->order('order ASC');
    } else {
      $select = $contentTable->select()
        ->where('page_id = ?', $pageObject->page_id)
        ->where('is_timeline = ?', false)
        ->order('order ASC');
    }

    $contentRowset = $contentTable->fetchAll($select);
    $contentStructure = $pageTable->prepareContentArea($contentRowset);
    
    $error = false;
    if( $pageObject->name !== 'header' && $pageObject->name !== 'footer' ) {
      foreach( $contentStructure as &$info1 ) {
        if( !in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container' ) {
          $error = true;
          break;
        }
        foreach( $info1['elements'] as &$info2 ) {
          if( !in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container' ) {
            $error = true;
            break;
          }
        }
        // Re order second-level elements
        usort($info1['elements'], array($this, '_reorderContentStructure'));
      }
    }

    if( $error ) {
      throw new Exception('page failed validation check');
    }
    
    $this->view->contentRowset = $contentRowset;
    $this->view->contentStructure = $contentStructure;

    // Timeline Page
    $timeline_enabled = false;
    $timeline_converted = false;
    $default_class = "active";
    $timeline_class = "";
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline')) {
      $timeline_converted = $pageObject->timeline_converted;
      if (!Engine_Api::_()->core()->hasSubject()) {
        Engine_Api::_()->core()->setSubject($pageObject);
      }
      $timeline_enabled = true;
      if ($pageObject->is_timeline) {
        $default_class = "";
        $timeline_class = "active";
      }
    }

    $this->view->is_timeline_enabled = $timeline_enabled;
    $this->view->timeline_converted = $timeline_converted;
    $this->view->default_class = $default_class;
    $this->view->timeline_class = $timeline_class;
  }

  public function updateAction()
  {
    $pageTable = Engine_Api::_()->getDbtable('pages', 'page');
    $contentTable = Engine_Api::_()->getDbtable('content', 'page');
    $db = $pageTable->getAdapter();
    $db->beginTransaction();

    try {

      // Get page
      $page = $this->_getParam('page');
      $pageObject = $this->view->page;
      if( null === $pageObject ) {
        throw new Engine_Exception('Page is missing');
      }

      // Update layout
      if( null !== ($newLayout = $this->_getParam('layout')) ) {
        $pageObject->layout = $newLayout;
        $pageObject->save();
      }

      $istimeline = $pageObject->is_timeline;
      if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline'))
        $istimeline = false;

      // Get registered content areas
      if ($istimeline) {
        $select = $contentTable->select()->where('page_id = ?', $pageObject->page_id)->where('is_timeline = ?', true);
      } else {
        $select = $contentTable->select()->where('page_id = ?', $pageObject->page_id)->where('is_timeline = ?', false);
      }
      $contentRowset = $contentTable->fetchAll($select);

      // Get structure
      $strucure = $this->_getParam('structure');

      // Diff
      $orderIndex = 1;
      $newRowsByTmpId = array();
      $existingRowsByContentId = array();

      foreach( $strucure as $element ) {

        // Get info
        $content_id = @$element['identity'];
        $tmp_content_id = @$element['tmp_identity'];
        $parent_id = @$element['parent_identity'];
        $tmp_parent_id = @$element['parent_tmp_identity'];

        $newOrder = $orderIndex++;

        // Sanity
        if( empty($content_id) && empty($tmp_content_id) ) {
          throw new Exception('content id and tmp content id both empty');
        }
        //if( empty($parent_id) && empty($tmp_parent_id) ) {
        //  throw new Exception('parent content id and tmp parent content id both empty');
        //}

        // Get existing content row (if any)
        $contentRow = null;
        if( !empty($content_id) ) {
          $contentRow = $contentRowset->getRowMatching('content_id', $content_id);
          if( null === $contentRow ) {
            throw new Exception('content row missing');
          }
        }

        // Get existing parent row (if any)
        $parentContentRow = null;
        if( !empty($parent_id) ) {
          $parentContentRow = $contentRowset->getRowMatching('content_id', $parent_id);
        } else if( !empty($tmp_parent_id) ) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        // Existing row
        if( !empty($contentRow) && is_object($contentRow) ) {
          $existingRowsByContentId[$content_id] = $contentRow;

          // Update row
          if( !empty($parentContentRow) ) {
            $contentRow->parent_content_id = $parentContentRow->content_id;
          }

          // Set params
          if( isset($element['params']) && is_array($element['params']) ) {
            $contentRow->params = $element['params'];
          }

          if( $contentRow->type == 'container' ) {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();
        }

        // New row
        else
        {
          if( empty($element['type']) || empty($element['name']) ) {
            throw new Exception('missing name and/or type info');
          }

          if( $element['type'] == 'container' ) {
            $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow = $contentTable->createRow();
          $contentRow->page_id = $pageObject->page_id;
          $contentRow->order = $newOrder;
          $contentRow->type = $element['type'];
          $contentRow->name = $element['name'];

          // Set parent content
          if( !empty($parentContentRow) ) {
            $contentRow->parent_content_id = $parentContentRow->content_id;
          } 
                    
          // Set params
          if( isset($element['params']) && is_array($element['params']) ) {
            $contentRow->params = $element['params'];
          }

          if($istimeline) {
            $contentRow->is_timeline = true;
          }

          $contentRow->save();

          $newRowsByTmpId[$tmp_content_id] = $contentRow;
        }
      }

      // Delete rows that were not present in data sent back
      $deletedRowIds = array();
      foreach( $contentRowset as $contentRow ) {
        if( empty($existingRowsByContentId[$contentRow->content_id]) ) {
          $deletedRowIds[] = $contentRow->content_id;
          $contentRow->delete();
        }
      }
      $this->view->deleted = $deletedRowIds;

      // Send back new content info
      $newData = array();
      foreach( $newRowsByTmpId as $tmp_id => $newRow ) {
        $newData[$tmp_id] = $pageTable->createElementParams($newRow);
      }
      $this->view->newIds = $newData;

      $this->view->status = true;
      $this->view->error = false;

      $db->commit();

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }
  }

  public function widgetAction()
  {
    // Render by widget name
    $mod = $this->_getParam('mod');
    $name = $this->_getParam('name');
    $this->view->pageObject = $this->view->page;
    if( null === $name ) {
      throw new Exception('no widget found with name: ' . $name);
    }
    if( null !== $mod ) {
      $name = $mod . '.' . $name;
    }

    $contentInfoRaw = $this->getContentAreas();
    $contentInfo = array();
    foreach( $contentInfoRaw as $info ) {
      $contentInfo[$info['name']] = $info;
    }

    // It has a form specified in content manifest
    if( !empty($contentInfo[$name]['adminForm']) ) {

      if( is_string($contentInfo[$name]['adminForm']) ) {
        $formClass = $contentInfo[$name]['adminForm'];
        Engine_Loader::loadClass($formClass);
        $this->view->form = $form = new $formClass();
      } else if( is_array($contentInfo[$name]['adminForm']) ) {
        $this->view->form = $form = new Engine_Form($contentInfo[$name]['adminForm']);
      } else {
        throw new Core_Model_Exception('Unable to load admin form class');
      }

      // Try to set title if missing
      if( !$form->getTitle() ) {
        $form->setTitle('Editing: ' . $contentInfo[$name]['title']);
      }

      // Try to set description if missing
      if( !$form->getDescription() ) {
        $form->setDescription('placeholder');
      }

      $form->setAttrib('class', 'global_form_popup' . $form->getAttrib('class'));

      // Add title element
      if( !$form->getElement('title') ) {
        $form->addElement('Text', 'title', array(
          'label' => 'Title',
          'order' => -100,
        ));
      }

// Add mobile element?
      if( !$form->getElement('nomobile') ) {
        $form->addElement('Select', 'nomobile', array(
          'label' => 'Hide on mobile site?',
          'order' => 100000 - 5,
          'multiOptions' => array(
            '1' => 'Yes, do not display on mobile site.',
            '0' => 'No, display on mobile site.',
          ),
          'value' => '0',
        ));
      }

      if( !empty($contentInfo[$name]['isPaginated']) && !$form->getElement('itemCountPerPage') ) {
        $form->addElement('Text', 'itemCountPerPage', array(
          'label' => 'Count',
          'description' => '(number of items to show)',
          'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
          ),
          'order' => 1000000 - 1,
        ));
      }

      // Add submit button
      if( !$form->getElement('submit')  && !$form->getElement('execute') ) {
        $form->addElement('Button', 'execute', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array(
            'ViewHelper',
          ),
          'order' => 1000000,
        ));
      }

      // Add name
      $form->addElement('Hidden', 'name', array(
        'value' => $name,
        'order' => 1000010,
      ));

      if( !$form->getElement('cancel') ) {
        $form->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'onclick' => 'parent.Smoothbox.close();',
          'ignore' => true,
          'decorators' => array(
            'ViewHelper',
          ),
          'order' => 1000001,
        ));
      }

      if( !$form->getDisplayGroup('buttons') ) {
        $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
        $form->addDisplayGroup(array(
          $submitName,
          'cancel',
        ), 'buttons', array(
          'order' => 1000002,
        ));
      }

      // Force method and action
      $form->setMethod('post')
        ->setAction($this->view->url(array()));

      if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $this->view->values = $form->getValues();
        $this->view->form = null;
      }

      return;
    }

    // Try to render admin page
    if( !empty($contentInfo[$name]) ) {
      try {
        $structure = array(
          'type' => 'widget',
          'name' => $name,
          'request' => $this->getRequest(),
          'action' => 'widget',
          'throwExceptions' => true,
        );

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        $content = $element->render();

        $this->getResponse()->setBody($content);

        $this->_helper->viewRenderer->setNoRender(true);
        return;
      } catch( Exception $e ) {

      }
    }

    // Just render default editing form
    $this->view->form = $form = new Engine_Form(array(
      'title' => $contentInfo[$name]['title'],
      'description' => 'placeholder',
      'method' => 'post',
      'action' => $this->view->url(array()),
      'class' => 'global_form_popup',
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Button',
          'submit',
          array(
            'label' => 'Save',
            'type' => 'submit',
            'decorators' => array('ViewHelper'),
            'ignore' => true,
            'order' => 1501,
          )
        ),
        array(
          'Hidden',
          'name',
          array(
            'value' => $name,
          )
        ),
        array(
          'Cancel',
          'cancel',
          array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'order' => 1502,
          )
        )
      ),
      'displaygroups' => array(
        'buttons' => array(
          'name' => 'buttons',
          'elements' => array(
            'submit',
            'cancel',
          ),
          'options' => array(
            'order' => 1500,
          )
        )
      )
    ));

    if( !empty($contentInfo[$name]['isPaginated']) ) {
      $form->addElement('Text', 'itemCountPerPage', array(
        'label' => 'Count',
        'description' => '(number of items to show)',
        'validators' => array(
          array('Int', true),
          array('GreaterThan', true, array(0)),
        )
      ));
    }

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $this->view->values = $form->getValues();
      $this->view->form = null;
    } else {
      $form->populate($this->_getAllParams());
    }
  }
  
  public function getContentAreas( $is_timeline = false )
  {
    $contentAreas = array();
    $ret = array();
    
    $module = Zend_Controller_Front::getInstance()->getControllerDirectory('page');
    $contentManifestFile = dirname($module) . '/settings/widgets.php';
    if( !file_exists($contentManifestFile) ) return array();
    
    $ret = include $contentManifestFile;
    $ret = (array)$ret;

    // Timeline Page
    if ($is_timeline) {
      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline')) {
        $module = Zend_Controller_Front::getInstance()->getControllerDirectory('timeline');
        $contentManifestFile = dirname($module) . '/settings/widgets.php';

        $timeline = include $contentManifestFile;
        $ret = array_merge($ret, (array)$timeline);
      }
    }

    $table = Engine_Api::_()->getDbTable('modules', 'core');
    $db = $table->getAdapter();

    $prefix = $table->getTablePrefix();

    $select = $db->select()->from($prefix.'page_modules', array('name'));
    $modules = $db->fetchCol($select);
	  
    $features = (array) $this->view->pageObject->getAllowedFeatures();
    $tmp = array("pagealbum", "pageblog", "pagediscussion", "pagedocument", "pageevent", "pagemusic", "pagevideo", "rate", "store", "pagecontact", "pagefaq", "donation", "offers");

    foreach ($modules as $module)
    {
      if(in_array($module, $tmp) && !in_array($module, $features))
        continue;
      if ($table->isModuleEnabled($module)) {
        $module = Zend_Controller_Front::getInstance()->getControllerDirectory($module);
        $contentManifestFile = dirname($module) . '/settings/widgets.php';
        $widget = include $contentManifestFile;

        if (is_array($widget) && isset($widget[0]) && is_array($widget[0])) {
          foreach ($widget as $w) {
            $ret[] = $w;
          }
        } else {
          $ret[] = $widget;
        }
      }
    }
    
    $contentAreas = array_merge($contentAreas, $ret);
    
    return $contentAreas;
  }
  
  public function buildCategorizedContentAreas($contentAreas)
  {
    $categorized = array();
    foreach( $contentAreas as $config ) {
      // Check some stuff
      if( !empty($config['requireItemType']) ) {
        if( is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType']) ) {
          $config['disabled'] = true;
        } else if( is_array($config['requireItemType']) ) {
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
    foreach( $categorized as $category => &$items ) {
      usort($items, array($this, '_sortCategoryItems'));
    }

    return $categorized;
  }

  // Timeline Page
  public function typeAction()
  {
    $this->_helper->viewRenderer->setNoRender();
    $this->_helper->getHelper("layout")->disableLayout();
    $result = array('error' => false);

    $page = $this->_getParam('page');
    $page = Engine_Api::_()->getItem('page', $page);
    if (!$page) {
      $result['error'] = true;
      $result['code'] = 2;
    }
    try {
      // not timeline
      if (!$page->is_timeline) {
        // not converted
        if (!$page->timeline_converted) {
          $cov_result = $this->_convertToTimeline($page);
          // conversion success
          if ($cov_result) {
            $page->timeline_converted = $cov_result;
            $page->is_timeline = !$page->is_timeline;
          } else {
            // conversion failed
            $result['error'] = true;
            $result['code'] = 3;
          }
        } else {
          // converted
          $page->is_timeline = !$page->is_timeline;
        }
      } else {
        // is timeline
        $page->is_timeline = !$page->is_timeline;
      }
      $result['error'] = false;
      $page->save();
    } catch (Exception $e) {
      $result['error'] = true;
      $result['code'] = 4;
      throw $e;
    }

    $result = json_encode($result);
    echo $result;
  }

  protected function _convertToTimeline($page = null)
  {
    if (!$page) return false;
    $page_id = $page->getIdentity();
    $content_table = Engine_Api::_()->getDbtable('content', 'page');
    $pages_table = Engine_Api::_()->getDbtable('pages', 'page');

    $main = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'container', 'order' => 2, 'name' => 'main', 'parent_content_id' => 0, 'is_timeline' => 1));
    $middle = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'container', 'order' => 6, 'name' => 'middle', 'parent_content_id' => $main->content_id, 'is_timeline' => 1));

    $timeline_header = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'widget', 'order' => 3, 'name' => 'timeline.page-header', 'parent_content_id' => $middle->content_id, 'is_timeline' => 1));;;
    $timeline_content = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'widget', 'order' => 4, 'name' => 'timeline.page-content', 'parent_content_id' => $middle->content_id, 'is_timeline' => 1));;

    $core_tabs = $pages_table->createContentItem(array('page_id' => $page_id, 'type' => 'widget', 'order' => 5, 'name' => 'core.container-tabs', 'parent_content_id' => $middle->content_id, 'is_timeline' => 1));

    // search for core.container-tabs
    $select = $content_table->select()
      ->where('page_id = ?', $page->page_id)
      ->where('name = ?', 'core.container-tabs')
      ->where('is_timeline = ?', false)
      ->order('order ASC');

    $row = $content_table->fetchRow($select);

    if ($row) {
      $select = $content_table->select()
        ->where('page_id = ?', $page->page_id)
        ->where('parent_content_id = ?', $row->content_id)
        ->order('order ASC');

      $tabs = $content_table->fetchAll($select);
      if ($tabs) {
        foreach ($tabs as $tab) {
          $tab = $tab->toArray();
          $tab['is_timeline'] = 1;
          $tab['parent_content_id'] = $core_tabs->content_id;
          unset($tab['content_id']);

          $tabs_content = $pages_table->createContentItem($tab);
        }
      }
    }
    return true;
  }
// Timeline Page

  protected function _sortCategories($a, $b)
  {
    if( $a == 'Core' ) return -1;
    if( $b == 'Core' ) return 1;
    return strcmp($a, $b);
  }

  protected function _sortCategoryItems($a, $b)
  {
    if( !empty($a['special']) ) return -1;
    if( !empty($b['special']) ) return 1;
    return strcmp($a['title'], $b['title']);
  }
  
  protected function _reorderContentStructure($a, $b)
  {
    $sample = array('left', 'middle', 'right');
    $av = $a['name'];
    $bv = $b['name'];
    $ai = array_search($av, $sample);
    $bi = array_search($bv, $sample);
    if( $ai === false && $bi === false ) return 0;
    if( $ai === false ) return -1;
    if( $bi === false ) return 1;
    $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
    return $r;
  }
  
}