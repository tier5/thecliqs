<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminTaxesController.php 11.04.12 15:34 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminTaxesController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_taxes');
  }

  public function indexAction()
  {
    /**
     * @var $table Store_Model_DbTable_Taxes
     */

    $table             = Engine_Api::_()->getDbTable('taxes', 'store');
    $this->view->taxes = $taxes = $table->getTaxes();
    $this->view->form  = $form = new Store_Form_Admin_Taxes_Add();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();

    if (!$form->isValid($values)) {
      $form->populate($values);
      return;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $row = $table->createRow();
      $row->setFromArray($values);
      $row->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->reset();
    $this->view->taxes = $taxes = $table->getTaxes();
  }

  public function editAction()
  {
    /**
     * @var $table Store_Model_DbTable_Taxes
     */
    $this->_helper->layout->setLayout('default-simple');
    $tax_id           = (int)$this->_getParam('tax_id');
    $this->view->form = $form = new Store_Form_Admin_Taxes_Edit();

    $table = Engine_Api::_()->getDbTable('taxes', 'store');
    $tax   = $table->getRow($tax_id);
    $form->populate(
      array(
        'title'   => $tax->title,
        'percent' => number_format($tax->percent, 2, '.', '')
      )
    );
    if (!$tax) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_("Tax doesn't exists or not authorized to delete");
      return 0;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return 0;
    }

    $values = $this->getRequest()->getPost();

    if (!$form->isValid($values)) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Error!!! Fill rows!');
      return 0;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $tax->setFromArray($values);
      $tax->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
        array(
          'module'     => 'store',
          'controller' => 'taxes',
        ),
        'admin_default', true
      ),
      'messages'       => Array(Zend_Registry::get('Zend_Translate')->_('Tax has been saved.'))
    ));
  }

  public function deleteAction()
  {
    /**
     * @var $table Store_Model_DbTable_Taxes
     */
    $this->_helper->layout->setLayout('default-simple');
    $tax_id           = (int)$this->_getParam('tax_id');
    $this->view->form = new Store_Form_Admin_Taxes_Delete();
    $table            = Engine_Api::_()->getDbTable('taxes', 'store');
    $tax              = $table->getRow($tax_id);
    if (!$tax) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_("Tax doesn't exists or not authorized to delete");
      return 0;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return 0;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $tax->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
        array(
          'module'     => 'store',
          'controller' => 'taxes',
        ),
        'admin_default', true
      ),
      'messages'       => Array(Zend_Registry::get('Zend_Translate')->_('Tax has been deleted.'))
    ));
  }
}
