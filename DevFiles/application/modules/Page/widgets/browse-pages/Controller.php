<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 04.11.11 15:50 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_BrowsePagesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /**
     * @var $table Page_Model_DbTable_Pages
     */
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $params = $request->getParams();

    if (isset($params['sort_type']) && $params['sort_type'] != '') {
      if (isset($params['sort_value']) && $params['sort_value'] != '') {
        switch ($params['sort_type']) {
          case 'tag':
            $sort_type = 'tag_name';
            break;
          case 'location':
            $sort_type = 'city';
            break;
          case 'category_set':
            $sort_type = 'setId';
            $dba = Engine_Db_Table::getDefaultAdapter();
            $prefix = $table->getTablePrefix();
            $params['sort_value'] = $dba->fetchOne("SELECT id FROM {$prefix}page_category_set WHERE `caption` = ?", array($params['sort_value']) );

            break;
          case 'sort':
            $sort_type = 'sort';
            break;
          case 'abc':
            $sort_type = 'abc';
            break;
          default:
            $sort_type = 'category_name';
            break;
        }
        $params[$sort_type] = $params['sort_value'];
        unset($params['sort_type']);
        unset($params['sort_value']);
      }
    }

    if(isset($params['keyword']) && $params['keyword'] == 'Search')
      unset($params['keyword']);
    if(isset($params['profile_type'])){
        $params['category'] = $params['profile_type'];
        unset($params['profile_type']);
        unset($params['submit']);
    }

    $params['view'] = 'map';
    $params['search'] = 1;
    $params['approved'] = 1;
    $params['page'] = $request->getParam('page', 1);
    $params['ipp'] = $settings->getSetting('page.browse_count', 5);
    $params['fields']['profile_type'] = (isset($params['category']) && $params['category']) ? $params['category'] : 0;
    $this->view->sort = $params['sort'] = (isset($params['sort'])) ? $params['sort'] : 'recent';

    if (!$params['fields']['profile_type']) {
      $params['fields'] = '';
    }
    else {
      // Process options
      $tmp = array();
      foreach( $params as $k => $v ) {
        if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
          continue;
        } else if( false !== strpos($k, '_field_') ) {
          list($null, $field) = explode('_field_', $k);
          $tmp['field_' . $field] = $v;
        } else if( false !== strpos($k, '_alias_') ) {
          list($null, $alias) = explode('_alias_', $k);
          $tmp[$alias] = $v;
        }
      }
      $params['fields'] = array_merge($params['fields'], $tmp);
    }

    $this->view->paginator = $paginator = $table->getPaginator($params);

    $isOffersModuleEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('offers');
    $isPageSortOffersEnabled = $settings->getSetting('page.sort.active.offers', 1);
    if ($isOffersModuleEnabled && $isPageSortOffersEnabled) {
      $offersTbl = Engine_Api::_()->_()->getDbTable('offers', 'offers');
      $pagesIdsActiveOffers = $offersTbl->getPagesIdsActiveOffers();
      $this->view->pagesIdsActiveOffers = $pagesIdsActiveOffers;
    }
    else {
      $this->view->pagesIdsActiveOffers = array();
    }

    $this->view->count = $paginator->getTotalItemCount();
    $markers = Engine_Api::_()->getApi('gmap', 'page')->getMarkers($paginator->getCurrentItems());
    $bounds = Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers);
    $this->view->field_ids = Zend_Json_Encoder::encode(Engine_Api::_()->page()->getCategorizedFieldIds());

    $page_ids = array();
    foreach ($paginator as $page) {
      $page_ids[] = $page->getIdentity();
    }

    $path = $fc->getControllerDirectory('page');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);
    $this->view->page_tags = Engine_Api::_()->page()->getPageTags($page_ids);
    $this->view->page_likes = Engine_Api::_()->like()->getLikesCount('page', $page_ids);

    $browse_mode = $settings->getSetting('page.browse.mode', 'list');
    $browse_mode = ($request->getParam('view_mode')) ? $request->getParam('view_mode') : $browse_mode;
    $this->view->view = ($browse_mode) ? $browse_mode : 'list' ;

    $this->view->gmap_js = Engine_Api::_()->getApi('gmap', 'page')->getMapJS();
	  $this->view->markers = (!empty($markers)) ? Zend_Json_Encoder::encode($markers) : '';
	  $this->view->bounds  = Zend_Json_Encoder::encode($bounds);

    if( empty($params['category_name']) ) $params['category_name'] = '';
    if( empty($params['tag_name']) ) $params['tag_name'] = '';
    if( empty($params['city']) ) $params['city'] = '';

    $this->view->params = $params;
  }
}