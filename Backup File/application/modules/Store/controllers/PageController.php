<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Store_PageController extends Store_Controller_Action_Standard
{
  public function init()
  {
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('index', 'json')->initContext();
  }

  public function indexAction()
  {
    /**
     * @var $viewer User_Model_User
     * @var $page   Page_Model_Page
     * @var $table  Store_Model_DbTable_Products
     */
    $this->view->page_id    = $page_id = $this->_getParam('page_id', 0);
    $this->view->product_id = $product_id = $this->_getParam('product_id', 0);
    $viewer                 = Engine_Api::_()->user()->getViewer();

    if (
      (null == ($page = Engine_Api::_()->getItem('page', $page_id))) ||
      !($page->getStorePrivacy() && $page->isAllowStore() || $page->isStore())
    ) {
      $this->view->status = false;
      return false;
    }

    $table = Engine_Api::_()->getDbTable('products', 'store');

    $params = array(
      'page_id'    => $page_id,
      'ipp'        => 12,
      'p'          => $this->_getParam('p', 1),
      'order'      => 'DESC',
      'owner'      => $page->getStorePrivacy(),
      'product_id' => $product_id
    );

    $this->view->products = $table->getProducts($params);

    $this->view->html = $this->view->render('_page_list.tpl');
  }
}