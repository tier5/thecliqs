<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminStoresController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminStoresController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_stores');

    $this->view->isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    if (!$this->view->isPageEnabled) {
      return ;
    }
  }

  public function indexAction()
  {
    /**
     * @var $table Page_Model_DbTable_Pages
     * @var $api Store_Api_Page
     */

    $table = Engine_Api::_()->getDbTable('pages', 'page');
  	$select = $table->select();

    $prefix = $table->getTablePrefix();

  	$select
  		->setIntegrityCheck(false)
  		->from($prefix.'page_pages')
  		->joinLeft($prefix.'page_fields_values', $prefix."page_fields_values.item_id = ".$prefix."page_pages.page_id")
  		->joinLeft($prefix.'page_fields_options', $prefix."page_fields_options.option_id = ".$prefix."page_fields_values.value AND ".$prefix."page_fields_options.field_id = 1", array("category" => $prefix."page_fields_options.label"));

    $api = Engine_Api::_()->getApi('page', 'store');

    $select = $api->setStoreIntegrity($select, false);

    $this->view->filterForm = $filterForm = new Page_Form_Admin_Manage_Filter();
    $filterForm->removeElement('package');
    $filterForm->removeElement('approved');
   	$page = $this->_getParam('page',1);

     $values = array();
     if( $filterForm->isValid($this->_getAllParams()) ) {
       $values = $filterForm->getValues();
     }

   	foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'page_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $select->order(( !empty($values['order']) ? $values['order'] : 'page_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

   	if ( !empty($values['title']) ) {
      $select->where($prefix.'page_pages.title LIKE ?', '%' . $values['title'] . '%');
    }

   	if( !empty($values['category']) && $values['category'] != -1) {
      $select
        ->where($prefix.'page_fields_values.field_id = 1 AND '.$prefix.'page_fields_values.value = ?', $values['category'] );
    } elseif (isset($values['category']) && $values['category'] == -1) {
     	$select
      	->where($prefix.'page_fields_options.label IS NULL');
    }

    if( isset($values['approved']) && $values['approved'] != -1 ) {
      $select->where($prefix.'page_pages.approved = ?', $values['approved'] );
    }

   	if( isset($values['featured']) && $values['featured'] != -1 ) {
      $select->where($prefix.'page_pages.featured = ?', $values['featured'] );
    }

   	$select->where($prefix."page_pages.name <> 'footer' AND ".$prefix."page_pages.name <> 'header' AND ".$prefix."page_pages.name <> 'default'");
   	$select->group($prefix."page_pages.page_id");

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(40);
    $this->view->formValues = array_filter($values);

    /**
     * @var $productsTbl Store_Model_DbTable_Products
     * @var $api Store_Api_Page
     */

    $productsTbl = Engine_Api::_()->getDbTable('products', 'store');
    $api = Engine_Api::_()->getApi('page', 'store');

    // Preload info
    $products = array();
    $balances = array();
    foreach ($paginator as $store) {
      $products[$store->page_id] = $productsTbl->getProducts(array('count' => 1, 'page_id' => $store->page_id));
      $balances[$store->page_id] = $api->getBalance($store->page_id);
    }

    $this->view->products = $products;
    $this->view->balances = $balances;
  }

 	public function featureAction()
  {
   	$page_id = (int)$this->_getParam('page_id');
   	$value = $this->_getParam('value');

   	if ($page_id && !_ENGINE_ADMIN_NEUTER) {
       /**
        * @var $page Page_Model_Page
        */
   		$page = Engine_Api::_()->getItem('page', $page_id);
      $page->featuredStatus($value);
    }

    $this->redirect();
  }

 	public function sponsorAction()
  {
   	$page_id = (int)$this->_getParam('page_id');
    $value = $this->_getParam('value');

   	if ($page_id && !_ENGINE_ADMIN_NEUTER) {
       /**
        * @var $page Page_Model_Page
        */
   		$page = Engine_Api::_()->getItem('page', $page_id);
      $page->sponsoredStatus($value);
    }

    $this->redirect();
  }

  private function redirect()
  {
    $this->_redirectCustom(
      $this->view->url(
        array(
          'module' => 'store',
          'controller' => 'stores',
          'action' => 'index'
        ), 'admin_default', true
      )
    );
  }
}