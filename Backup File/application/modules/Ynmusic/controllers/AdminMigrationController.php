<?php
class Ynmusic_AdminMigrationController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynmusic_admin_main', array(), 'ynmusic_admin_main_migration');
		
		if (!Engine_Api::_()->hasModuleBootstrap('mp3music')) {
			 $this -> _helper -> redirector -> gotoRoute(array(), 'admin_default', TRUE);
		}
    }
        
    public function indexAction() {
        $this -> view -> form = $form = new Ynmusic_Form_Admin_Migration_Filter();	
		
		$form->populate($this->_getAllParams());
        $params = $form -> getValues();	
		
		$importedIds = Engine_Api::_()->getDbTable('imports', 'ynmusic')->getImportedIdsByType('mp3music_album');
		$albumsTbl = Engine_Api::_()->getItemTable('mp3music_album');
		$albumsTblName = $albumsTbl->info('name');
		$select = $albumsTbl->select()->from("$albumsTblName as object");
		
		$userTbl = Engine_Api::_()->getItemTable('user');
		$userTblName = $userTbl->info('name');
		$select->setIntegrityCheck(false)->joinLeft("$userTblName as user", "user.user_id = object.user_id", "");
			
		if (!empty($params['owner'])) {
			$select->where("user.displayname LIKE ?", '%'.$params['owner'].'%');
		}
		if (!empty($params['title'])) {
			$select->where("object.title LIKE ?", '%'.$params['title'].'%');
		}
		
		$sysTimezone = date_default_timezone_get();
		if (!empty($params['from_date'])) {
            $from_date = new Zend_Date(strtotime($params['from_date']));
			$from_date->setTimezone($sysTimezone);
			$select->where("object.creation_date >= ?", $from_date->get('yyyy-MM-dd'));
			
        }
		
	    if (!empty($params['to_date'])) {
	    	$to_date = new Zend_Date(strtotime($params['to_date']));
			$to_date->setTimezone($sysTimezone);
			$select->where("object.creation_date <= ?", $to_date->get('yyyy-MM-dd'));
	    }
		
		if (!empty($importedIds))
			$select->where("object.album_id NOT IN (?)", $importedIds);
		
		if (!empty($params['order']) && !empty($params['direction'])) {
			$select->order($params['order'].' '.$params['direction']);
		}
		else {
			$select->order("object.creation_date DESC");
		} 
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->formValues = $params;
		
		$page = $this->_getParam('page', 1);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
    }
	
	public function playlistAction() {
        $this -> view -> form = $form = new Ynmusic_Form_Admin_Migration_Filter();	
		
		$form->populate($this->_getAllParams());
        $params = $form -> getValues();	
		
		$importedIds = Engine_Api::_()->getDbTable('imports', 'ynmusic')->getImportedIdsByType('mp3music_playlist');
		$playlistsTbl = Engine_Api::_()->getItemTable('mp3music_playlist');
		$playlistsTblName = $playlistsTbl->info('name');
		$select = $playlistsTbl->select()->from("$playlistsTblName as object");
		
		$userTbl = Engine_Api::_()->getItemTable('user');
		$userTblName = $userTbl->info('name');
		$select->setIntegrityCheck(false)->joinLeft("$userTblName as user", "user.user_id = object.user_id", "");
			
		if (!empty($params['owner'])) {
			$select->where("user.displayname LIKE ?", '%'.$params['owner'].'%');
		}
		if (!empty($params['title'])) {
			$select->where("object.title LIKE ?", '%'.$params['title'].'%');
		}
		
		$sysTimezone = date_default_timezone_get();
		if (!empty($params['from_date'])) {
            $from_date = new Zend_Date(strtotime($params['from_date']));
			$from_date->setTimezone($sysTimezone);
			$select->where("object.creation_date >= ?", $from_date->get('yyyy-MM-dd'));
			
        }
		
	    if (!empty($params['to_date'])) {
	    	$to_date = new Zend_Date(strtotime($params['to_date']));
			$to_date->setTimezone($sysTimezone);
			$select->where("object.creation_date <= ?", $to_date->get('yyyy-MM-dd'));
	    }
		
		if (!empty($importedIds))
			$select->where("object.playlist_id NOT IN (?)", $importedIds);
		
		if (!empty($params['order']) && !empty($params['direction'])) {
			$select->order($params['order'].' '.$params['direction']);
		}
		else {
			$select->order("object.creation_date DESC");
		} 
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->formValues = $params;
		
		$page = $this->_getParam('page', 1);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
    }
	
    public function importAction() {
       	$item_guid = $this->_getParam('item', '');
		$item = Engine_Api::_()->getItemByGuid($item_guid);
		if (!$item_guid || !$item) {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array($this->view->translate('Item not found.')),
            ));
		}
	   
	   	if (Engine_Api::_()->ynmusic()->hasImported($item)) {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array($this->view->translate('This item %s has been imported to social music.', $item)),
            ));
		}
		
		Engine_Api::_()->getDbTable('imports', 'ynmusic')->addItem($item);
		// Add to jobs
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynmusic_migration', array(
            'item' => $item->getGuid(),
        ));
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array($this->view->translate('This item %s has been processing for importing to social music.', $item)),
        ));
    }
    
	public function multiimportAction() {
        $items = $this -> _getParam('items', '');
		$items = explode(',', $items);
		if (empty($items)) {
			$this->_forward('success', 'utility', 'core', array(
	            'smoothboxClose' => true,
	            'parentRefresh'=> false,
	            'messages' => array($this->view->translate('Please select at least one item for importing.')),
	        ));
			return;
		}
		
		$count = 0;
		foreach ($items as $item_guid) {
			$item = Engine_Api::_()->getItemByGuid($item_guid);
			if (!Engine_Api::_()->ynmusic()->hasImported($item)) {
				Engine_Api::_()->getDbTable('imports', 'ynmusic')->addItem($item);
				// Add to jobs
		        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynmusic_migration', array(
		            'item' => $item->getGuid(),
		        ));
				$count++;
			}
		}
		
		if ($count == 0) {
			$this->_forward('success', 'utility', 'core', array(
	            'smoothboxClose' => true,
	            'parentRefresh'=> false,
	            'messages' => array($this->view->translate('Error! Can not found any items for importing.')),
	        ));
		}
		else {
			$this->_forward('success', 'utility', 'core', array(
	            'smoothboxClose' => true,
	            'parentRefresh'=> true,
	            'messages' => array($this->view->translate('%s item(s) have has been processing for importing to social music.', $count)),
	        ));
		}
    }
	
	
	public function updateSongsAction() {
       	$id = $this->_getParam('id', 0);
		
		$import_item = Engine_Api::_()->getDbTable('imports','ynmusic')->find($id)->current();
		if (!$id || !$import_item) {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array($this->view->translate('Item not found.')),
            ));
			return;
		}
		$newItem = ($import_item->status == 'processing') ? null : Engine_Api::_()->getItem($import_item->item_type, $import_item->item_id);
        $oldItem = Engine_Api::_()->getItem($import_item->from_type, $import_item->from_id);
		if (!$newItem || !$oldItem || !Engine_Api::_()->ynmusic()->canUpdateImport($newItem, $oldItem)) {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array($this->view->translate('Can not update songs of this item.')),
            ));
			return;
		}
		
		if ($import_item->status == 'updating') {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array($this->view->translate('Item is updating.')),
            ));
			return;
		}
		
		Engine_Api::_()->getDbTable('imports', 'ynmusic')->updateItem($newItem, $oldItem, 'updating');
		// Add to jobs
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynmusic_migration', array(
            'item' => $oldItem->getGuid(),
        ));
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array($this->view->translate('This item %s has been processing for updating songs.', $newItem)),
        ));
    }

	public function reportAction() {
		$this -> view -> form = $form = new Ynmusic_Form_Admin_Migration_ReportFilter();	
		
		$form->populate($this->_getAllParams());
        $params = $form -> getValues();
		
		$table = Engine_Api::_()->getDbTable('imports','ynmusic');
		$tableName = $table->info('name');
		$select = $table->select()->from("$tableName as report");
		
		$userTbl = Engine_Api::_()->getItemTable('user');
		$userTblName = $userTbl->info('name');
		$select->setIntegrityCheck(false)->joinLeft("$userTblName as user", "user.user_id = report.user_id", "");
		$select -> where("report.from_type IN ('mp3music_album','mp3music_playlist')");
		if (!empty($params['item_title'])) {
			$sAlbumTbl = Engine_Api::_()->getItemTable('ynmusic_album');
			$sAlbumTblName = $sAlbumTbl->info('name');
			$sPlaylistTbl = Engine_Api::_()->getItemTable('ynmusic_playlist');
			$sPlaylistTblName = $sPlaylistTbl->info('name');
			$select
				->joinLeft("$sAlbumTblName as sAlbum", "sAlbum.album_id = report.item_id AND report.item_type = 'ynmusic_album'", "")
				->joinLeft("$sPlaylistTblName as sPlaylist", "sPlaylist.playlist_id = report.item_id AND report.item_type = 'ynmusic_playlist'", "");
			$select->where("sAlbum.title LIKE ? OR sPlaylist.title LIKE ?", '%'.$params['item_title'].'%');
		}
		
		if (!empty($params['ori_title'])) {
			$mAlbumTbl = Engine_Api::_()->getItemTable('mp3music_album');
			$mAlbumTblName = $mAlbumTbl->info('name');
			$mPlaylistTbl = Engine_Api::_()->getItemTable('mp3music_playlist');
			$mPlaylistTblName = $mPlaylistTbl->info('name');
			$select
				->joinLeft("$mAlbumTblName as mAlbum", "mAlbum.album_id = report.from_id AND report.from_type = 'mp3music_album'", "")
				->joinLeft("$mPlaylistTblName as mPlaylist", "mPlaylist.playlist_id = report.from_id AND report.from_type = 'mp3music_playlist'", "");
			$select->where("mAlbum.title LIKE ? OR mPlaylist.title LIKE ?", '%'.$params['ori_title'].'%');
		}
		
		if (!empty($params['type']) && ($params['type'] != 'all')) {
			$select->where('report.item_type = ?', $params['type']);
		}
		
		if (!empty($params['owner'])) {
			$select->where("user.displayname LIKE ?", '%'.$params['owner'].'%');
		}

		if (!empty($params['status'])  && ($params['status'] != 'all')) {
			$select->where('report.status = ?', $params['status']);
		}
		
		if (!empty($params['order']) && !empty($params['direction'])) {
			$select->order($params['order'].' '.$params['direction']);
		}
		else {
			$select->order("report.modified_date DESC");
		} 
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->formValues = $params;
		
		$page = $this->_getParam('page', 1);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(10);
    }
	
	public function removeAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $import_item = Engine_Api::_()->getDbTable('imports','ynmusic')->find($id)->current();
                $import_item->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This report item has been removed.')
            ));
        }
    }

    public function multiremoveAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id) {
                $import_item = Engine_Api::_()->getDbTable('imports','ynmusic')->find($id)->current();
                if ($import_item) {
                    $import_item->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynmusic','controller'=>'migration', 'action'=>'report'), 'admin_default', TRUE);
        }
    }
}