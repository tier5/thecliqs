<?php
class Ynlistings_AdminFaqsController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynlistings_admin_main', array(), 'ynlistings_admin_main_faqs');
    }    
    public function indexAction() {
        
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('faqs', 'ynlistings');
        $faqs = $table->fetchAll($table->select()->order('order ASC'));
        $this->view->paginator = Zend_Paginator::factory($faqs);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->faq_id=$id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $faq = Engine_Api::_()->getItem('ynlistings_faq', $id);
                $faq->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-faqs/delete.tpl');
    }
    
    public function editAction() {
        $id = $this->_getParam('id');
        $this->view->form = $form = new Ynlistings_Form_Admin_Faqs_Edit();
        $faq = Engine_Api::_()->getItem('ynlistings_faq', $id);
        $form->populate($faq->toArray());
            
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if(!$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $db = Engine_Api::_()->getDbtable('faqs', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        $faq = Engine_Api::_()->getItem('ynlistings_faq', $id);
        try {
            $faq->setFromArray($form->getValues());
            $faq->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        if ($success) {
            $this->_redirect('admin/ynlistings/faqs');
        }       
    }
    
    public function createAction() {
        $this->view->form = $form = new Ynlistings_Form_Admin_Faqs_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $success = FALSE;
        
        $values = array_merge($form->getValues(), array(
                'created_date' => date('Y-m-d H:i:s')
            ));
        
        $db = Engine_Api::_()->getDbtable('faqs', 'ynlistings')->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('faqs', 'ynlistings');
            $faq = $table->createRow();
            $faq->setFromArray($values);
            $faq->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        if ($success) {
            $this->_redirect('admin/ynlistings/faqs');
        }
    }

    public function multideleteAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true)
        {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id)
            {
                $faq = Engine_Api::_()->getItem('ynlistings_faq', $id);
                if ($faq) {
                    $faq->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }
}