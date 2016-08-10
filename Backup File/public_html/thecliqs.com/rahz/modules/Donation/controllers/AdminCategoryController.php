<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 21.08.12
 * Time: 10:47
 * To change this template use File | Settings | File Templates.
 */
class Donation_AdminCategoryController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_admin_main', array(), 'donation_admin_main_categories');
  }

  public function indexAction()
  {
    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'donation')->fetchAll();
  }

  public function editCategoryAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Donation_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Must have an id
    if( !($id = $this->_getParam('id')) ) {
      die('No identifier specified');
    }

    $categoryTable = Engine_Api::_()->getDbtable('categories', 'donation');
    $category = $categoryTable->find($id)->current();
    $form->populate($category->toArray());
    $form->submit->setLabel('Edit Category');

    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if ( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
      // Ok, we're good to add field
    $values = array_merge($form->getValues(), array(
      'user_id' => $viewer->getIdentity(),
    ));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $category->setFromArray($values);
      $category->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('')
    ));

  }

  public function addCategoryAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Donation_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Check post
    if( !$this->getRequest()->isPost()) {
      return;
    }
    if ( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    // we will add the category
    $values = array_merge($form->getValues(), array(
      'user_id' => $viewer->getIdentity(),
    ));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      // add category to the database
      // Transaction
      $table = Engine_Api::_()->getDbtable('categories', 'donation');

      // insert the category into the database
      $category = $table->createRow();
      $category->setFromArray($values);
      $category->save();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('')
    ));
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->donation_id = $donation_id = $this->_getParam('id');

    $donationTable = Engine_Api::_()->getDbtable('donations', 'donation');
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'donation');
    $category = $categoryTable->find($donation_id)->current();

    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      // go through logs and see which groups used this category id and set it to ZERO
      $donationTable->update(array(
        'category_id' => 0,
      ), array(
        'category_id = ?' => $category->getIdentity(),
      ));

      $category->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('')
    ));
  }
}
