<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_StoreBrowseController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if ( !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') ) {
      return $this->setNoRender();
		}

	  $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $this->view->params = $params = $request->getParams();
    $params['view'] = 'map';
	  $params['page'] = $request->getParam('page', 1);
    $params['ipp'] = $this->_getParam('itemCountPerPage', 12);
    $path = $fc->getControllerDirectory('store');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);
    $params['fields']['profile_type'] = (isset($params['category']) && $params['category']) ? $params['category'] : 0;
    if (!$params['fields']['profile_type']) {
      $params['fields'] = '';
    } else {
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

	  $this->view->sort = $params['sort'] = isset($params['sort']) ? $params['sort'] : 'recent';

		$this->view->paginator = $paginator = Engine_Api::_()->getApi('page', 'store')->getPaginator($params);
    $markers = Engine_Api::_()->getApi('gmap', 'page')->getMarkers($paginator->getCurrentItems());
    $this->view->gmap_js = Engine_Api::_()->getApi('gmap', 'page')->getMapJS();
    $this->view->markers = (!empty($markers)) ? Zend_Json_Encoder::encode($markers) : '';
    $bounds = Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers);
    $this->view->bounds  = Zend_Json_Encoder::encode($bounds);
    $this->view->view = (!empty($params['view_mode']) && $params['view_mode'] != 'map') ? $params['view_mode'] : 'list';

    $page_ids = array();
    foreach ($paginator as $page) {
      $page_ids[] = $page->getIdentity();
    }

    $this->view->page_tags = Engine_Api::_()->page()->getPageTags($page_ids);
    $this->getElement()->setTitle('');
	}
}