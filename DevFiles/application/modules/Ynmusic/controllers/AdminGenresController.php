<?php
class Ynmusic_AdminGenresController extends Core_Controller_Action_Admin {
	protected $_paginate_params = array();
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_main_genres');
	}

	public function indexAction() {
		$params = array (
			'page' => $this->_getParam('page', 1),
			'admin' => 1
		);
		$this -> view -> paginator = Engine_Api::_()->getDbTable('genres', 'ynmusic')->getPaginator($params);
		$this -> view -> params = $params;
	}

	public function createAction() {
        $this->view->form = $form = new Ynmusic_Form_Admin_Genres_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
		
        $values = $form->getValues();
		
		$title = $values['title'];
		$validate = Engine_Api::_()->getDbTable('genres', 'ynmusic')->checkTitle($title);
		if (!$validate) {
			$form->addError($this->view->translate('This genre title already exist. Please try to add another.'));
			return;
		}
		
        $table = Engine_Api::_()->getDbtable('genres', 'ynmusic');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $genre = $table->createRow();
            $genre->setFromArray($values);
            $genre->save();
            $db->commit();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array($this->view->translate('Add genre successfully!'))
        ));       
    }
	
	public function editAction() {
		$id = $this->_getParam('id', 0);
		$genre = Engine_Api::_()->getItem('ynmusic_genre', $id);
		if (!$id || !$genre) {
			return $this->_helper->requireSubject()->forward();
		}
        $this->view->form = $form = new Ynmusic_Form_Admin_Genres_Edit();
		$form->populate($genre->toArray());
		
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
		
		$title = $values['title'];
		$validate = Engine_Api::_()->getDbTable('genres', 'ynmusic')->checkTitle($title, $id);
		if (!$validate) {
			$form->addError($this->view->translate('This genre title already exist. Please try to add another.'));
			return;
		}

        $table = Engine_Api::_()->getDbtable('genres', 'ynmusic');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $genre->setFromArray($values);
            $genre->save();
            $db->commit();
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array($this->view->translate('Edit genre successfully!'))
        ));       
    }

	public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->genre_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $genre = Engine_Api::_()->getItem('ynmusic_genre', $id);
                $genre->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This genre has been deleted.')
            ));
        }
    }

	public function multideleteAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id) {
                $genre = Engine_Api::_()->getItem('ynmusic_genre', $id);
                if ($genre) {
                    $genre->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmusic','controller'=>'genres', 'action'=>'index'), 'admin_default', TRUE);
        }
    }
}
