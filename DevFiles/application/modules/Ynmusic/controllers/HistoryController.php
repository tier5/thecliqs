<?php
class Ynmusic_HistoryController extends Core_Controller_Action_Standard {
	public function init() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
	}
	
	public function indexAction() {
		$this->_helper->content->setEnabled()->setNoRender();
	}
	
	public function removeAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        $id = $this->_getParam('id');
        $this->view->history = $id;
		$history = Engine_Api::_()->getDbTable('history','ynmusic')->find($id)->current();
		if (!$id || !$history) {
			return $this -> _helper -> requireSubject() -> forward();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($history->user_id != $viewer->getIdentity()) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $history->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array($this->view->translate('This history item has been remove.'))
            ));
        }
    }
	
	public function multiremoveAction() {
		$this->_helper->layout->setLayout('default-simple');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $table = Engine_Api::_()->getDbTable('history','ynmusic');
            $ids_array = explode(",", $ids);
			$table->removeHistory($viewer, $ids_array);
			
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array($this->view->translate('Remove history items successfully.'))
            ));
			
        }
    }
	
	public function removeallAction() {
		$this->_helper->layout->setLayout('default-simple');
        $viewer = Engine_Api::_() -> user() -> getViewer();

        // Check post
        if ($this -> getRequest() -> isPost()) {
            //Process delete
            $table = Engine_Api::_()->getDbTable('history','ynmusic');
			$table->removeHistory($viewer, array());
			
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array($this->view->translate('Clear History successfully.'))
            ));
			
        }
    }	
}