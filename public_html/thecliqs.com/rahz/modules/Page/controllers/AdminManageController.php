<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminManageController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_AdminManageController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_manage');
  }

  public function indexAction()
  {
    /**
     * @var $table Page_Model_DbTable_Pages
     */
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $table->select();

    $prefix = $table->getTablePrefix();

    $select
      ->setIntegrityCheck(false)
      ->from($prefix.'page_pages')
      ->joinLeft($prefix.'page_fields_values', $prefix."page_fields_values.item_id = ".$prefix."page_pages.page_id")
      ->joinLeft($prefix.'page_fields_options', $prefix."page_fields_options.option_id = ".$prefix."page_fields_values.value AND ".$prefix."page_fields_options.field_id = 1", array("category" => $prefix."page_fields_options.label"))
      ->joinLeft($prefix.'page_packages', $prefix.'page_packages.package_id = ' . $prefix."page_pages.package_id", array('package' => $prefix.'page_packages.title'));

    $this->view->filterForm = $filterForm = new Page_Form_Admin_Manage_Filter();
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
      'order_direction' => 'ASC',
    ), $values);

    $this->view->assign($values);

    $select->order(( !empty($values['order']) ? $values['order'] : 'page_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'ASC' ));

    if( !empty($values['title']) ){
      $select->where($prefix.'page_pages.title LIKE ?', '%' . $values['title'] . '%');
    }

    if( !empty($values['category']) && $values['category'] != -1){
      $select
        ->where($prefix.'page_fields_values.field_id = 1 AND '.$prefix.'page_fields_values.value = ?', $values['category'] );
    } elseif (isset($values['category']) && $values['category'] == -1) {
      $select
        ->where($prefix.'page_fields_options.label IS NULL');
    }

    if( isset($values['approved']) && $values['approved'] != -1 ){
      $select->where($prefix.'page_pages.approved = ?', $values['approved'] );
    }

    if( isset($values['featured']) && $values['featured'] != -1 ){
      $select->where($prefix.'page_pages.featured = ?', $values['featured'] );
    }

    if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.package.enabled', 0)) {
      if( isset($values['package']) && $values['package'] != -1 ){
        $select->where($prefix.'page_pages.package_id = ?', $values['package'] );
      }
    }

    if( !empty($values['owner']) ) {
      $select->joinLeft(array('user' => $prefix.'users'), 'user.user_id = '.$prefix.'page_pages.user_id', array('displayname'))
        ->where('user.displayname LIKE ?', '%'.$values['owner'].'%');
    }

    $select->where($prefix."page_pages.name <> 'footer' AND ".$prefix."page_pages.name <> 'header' AND ".$prefix."page_pages.name <> 'default'");
    $select->group($prefix."page_pages.page_id");

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    if( !empty($values['ipp']))
      $paginator->setItemCountPerPage($values['ipp']);
    else
      $paginator->setItemCountPerPage(20);

    $this->view->formValues = array_filter($values);
    $this->view->isPackageEnabled = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.package.enabled', 0);

  }

  public function approveAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $page_id = (int)$this->_getParam('page_id');
    $value = $this->_getParam('value');

    if ($page_id) {
      $page = Engine_Api::_()->getItem('page', $page_id);
      $page->approvedStatus($value);
    }

    $this->redirect();
  }

  public function enableAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $page_id = (int)$this->_getParam('page_id');
    $value = $this->_getParam('value');

    if ($page_id) {
      $page = Engine_Api::_()->getItem('page', $page_id);
      if ($value != $page->enabled) {
        $page->enabled = $value;
        $page->search = $value;
        $page->save();
      }
    }

    $this->redirect();
  }

  public function featureAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $page_id = (int)$this->_getParam('page_id');
    $value = $this->_getParam('value');

    if ($page_id) {
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
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $page_id = (int)$this->_getParam('page_id');
    $value = $this->_getParam('value');

    if ($page_id) {
      /**
       * @var $page Page_Model_Page
       */
      $page = Engine_Api::_()->getItem('page', $page_id);
      $page->sponsoredStatus($value);
    }

    $this->redirect();
  }

  public function deleteAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $page_id = (int)$this->_getParam('page_id');

    if ($page_id){
      $page = Engine_Api::_()->getItem('page', $page_id);
      $pageTable = Engine_Api::_()->getDbTable('pages', 'page');

      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store') ) {
        $apiTable = Engine_Api::_()->getDbTable('apis', 'store');
        $productsTable = Engine_Api::_()->getDbTable('products', 'store');
        $apiTable->delete(array('page_id = ?' => $page_id));
        $products = $productsTable->fetchAll($productsTable->select()->where('page_id = ?', $page_id));
        foreach( $products as $product ) {
          $product->delete();
        }
      }

      $pageTable->deletePage($page);
    }

    $this->redirect();
  }

  public function deleteAllAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $ids = $this->_getParam('items');
    $action = $this->_getParam('action_type');

    if (!empty($ids)) {

      $pageTable = Engine_Api::_()->getItemTable('page');
      $where = $pageTable->getAdapter()->quoteInto('page_id IN (?)', $ids);

      if ($action == 'delete' ) {
        $pages = $pageTable->select()->where($where);
        $pages = $pageTable->fetchAll($pages);
        foreach( $pages as $page ) {
          $page->delete();
        }
      } else {
        switch( $action ) {
          case 'approve' : $pageTable->update(array('approved' => 1, 'search' => 1), $where); break;
          case 'feature' : $pageTable->update(array('featured' => 1), $where); break;
          case 'sponsore' : $pageTable->update(array('sponsored' => 1), $where); break;
          case 'enable' : $pageTable->update(array('enabled' => 1, 'search' => 1), $where); break;
          case 'disable' : $pageTable->update(array('enabled' => 0, 'search' => 0), $where); break;
          case 'disapprove' : $pageTable->update(array('approved' => 0, 'search' => 0), $where); break;
          case 'notsponsore' : $pageTable->update(array('sponsored' => 0), $where); break;
          case 'notfeature' : $pageTable->update(array('featured' => 0), $where); break;
        }
      }
    }

    $this->redirect();
  }

  public function managePackageAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
      $this->redirect();

    $page_id = $this->_getParam('page_id');
    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->getItem('page', $page_id);

    $this->view->form = $form = new Page_Form_Admin_Manage_Package();


    /**
     * @var $packageTbl Page_Model_DbTable_Packages
     * @var $package Page_Model_Package
     */

    $packageTbl = Engine_Api::_()->getDbTable('packages', 'page');
    $stm = $packageTbl
      ->select()
      ->from($packageTbl
      ->info('name'), array('package_id', 'title', 'description', 'modules'))
      ->order('package_id ASC')
      ->query();

    $package_arr = $stm->fetchAll();

    //Prepare Form
    $packages = array();

    foreach( $package_arr as $package) {
      $packages[$package['package_id']] = $package['title'];
    }
    $form->getElement('package_id')->setMultiOptions($packages);
    if( $page->package_id ) $form->package_id->setValue($page->package_id);

    $packages = array();

    foreach( $package_arr as $package) {
      $packages[$package['package_id']] = array($package['title'], $package['description'], implode(', ', unserialize($package['modules'])));
    }
    $packages[0] = array('None', 'None', 'None');

    $this->view->packages = Zend_Json_Encoder::encode($packages);
    $this->view->all_packages = $packages;
    $this->view->page_id = $page_id;
    $this->view->package_id = $page->package_id;
    $this->view->page_title = $page->title;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    if( !$values['package_id'] || !$package = Engine_Api::_()->getItem('page_package', $values['package_id']) ) {
      return;
    }
    $subscTbl = Engine_Api::_()->getDbTable('subscriptions', 'page');
    $currentSubscription = $subscTbl->fetchRow(array(
      'page_id = ?' => $page->getIdentity(),
      'active = ?' => true,
    ));

    $subscTbl->cancelAll($page, 'User cancelled the subscription.', $currentSubscription);

    // Insert the new temporary subscription
    $db = $subscTbl->getAdapter();
    $db->beginTransaction();

    try {

      /**
       * @var $subscription Page_Model_Subscription
       */
      $subscription = $subscTbl->createRow();
      $subscription->setFromArray(array(
        'package_id' => $package->package_id,
        'page_id' => $page->getIdentity(),
        'status' => 'initial',
        'active' => false, // Will set to active on payment success
        'creation_date' => new Zend_Db_Expr('NOW()'),
      ));
      $subscription->save();
      $subscription->setActive(true);
      $subscription->onPaymentSuccess();

      $expiration = strtotime($values['expiration']);
      if( $values['is_expired_day'] && !$package->isForever() && $expiration) {
        $subscription->expiration_date = date('Y-m-d H:i:s', $expiration);
        $subscription->save();
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Pages Package has been changed and activated');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'page_admin_manage', true),
      'messages' => Array($this->view->message)
    ));
  }

  private function redirect()
  {
    return $this->_redirectCustom($this->view->url(array('action' => 'index'), 'page_admin_manage', true));
  }

}