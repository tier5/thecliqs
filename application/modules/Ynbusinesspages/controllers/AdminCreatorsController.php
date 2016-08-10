<?php
class Ynbusinesspages_AdminCreatorsController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_main_creator');
    }
        
    public function indexAction() {
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('creators', 'ynbusinesspages');
        $creators = $table->fetchAll($table->select());
        $this->view->paginator = Zend_Paginator::factory($creators);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
    
    public function createAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->form = $form = new Ynbusinesspages_Form_Admin_Creators_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $success = FALSE;
        
        $values = $form->getValues();
        
        if (!isset($values['toValues']) || empty($values['toValues'])) {
            $form->addError('Can not find the user.');
            return;
        } 
        $db = Engine_Api::_()->getDbtable('creators', 'ynbusinesspages')->getAdapter();
        $db->beginTransaction();
        
        $ids = explode(',', $values['toValues']);
        try {
            $table = Engine_Api::_()->getDbtable('creators', 'ynbusinesspages');
            foreach ($ids as $id) {
                $creator = $table->createRow();
                $creator->user_id = $id;
                $creator->save();
            }
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
                'messages' => 'Add Creator sucessful.'));
        }
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->creator_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $creator = Engine_Api::_()->getItem('ynbusinesspages_creator', $id);
                $creator->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('This creator has been removed.')
            ));
        }
    }
    
    public function multideleteAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE)
        {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id)
            {
                $faq = Engine_Api::_()->getItem('ynbusinesspages_creator', $id);
                if ($faq) {
                    $faq->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynbusinesspages','controller'=>'creators', 'action'=>'index'), 'admin_default' , TRUE);
        }
    }

    public function suggestAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('user');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = Engine_Api::_()->getItemTable('user')->select()->where('search = ?', 1);
    
        $creators = Engine_Api::_()->getItemTable('ynbusinesspages_creator')->getCreators();
        if (!empty($creators)) {
            $select->where('user_id NOT IN (?)', $creators);
        }
        
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $friend ){
            $data[] = array(
                'id' => $friend->getIdentity(),
                'label' => $friend->getTitle(), // We should recode this to use title instead of label
                'title' => $friend->getTitle(),
                'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                'url' => $friend->getHref(),
                'type' => 'user',
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
}
