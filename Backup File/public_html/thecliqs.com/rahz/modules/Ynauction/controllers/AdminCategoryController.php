<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminCategoryController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminCategoryController extends Core_Controller_Action_Admin
{
  protected $_paginate_params = array();
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_categories');
  }
  public function indexAction()
  {
   
  }
  public function addCategoryAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->getRequest()->isPost()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Invalid request method');
      exit;
    }
    try {
      $category =  Engine_Api::_()->getDbtable('categories', 'ynauction')->createRow();
      $category->title = trim($this->getRequest()->getParam('title'));
	  $category->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	  if($this->getRequest()->getParam('cat_id'))
		$category->parent = $this->getRequest()->getParam('cat_id');
	  else	  
		$category->parent = 0;
      $category->save();
      $this->view->success = true;
    } catch (Exception $e) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Unknown database error');
      throw $e;
    }
  }
  public function removeCategoryAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->getRequest()->isPost()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('isGet');
      exit;
    }
    $category     = Engine_Api::_()->getItem('ynauction_category', $this->getRequest()->getParam('cat_id'));
    if (!$category) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Not a valid category');
      $this->view->post    = $_POST;
      return;
    }
    $db = Engine_Api::_()->getDbTable('categories', 'ynauction')->getAdapter();
    $db->beginTransaction();
    try {
	  if(!$this->getRequest()->getParam('subcat_id'))
	  {
		  foreach (Engine_Api::_()->ynauction()->getCategories($category->category_id) as $subCategory)
			$subCategory->delete();
	  }
      $category->delete();
      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error   = $translate->_('Unknown database error');
      throw $e;
    }
  }
  public function renameCategoryAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->getRequest()->isPost()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Invalid request method');
      exit;
    }
    $category     = Engine_Api::_()->getItem('ynauction_category', $this->getRequest()->getParam('cat_id'));
    if (!$category) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Not a valid category');
      return;
    }
    $db = Engine_Api::_()->getDbTable('categories', 'ynauction')->getAdapter();
    $db->beginTransaction();
    try {
        $category->setTitle( trim($this->getRequest()->getParam('title') ));
      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error   = $translate->_('Unknown database error');
      throw $e;
    }
  }
  
}
