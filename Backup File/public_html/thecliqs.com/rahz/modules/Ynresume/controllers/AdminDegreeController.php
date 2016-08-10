<?php
class Ynresume_AdminDegreeController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynresume_admin_main', array(), 'ynresume_admin_main_degree');

    $this->view->degrees = Engine_Api::_()->getDbtable('degrees', 'ynresume')->getAllDegress();
  }
  

  public function addAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Ynresume_Form_Admin_Degree_Add();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getDbtable('degrees', 'ynresume');

        // insert the category into the database
        $row = $table->createRow();
        $row->name = $values["label"];
        $row->save();

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

    // Output
    $this->renderScript('admin-degree/form.tpl');
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->category_id = $id;
    
    $degreeTable = Engine_Api::_()->getDbtable('degrees', 'ynresume');
    $degree = $degreeTable->find($id)->current();

      $this->view->canDelete = true;
      // Check post
      if( $this->getRequest()->isPost() ) {
        $db = $degreeTable->getAdapter();
        $db->beginTransaction();

        try {

          $degree->delete();

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

    // Output
    $this->renderScript('admin-degree/delete.tpl');
  }

  public function editAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Must have an id
    if( !($id = $this->_getParam('id')) ) {
      die('No identifier specified');
    }
    $degreeTable = Engine_Api::_()->getDbtable('degrees', 'ynresume');
    $degree = $degreeTable->find($id)->current();
    $form = $this->view->form = new Ynresume_Form_Admin_Degree_Add();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setField($degree);
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $degree->name = $values["label"];
        $degree->save();
        
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
    
    // Output
    $this->renderScript('admin-degree/form.tpl');
  }

}