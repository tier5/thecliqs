<?php
class Yncredit_AdminManagePackageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('yncredit_admin_main', array(), 'yncredit_admin_main_manage_packages');

    $table = Engine_Api::_()->getDbTable('packages', 'yncredit');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $this->view->packages = $packages = $table->getPackages();
    $this->view->credits_for_one_unit = $settings->getSetting('yncredit.credit_price', 100);
    $this->view->currency = $settings->getSetting('payment.currency', 'USD');
  }
  public function createAction() 
  {
	// In smoothbox
	$this -> _helper -> layout -> setLayout('admin-simple');
	if (!$this -> _helper -> requireUser -> isValid())
		return;
	// Create form
	$this->view->form = $form = new Yncredit_Form_Admin_AddPackage();
	$table = Engine_Api::_()->getDbTable('packages', 'yncredit');
	if (!$this->getRequest()->isPost()) {
      return ;
    }

    $values = $this->getRequest()->getPost();

    if (!$form -> isValid($values)) 
    {
      	return;
    }

    if ($table->checkPackage($values)) 
    {
    	$form->addError('The price of package is exist.');
      	return ;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $row = $table->createRow();
      $row->setFromArray($values);
      $row->save();
      $db->commit();
	  return $this -> _forward('success', 'utility', 'core', 
		  array('smoothboxClose' => true, 
		  'parentRefresh' => true, 
		  'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The package was added successfully.'))));
    } catch(Exception $e) {
      $db->rollBack();
	  $form -> addError(Zend_Registry::get('Zend_Translate') -> _('Error.'));
      throw $e;
    }
  }
  public function deleteAction()
  {
  	// In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this -> view -> package_id = $package_id = $this->_getParam('package_id', 0);
	
    // Check post
    if( $this->getRequest()->isPost() )
    {
    	$table = Engine_Api::_()->getDbTable('packages', 'yncredit');
    	$table->deletePackage($package_id);
		// Refresh parent page
	    return $this->_forward('success', 'utility', 'core', array(
	          'smoothboxClose' => true,
	          'parentRefresh'=> 10,
	          'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The package was deleted successfully.'))
	      ));
	}
    // Output
    $this->renderScript('admin-manage-package/delete.tpl');
  }
  
  public function activeAction()
  {
    $package_id = $this->_getParam('package_id', 0);
    $status = $this->_getParam('status', 1);
    $table = Engine_Api::_()->getDbTable('packages', 'yncredit');
    $table->activePackage($package_id, $status);
  }
}