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

class Store_Widget_StorePopularStoresController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		if ( !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') ) {
      return $this->setNoRender();
		}

		/**
		 * @var $table Page_Model_DbTable_Pages
		 * @var $api Store_Api_Page
		 */
    $table = Engine_Api::_()->getDbTable('pages', 'page');
		$api = Engine_Api::_()->getApi('page', 'store');

    $ipp = $this->_getParam('itemCountPerPage', 6);

    $this->view->showTitle = false;
    if ($this->getElement()->getTitle() == null) {
      $this->view->showTitle = true;
    }

    $params = array('approved' => 1, 'sort' => 'popular', 'ipp' => $ipp, 'p' => 1);
		$select = $table->getSelect($params);
		$select = $api->setStoreIntegrity($select);

		$paginator = Zend_Paginator::factory($select);

		if (!empty($params['ipp'])) {
			$params['ipp'] = (int)$params['ipp'];
			$paginator->setItemCountPerPage($params['ipp']);
		}

		if (!empty($params['page'])) {
			$params['page'] = (int)$params['page'];
			$paginator->setCurrentPageNumber($params['page']);
		}

		$this->view->pages = $paginator;

    if (!$paginator->getTotalItemCount()){
      return $this->setNoRender();
    }
  }
}