<?php
class Ynbusinesspages_AdminComparisonController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_main_comparison');
    }
        
    public function indexAction() {
        $table = Engine_Api::_()->getDbTable('comparisonfields', 'ynbusinesspages');
        $comparisonfields = $table->fetchAll($table->select()->order('order ASC'));
        $this->view->comparisonfields = $comparisonfields;
    }
    
    public function createAction() {
        $this->view->form = $form = new Ynbusinesspages_Form_Admin_Comparison_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $success = FALSE;
        
        $values = $form->getValues();
        
        $db = Engine_Api::_()->getDbtable('comparisonfields', 'ynbusinesspages')->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('comparisonfields', 'ynbusinesspages');
            $field = $table->createRow();
            $field->setFromArray($values);
            $field->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        if ($success) {
            return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => true, 
                'messages' => 'Add Header sucessful.'));
        }
    }
    
    public function editAction() {
        $id = $this->_getParam('id');
        if (!$id || !($field = Engine_Api::_()->getItem('ynbusinesspages_comparisonfield', $id))) {
            return $this -> _helper -> requireSubject -> forward();
        }
        $this->view->form = $form = new Ynbusinesspages_Form_Admin_Comparison_Edit();
        $form->populate($field->toArray());
            
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if(!$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $db = Engine_Api::_()->getDbtable('comparisonfields', 'ynbusinesspages')->getAdapter();
        $db->beginTransaction();
        try {
            $field->setFromArray($form->getValues());
            $field->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        if ($success) {
            return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => true, 
                'messages' => 'Edit Header sucessful.'));
        }
       
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->field_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $field = Engine_Api::_()->getItem('ynbusinesspages_comparisonfield', $id);
                $field->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('This Header has been deleted.')
            ));
        }
    }
    
    public function sortAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $table = Engine_Api::_()->getDbTable('comparisonfields', 'ynbusinesspages');
        $comparisonfields = $table->fetchAll();
        $order = explode(',', $this->getRequest()->getParam('order'));
        foreach( $order as $i => $item ) {
            $field_id = substr($item, strrpos($item, '_') + 1);
            foreach( $comparisonfields as $field ) {
                if( $field->getIdentity() == $field_id ) {
                    $field->order = $i;
                    $field->save();
                }
            }
        }
    }
    
    public function showAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $field = Engine_Api::_()->getItem('ynbusinesspages_comparisonfield', $id);
        if ($field) {
            $field->show = $value;
            $field->save();
        }
    }
}