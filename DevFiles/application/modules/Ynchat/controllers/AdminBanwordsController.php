<?php
class Ynchat_AdminBanwordsController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynchat_admin_main', array(), 'ynlistings_admin_main_banwords');
    }    
    public function indexAction() {
        
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getItemTable('ynchat_banword');
        $banwords = $table->fetchAll($table->select()->order('banword_id DESC'));
        $this->view->paginator = Zend_Paginator::factory($banwords);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->banword = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $faq = Engine_Api::_()->getItem('ynchat_banword', $id);
                $faq->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This ban word has been deleted.')
            ));
        }
    }
    
    public function editAction() {
        $id = $this->_getParam('id');
        $this->view->form = $form = new Ynchat_Form_Admin_Banwords_Edit();
        $banword = Engine_Api::_()->getItem('ynchat_banword', $id);
        $form->populate($banword->toArray());
        $form->find_value->setAttrib('disabled', 'disabled');
        $form->find_value->setRequired(false);
            
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if(!$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $db = Engine_Api::_()->getDbtable('banwords', 'ynchat')->getAdapter();
        $db->beginTransaction();
        $banword = Engine_Api::_()->getItem('ynchat_banword', $id);
        $values = $form->getValues();
        unset($values['find_value']);
        try {
            $banword->setFromArray($values);
            $banword->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array('Ban word successfully edited.'),
        ));       
    }
    
    public function createAction() {
        
        $this->view->form = $form = new Ynchat_Form_Admin_Banwords_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
        $values['find_value'] = trim(strip_tags($values['find_value']));
        $values['find_value'] = strtolower($values['find_value']);
        $values['replacement'] = strip_tags($values['replacement']);
        $db = Engine_Api::_()->getDbtable('banwords', 'ynchat')->getAdapter();
        $db->beginTransaction();
        $viewer = Engine_Api::_()->user()->getViewer();
        try {
            $table = Engine_Api::_()->getDbtable('banwords', 'ynchat');
            $select = $table->select()->where('find_value = ?', $values['find_value']);
            $banword = $table->fetchRow($select);
            if (!$banword)
                $banword = $table->createRow();
            $banword->setFromArray($values);
            $banword->user_id = $viewer->getIdentity();
            $banword->save();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array('Ban word successfully added.'),
        ));
    }

    public function multideleteAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true) {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id)
            {
                $banword = Engine_Api::_()->getItem('ynchat_banword', $id);
                if ($banword) {
                    $banword->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }
}