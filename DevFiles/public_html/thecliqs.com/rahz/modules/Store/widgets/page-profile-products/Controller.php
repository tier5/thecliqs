<?php
/**
 * SocialEngine
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

class Store_Widget_PageProfileProductsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $this->view->page = $page = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!$page->approved) {
      return $this->setNoRender();
    }

    if (!($page instanceof Page_Model_Page)) {
      return $this->setNoRender();
    }

    if (!$page->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    if (!($page->isStore() || $page->isOwner($viewer)) || !$page->isAllowStore()) {
      return $this->setNoRender();
    }

    $this->view->isGatewayEnabled = (Engine_Api::_()->getDbTable('apis', 'store')->getEnabledGatewayCount($page->getIdentity()) <= 0 && !Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit')) ? false : true;

    $page_path = Zend_Controller_Front::getInstance()->getControllerDirectory('page');
    $this->view->addScriptPath(dirname($page_path) . '/views/scripts');

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('store');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $params = array(
      'page_id' => $page->getIdentity(),
      'ipp' => $this->_getParam('itemCountPerPage', 12),
      'p' => $this->_getParam('p', 1),
      'order' => 'DESC',
      'owner' => $page->getStorePrivacy(),
      'quantity' => true
    );

    $paginator = $this->getTable()->getProducts($params);

    $this->view->products = $paginator;

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  /**
   * @return Store_Model_DbTable_Products
   */
  public function getTable()
  {
    return Engine_Api::_()->getDbTable('products', 'store');
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}