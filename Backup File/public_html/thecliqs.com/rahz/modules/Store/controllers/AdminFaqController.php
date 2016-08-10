<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminFaqController.php 27.04.12 18:27 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminFaqController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main');
  }

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_faq');

    /**
     * @var $table Store_Model_DbTable_Faq
     */

    $table = Engine_Api::_()->getDbTable('faq', 'store');
    $this->view->paginator = $paginator = Zend_Paginator::factory($table->select());
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function createAction()
  {
    $this->view->form = $form = new Store_Form_Admin_Faq_Create();

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return ;
    }

    $values = $form->getValues();

    /**
     * @var $table Store_Model_DbTable_Faq
     */

    $table = Engine_Api::_()->getDbTable('faq', 'store');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $faq = $table->createRow();
      $faq->setFromArray($values);
      $faq->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_redirectCustom(array(
      'route'      => 'admin_default',
      'module'     => 'store',
      'controller' => 'faq',
      'reset'      => true,
    ));
  }

  public function editAction()
  {
    $faq_id = $this->_getParam('faq_id', 0);
    if (!$faq_id) {
      $this->_redirectCustom(array(
        'route'      => 'admin_default',
        'module'     => 'store',
        'controller' => 'faq',
        'reset'      => true,
      ));
    }

    /**
     * @var $table Store_Model_DbTable_Faq
     */

    $table = Engine_Api::_()->getDbTable('faq', 'store');

    $faq = $table->fetchRow(array('faq_id = ?' => $faq_id));
    $this->view->form = $form = new Store_Form_Admin_Faq_Edit();
    $form->populate($faq->toArray());

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return ;
    }

    $values = $form->getValues();

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $faq->setFromArray($values);
      $faq->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $form->populate($faq->toArray());
    $form->addNotice('Changes successfully saved.');
  }

  public function deleteAction()
  {
    /**
     * @var $table Store_Model_DbTable_Faq
     */
    $this->_helper->layout->setLayout('default-simple');
    $faq_id           = (int)$this->_getParam('faq_id', 0);
    $this->view->form = new Store_Form_Admin_Faq_Delete();
    $table            = Engine_Api::_()->getDbTable('faq', 'store');
    $faq              = $table->fetchRow(array('faq_id = ?' => $faq_id));
    if (!$faq) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_("FAQ doesn't exists or not authorized to delete");
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
      $faq->delete();
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
          'controller' => 'faq',
        ),
        'admin_default', true
      ),
      'messages'       => Array(Zend_Registry::get('Zend_Translate')->_('FAQ has been deleted.'))
    ));
  }
}
