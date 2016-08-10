<?php
class Ynjobposting_AdminJobtypesController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_manage_jobtypes');

    $this->view->jobtypes = Engine_Api::_()->getDbtable('jobtypes', 'ynjobposting')->getAllJobTypes();
  }
  

  public function addAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Ynjobposting_Form_Admin_JobType_Add();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $table = Engine_Api::_()->getDbtable('jobtypes', 'ynjobposting');

        $row = $table->createRow();
        $row->title = $values["label"];
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
    $this->renderScript('admin-jobtypes/form.tpl');
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->id = $id;
    
    $typeTable = Engine_Api::_()->getDbtable('jobtypes', 'ynjobposting');
    $type = $typeTable->find($id)->current();

      $this->view->canDelete = true;
      // Check post
      if( $this->getRequest()->isPost() ) {
        $db = $typeTable->getAdapter();
        $db->beginTransaction();

        try {

          $type->delete();

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
    $this->renderScript('admin-jobtypes/delete.tpl');
  }

  public function editAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Must have an id
    if( !($id = $this->_getParam('id')) ) {
      die('No identifier specified');
    }
    $typeTable = Engine_Api::_()->getDbtable('jobtypes', 'ynjobposting');
    $type = $typeTable->find($id)->current();
    $form = $this->view->form = new Ynjobposting_Form_Admin_JobType_Add();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setField($type);
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $type->title = $values["label"];
        $type->save();
        
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
    $this->renderScript('admin-jobtypes/form.tpl');
  }

}