<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminCategoryController.php 03.02.12 16:14 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_AdminCategoryController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hegift_admin_main', array(), 'hegift_admin_main_category');
  }

  public function indexAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Categories
     */

    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $this->view->form = $form = new Hegift_Form_Admin_Category_Create();

    $page = $this->_getParam('page', 1);

    $this->view->paginator = $paginator = $table->getCategories();
    $paginator->setCurrentPageNumber($page);

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return ;
    }

    $values = $this->getRequest()->getPost();

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $row = $table->createRow();
      $row->setFromArray($values);
      $row->save();
      $db->commit();
    } catch(Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_redirectCustom($this->view->url(array('module' => 'hegift', 'controller' => 'category', 'action' => 'index'), 'admin_default', true));
  }

  public function renameAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Categories
     */
    $category_id = $this->_getParam('category_id', 0);
    $title = $this->_getParam('title', '');

    if (_ENGINE_ADMIN_NEUTER) {
      return ;
    }

    if (!$category_id || $category_id == 1) {
      $this->view->result = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Category doesn\'t exists');
      return ;
    }

    if (!$title) {
      $this->view->result = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot rename to empty string');
      return ;
    }

    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $table->renameCategory($category_id, $title);
    $this->view->result = 1;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Successfully renamed');
  }

  public function deleteAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->form = new Hegift_Form_Admin_Category_Delete();
    $category_id = $this->_getParam('category_id', 0);

    if( !$category_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Category doesn't exists");
      return;
    }

    if( $category_id == 1)
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("You cannot to detele default category!");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    /**
     * @var $table Hegift_Model_DbTable_Categories
     **/
    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $table->deleteCategory($category_id);

    $message = Zend_Registry::get('Zend_Translate')->_('Category has been deleted and changed gift category to default category.');
    $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
          array(
            'module' => 'hegift',
            'controller' => 'category',
            'action' => 'index'
          ),
          'admin_default', true
        ),
      'messages' => Array($message)
    ));
  }
}
