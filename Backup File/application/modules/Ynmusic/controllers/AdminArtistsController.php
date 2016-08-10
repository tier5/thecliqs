<?php
class Ynmusic_AdminArtistsController extends Core_Controller_Action_Admin
{		
	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_main_artists');
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
                $artist = Engine_Api::_()->getItem('ynmusic_artist', $id);
                if ($artist) {
                    $artist->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmusic','controller'=>'artists', 'action'=>'index'), 'admin_default' , TRUE);
        }
    }
	
	public function suggestGenreAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('ynmusic_genre');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = $table -> select();
        
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`title` LIKE ?', '%'. $text .'%');
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $genre ){
            $data[] = array(
                'id' => $genre->getIdentity(),
                'label' => $genre->getTitle(), // We should recode this to use title instead of label
                'title' => $genre->getTitle(),
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
	
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Artist_Search();
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('artists', 'ynmusic');
		$params = $this ->_getAllParams();
		$form -> populate($params);
		$this->view->formValues = $params;
		$params['admin'] = true;
        $this->view->paginator = $table -> getArtistsPaginator($params);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}

	public function deleteAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$this -> view -> form  = new Ynmusic_Form_Admin_Artist_Delete();
		$artist = Engine_Api::_() -> getItem('ynmusic_artist', $this -> getRequest() -> getParam('id'));
		
		if (!$artist) {
			return $this->_helper->requireSubject()->forward();
		}
		// Check post
	    if(!$this->getRequest()->isPost()) {
            return;
        }
		
		$db = Engine_Api::_() -> getDbTable('artists', 'ynmusic') -> getAdapter();
		$db -> beginTransaction();
		try	{
			$artist -> delete();
			$db -> commit();
		}
		catch (Exception $e) {
			$db -> rollback();
			throw $e;
		}
		
		 return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Artist deleted.')),
			'format' => 'smoothbox',
            'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}

	public function editAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$this -> view -> artist = $artist = Engine_Api::_() -> getItem('ynmusic_artist', $this ->_getParam('id'));
		if (!$artist)
		{
			 return $this->_helper->requireSubject()->forward();
		}
		
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Artist_Edit(array('artist' => $artist));
		$form -> populate($artist -> toArray());
		
		//populate genre
		// get mapping table
		$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
		$this -> view -> genres = $genreMappingsTable -> getGenresByItem($artist);
		
		 if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
		
		$artistTable =  Engine_Api::_() -> getDbTable('artists', 'ynmusic');
		$db = $artistTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Save params
			$values = $form -> getValues();
			$artist -> setFromArray($values);
			$artist -> save();
			
			// get mapping table
			$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
			
			// Delete all genre with artist in mapping table
			$genreMappingsTable -> deleteGenresByItem($artist);
			
			// Save genre to mapping table
			$genreArr = explode(',',$values['toValues']);
			foreach($genreArr as $genre_id) {
				$mapRow = $genreMappingsTable -> createRow();
				$mapRow -> item_id = $artist -> getIdentity();
				$mapRow -> item_type = $artist -> getType();
				$mapRow -> genre_id = $genre_id;
				$mapRow -> save();
			}
			
			// Set photo
			if (!empty($values['photo'])) {
				$artist -> setPhoto($form -> photo, "photo_id");
			}
		
			// Set cover
			if (!empty($values['cover'])) {
				$artist -> setPhoto($form -> cover, "cover_id");
			}
			
			$db -> commit();
			$this->_helper->redirector->gotoRoute(array('module'=>'ynmusic','controller'=>'artists', 'action' => 'index'), 'admin_default', true);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	public function createAction()
	{
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Artist_Create();
		
	    if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
		
		$artistTable =  Engine_Api::_() -> getDbTable('artists', 'ynmusic');
		$db = $artistTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Save params
			$values = $form -> getValues();
			$artist = $artistTable -> createRow();
			$artist -> setFromArray($values);
			$artist -> search = 1;
			$artist -> save();
			
			// get mapping table
			$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
			
			// Save genre to mapping table
			$genreArr = explode(',',$values['toValues']);
			foreach($genreArr as $genre_id) {
				$mapRow = $genreMappingsTable -> createRow();
				$mapRow -> item_id = $artist -> getIdentity();
				$mapRow -> item_type = $artist -> getType();
				$mapRow -> genre_id = $genre_id;
				$mapRow -> save();
			}
			
			// Set photo
			if (!empty($values['photo'])) {
				$artist -> setPhoto($form -> photo, "photo_id");
			}
		
			// Set cover
			if (!empty($values['cover'])) {
				$artist -> setPhoto($form -> cover, "cover_id");
			}
			
			$db -> commit();
			$this->_helper->redirector->gotoRoute(array('module'=>'ynmusic','controller'=>'artists', 'action' => 'index'), 'admin_default', true);
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

}
