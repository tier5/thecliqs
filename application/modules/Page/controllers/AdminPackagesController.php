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

class Page_AdminPackagesController extends Core_Controller_Action_Admin
{
	public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_permission');

		/**
		 * @var $_settings Core_Api_Settings
		 */
		$settings = Engine_Api::_()->getDbTable('settings', 'core');

    if (!$settings->__get('page.package.enabled', 0)) {
			return $this->_helper->redirector->gotoRoute(array('module'=>'page', 'controller'=>'permission'), 'admin_default', true);
		}
  }
  
	public function indexAction()
  {
  	$this->view->filterForm = $filterForm = new Page_Form_Admin_Package_Filter();
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
      'order' => 'p.package_id',
      'order_direction' => 'DESC',
    ), $values);
    $this->view->assign($values);

		/**
		 * @var $table Page_Model_DbTable_Packages
		 */
    $table = Engine_Api::_()->getDbTable('packages', 'page');
    $prefix = $table->getTablePrefix();

  	$select = $table
			->select()
			->setIntegrityCheck(false)
			->from(array('p'=>$prefix.'page_packages'))
			->joinLeft(array('s'=>$prefix.'page_pages'), 's.package_id =p.package_id', array('totalpages'=>'COUNT(DISTINCT(s.page_id))'));

    $select->order(( !empty($values['order']) ? $values['order'] : 'p.package_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

  	if( !empty($values['name']) ){
      $select->where('p.name LIKE ?', '%' . $values['name'] . '%');
    }

    if( !empty($values['price']) ){
      $select->where('p.price LIKE ?', '%' . $values['price'] . '%');
    }

    if( !empty($values['enabled']) && $values['enabled']!= -1 ){
      $select->where('p.enabled LIKE ?', '%' . $values['enabled'] . '%');
    }
		
		$select->group('p.package_id');

    $this->view->default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.default.package', 1);
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(40);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

  public function createAction()
  {
	  if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('payment')) {
      return $this->_forward('notfound', 'error', 'core');
    }
    // Make form
    $this->view->form = $form = new Page_Form_Admin_Package_Create();
		$form->onShow();
		
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

		$values = $this->getRequest()->getPost();
    if( !$form->isValid($values) ) {
      return;
    }

    // Process
    $values = $form->onSave($values);

    $packageTable = Engine_Api::_()->getDbtable('packages', 'page');
    $db = $packageTable->getAdapter();
    $db->beginTransaction();

    try {
      // Create package
      $package = $packageTable->createRow();
      $package->setFromArray($values);
      $package->save();

			$gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
			
      // Create package in gateways?
      if( !$package->isFree() ) {
        foreach( $gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway ) {
          $gatewayPlugin = $gateway->getGateway();
          // Check billing cycle support
          if( !$package->isOneTime() ) {
            $sbc = $gateway->getGateway()->getSupportedBillingCycles();
            if( !in_array($package->recurrence_type, array_map('strtolower', $sbc)) ) {
              continue;
            }
          }
          if( method_exists($gatewayPlugin, 'createProduct') ) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
        }
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function editAction()
  {
		/**
		 * @var $viewer User_Model_User
		 * @var $packagesTb Page_Model_DbTable_Packages
		 * @var $permissionsTb Authorization_Model_DbTable_Permissions
     * @var $package Page_Model_Package
		 */
    $viewer = $this->_helper->api()->user()->getViewer();
    $packagesTb = $this->_helper->api()->getDbtable('packages', 'page');
		$permissionsTb =  $this->_helper->api()->getDbtable('permissions', 'authorization');

    $package_id = $this->_getParam('package_id', 0);

    $package = Engine_Api::_()->getItem('page_package', $package_id);
    $values = $package->toArray();

    $this->view->form = $form = new Page_Form_Admin_Package_Create();
    $form->setDescription('PACKAGE_EDIT_DESC');
    $form->setTitle('PACKAGE_EDIT_TITLE');
    $form->getElement('submit')->setLabel('PAGE_PACKAGE_Save Package');
    $form->populate($form->onEdit($values));
		$form->onShow();

    if ( $package->isDefault() ){
      $form->getElement('price')
      ->setAttrib('disabled', true)
      ;

      $form->removeElement('recurrence');
      $form->removeElement('duration');
    }

    if( !$this->getRequest()->isPost() ){
      return;
    }

    $values = $this->getRequest()->getPost();

    if ( $package->isDefault() ){
     $values['price'] = 0.00;
    }

  	if( !$form->isValid($values) ) {
      return;
    }

		$values = $form->onSave($values);

		//Begin Transaction
    $prefix = $packagesTb->getTablePrefix();
		$db = $packagesTb->getAdapter();
		$db->beginTransaction();
    try{
      $package->setFromArray($values);
      $package->save();

      // Create package in gateways?
      if( !$package->isFree() ) {
        $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        foreach( $gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway ) {
          $gatewayPlugin = $gateway->getGateway();
          // Check billing cycle support
          if( !$package->isOneTime() ) {
            $sbc = $gateway->getGateway()->getSupportedBillingCycles();
            if( !in_array($package->recurrence_type, array_map('strtolower', $sbc)) ) {
              continue;
            }
          }
          if( !method_exists($gatewayPlugin, 'createProduct') ||
            !method_exists($gatewayPlugin, 'editProduct') ||
            !method_exists($gatewayPlugin, 'detailVendorProduct') ) {
            continue;
          }
          // If it throws an exception, or returns empty, assume it doesn't exist?
          try {
            $info = $gatewayPlugin->detailVendorProduct($package->getGatewayIdentity());
          } catch( Exception $e ) {
            $info = false;
          }
          // Create
          if( !$info ) {
            $gatewayPlugin->createProduct($package->getGatewayParams());
          }
          // Edit
          else {
            $gatewayPlugin->editProduct($package->getGatewayIdentity(), $package->getGatewayParams());
          }
        }
      }

      $db->commit();
    }catch(Exception $e){
      $db->rollback();
      throw $e;
    }

    return $this->_redirectCustom(
			array('route' => 'page_admin_packages'),
			array('reset' => true)
		);
  }

  public function makeDefaultAction()
  {
    $package_id = $this->_getParam('package_id', 0);

    /**
     * @var $package Page_Model_Package
     */
    if (  null == ($package = Engine_Api::_()->getItem('page_package', $package_id)) || !$package->isFree() || !$package->isOneTime() || !$package->isForever() ) {
      return $this->_redirectCustom(array('route'=>'page_admin_packages'), array('reset'=>true));
    }

    $this->view->package = $package;

    $this->view->form = $form = new Page_Form_Packages_Default();

  	$description = sprintf(Zend_Registry::get('Zend_Translate')
  	  ->_('MAKE_PACKAGE_DEFAULT_DESC'), $package->getTitle());

  	$form
      ->setDescription($description)
      ->getDecorator('description')
      ->setOptions(array('placement' => 'PREPEND', 'class' => 'form-description', 'escape'=>false));

    $form->populate(array('package_id'=>$package->getIdentity()));

  	if (!$this->getRequest()->isPost()) {
  		return;
  	}

    Engine_Api::_()->getApi('settings', 'core')->__set('page.default.package', $package->getIdentity());


    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Default package successfully has been changed!');
    return $this->_forward('success' ,'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'messages' => Array($this->view->message)
    ));
  }


  public function deleteAction()
  {
    $package_id = $this->_getParam('package_id', 0);


    /**
     * @var $table Page_Model_DbTable_Packages
     *
     * @var $package Page_Model_Package
     */
    $table = Engine_Api::_()->getItemTable('page_package');
    if (  null == ($package = $table->findRow($package_id)) )
    {
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('No package found!');
      return $this->_forward('success' ,'utility', 'core', array(
        'smoothboxClose' => true,
        'messages' => Array($this->view->message)
      ));
    }

    if ( $table->isDefault( $package ) )
    {
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Change your default package before deleting!');
      return $this->_forward('success' ,'utility', 'core', array(
        'smoothboxClose' => true,
        'messages' => Array($this->view->message)
      ));
    }

    $this->view->form = $form = new Page_Form_Packages_Delete();

  	$description = sprintf(Zend_Registry::get('Zend_Translate')
  	  ->_('DELETE_PAGE_PACKAGE_DESC'), $package->getTitle());

  	$form
      ->setDescription($description)
      ->getDecorator('description')
      ->setOptions(array('placement' => 'PREPEND', 'class' => 'form-description', 'escape'=>false));

    $form->populate(array('package_id'=>$package->getIdentity()));

  	if ( !$this->getRequest()->isPost() ) {
  		return;
  	}

    /**
     * Downgrade Pages
     *
     * @var $table Page_Model_DbTable_Subscriptions
     */

    $table = Engine_Api::_()->getItemTable('page_subscription');
    $prefix = $table->getTablePrefix();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try{
      // Delete package in gateways?
      $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
      foreach( $gatewaysTable->fetchAll(array('enabled = ?' => 1)) as $gateway ) {
        $gatewayPlugin = $gateway->getGateway();
        if( method_exists($gatewayPlugin, 'deleteProduct') ) {
          try {
            $gatewayPlugin->deleteProduct($package->getGatewayIdentity());
          } catch( Exception $e ) {} // Silence?
        }
      }

      $package->downgradePages();
      $package->delete();
      
      $db->commit();
    }catch(Exception $e){
      $db->rollBack();
      throw $e;
    }


    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Default package successfully has been changed!');
    return $this->_forward('success' ,'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'messages' => Array($this->view->message)
    ));
  }

	public function saveOrderAction()
	{
		$this->_forward('index');
	}
}