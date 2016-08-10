<?php
class Ynultimatevideo_AdminMigrationVideoController extends Core_Controller_Action_Admin {
    public function init() 
    {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynultimatevideo_admin_main', array(), 'ynultimatevideo_admin_main_migrationvideo');
		
		if (!Engine_Api::_()->hasModuleBootstrap('video') && !Engine_Api::_()->hasModuleBootstrap('ynvideo')) {
			 $this -> _helper -> redirector -> gotoRoute(array('module' => 'ynultimatevideo', 'controller' => 'manage'), 'admin_default', TRUE);
		}
    }
        
    public function indexAction() {
        $this -> view -> form = $form = new Ynultimatevideo_Form_Admin_Migration_Filter();	
		$form->populate($this->_getAllParams());
        $params = $form -> getValues();	
		
		$importedIds = Engine_Api::_()->getDbTable('imports', 'ynultimatevideo')->getImportedIdsByType('video');
		$videosTbl = Engine_Api::_()->getItemTable('video');
		$videosTblName = $videosTbl->info('name');
		$select = $videosTbl->select()->from("$videosTblName as object");
		
		$userTbl = Engine_Api::_()->getItemTable('user');
		$userTblName = $userTbl->info('name');
		$select->setIntegrityCheck(false)->joinLeft("$userTblName as user", "user.user_id = object.owner_id", "");
			
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
			$select->where("object.video_id NOT IN (?)", $importedIds);
		
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
	
	public function playlistAction() 
	{
		if (!Engine_Api::_()->hasModuleBootstrap('ynvideo')) {
			 $this -> _helper -> redirector -> gotoRoute(array('module' => 'ynultimatevideo', 'controller' => 'migration-video'), 'admin_default', TRUE);
		}
        $this -> view -> form = $form = new Ynultimatevideo_Form_Admin_Migration_Filter();	
		
		$form->populate($this->_getAllParams());
        $params = $form -> getValues();	
		
		$importedIds = Engine_Api::_()->getDbTable('imports', 'ynultimatevideo')->getImportedIdsByType('ynvideo_playlist');
		$playlistsTbl = Engine_Api::_()->getItemTable('ynvideo_playlist');
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
		$this -> _helper -> layout -> setLayout('admin-simple');
		//Get first category
		$tableCategory = Engine_Api::_() -> getItemTable('ynultimatevideo_category');
		$firstCategory = $tableCategory -> getFirstCategory();
		$category_id = $this -> _getParam('category_id', $firstCategory -> category_id);
		// Prepare form
		$this->view->form = $form = new Ynultimatevideo_Form_Admin_Migration_Category();

		$categoryElement = $form -> getElement('category_id');
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			$categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this -> view -> translate($item['title']));
		}
		//populate category
		if ($category_id) {
			$form -> category_id -> setValue($category_id);
		} else {
			$form -> addError('Create video require at least one category. Please contact admin for more details.');
		}

		$post = $this -> getRequest() -> getPost();
		if ($this -> getRequest() -> isPost() && $form -> isValid($post))
		{
			//Process
			$item_guid = $this->_getParam('item', '');
			$item = Engine_Api::_()->getItemByGuid($item_guid);
			if (!$item_guid || !$item) {
				$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh'=> false,
					'messages' => array($this->view->translate('Item not found.')),
				));
			}

			$values = $form->getValues();

			if (Engine_Api::_()->ynultimatevideo()->hasImported($item)) {
				$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh'=> false,
					'messages' => array($this->view->translate('This item %s has been imported to ultimate video.', $item)),
				));
			}

			Engine_Api::_()->getDbTable('imports', 'ynultimatevideo')->addItem($item);
			// Add to jobs
			Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynultimatevideo_migration', array(
				'item' => $item->getGuid(),
				'category_id' => $values['category_id'],
			));

			$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh'=> true,
				'messages' => array($this->view->translate('This item %s has been processing for importing to ultimate videos.', $item)),
			));
		}

		$this -> renderScript('admin-migration-video/import.tpl');
    }
    
	public function multiimportAction() {
		$this -> _helper -> layout -> setLayout('admin-simple');
		//Get first category
		$tableCategory = Engine_Api::_() -> getItemTable('ynultimatevideo_category');
		$firstCategory = $tableCategory -> getFirstCategory();
		$category_id = $this -> _getParam('category_id', $firstCategory -> category_id);
		// Prepare form
		$this->view->form = $form = new Ynultimatevideo_Form_Admin_Migration_Category();

		$categoryElement = $form -> getElement('category_id');
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			$categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $this -> view -> translate($item['title']));
		}

		//Get items
		$items = $this -> _getParam('items', '');

		//populate category
		if ($category_id) {
			$form -> category_id -> setValue($category_id);
		} else {
			$form -> addError('Create video require at least one category. Please contact admin for more details.');
		}

		$post = $this -> getRequest() -> getPost();
		if ($this -> getRequest() -> isPost() && $form -> isValid($post))
		{
			$items = explode(',', $items);
			if (empty($items)) {
				$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh'=> false,
					'messages' => array($this->view->translate('Please select at least one item for importing.')),
				));
				return;
			}

			$values = $form->getValues();
			$count = 0;
			foreach ($items as $item_guid) {
				$item = Engine_Api::_()->getItemByGuid($item_guid);
				if (!Engine_Api::_()->ynultimatevideo()->hasImported($item)) {
					Engine_Api::_()->getDbTable('imports', 'ynultimatevideo')->addItem($item);
					// Add to jobs
					Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynultimatevideo_migration', array(
						'item' => $item->getGuid(),
						'category_id' => $values['category_id'],
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
					'messages' => array($this->view->translate('%s item(s) have has been processing for importing to ultimate video.', $count)),
				));
			}
		}

		$this -> renderScript('admin-migration-video/import.tpl');
    }
	
	
	public function updateVideosAction() {
       	$id = $this->_getParam('id', 0);
		
		$import_item = Engine_Api::_()->getDbTable('imports','ynultimatevideo')->find($id)->current();
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
		if (!$newItem || !$oldItem || !Engine_Api::_()->ynultimatevideo()->canUpdateImport($newItem, $oldItem)) {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array($this->view->translate('Can not update videos of this item.')),
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
		
		Engine_Api::_()->getDbTable('imports', 'ynultimatevideo')->updateItem($newItem, $oldItem, 'updating');
		// Add to jobs
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynultimatevideo_migration', array(
            'item' => $oldItem->getGuid(),
        ));
		
		$this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array($this->view->translate('This item %s has been processing for updating videos.', $newItem)),
        ));
    }

	public function reportAction() {
		$this -> view -> form = $form = new Ynultimatevideo_Form_Admin_Migration_ReportFilter();	
		
		$form->populate($this->_getAllParams());
        $params = $form -> getValues();
		
		$table = Engine_Api::_()->getDbTable('imports','ynultimatevideo');
		$tableName = $table->info('name');
		$select = $table->select()->from("$tableName as report");
		
		$userTbl = Engine_Api::_()->getItemTable('user');
		$userTblName = $userTbl->info('name');
		$select->setIntegrityCheck(false)->joinLeft("$userTblName as user", "user.user_id = report.user_id", "");
		$select -> where("report.from_type IN ('video','ynvideo_playlist')");
		if (!empty($params['item_title'])) {
			$sVideoTbl = Engine_Api::_()->getItemTable('ynultimatevideo_video');
			$sVideoTblName = $sVideoTbl->info('name');
			$sPlaylistTbl = Engine_Api::_()->getItemTable('ynultimatevideo_playlist');
			$sPlaylistTblName = $sPlaylistTbl->info('name');
			$select
				->joinLeft("$sVideoTblName as sVideo", "sVideo.video_id = report.item_id AND report.item_type = 'ynultimatevideo_video'", "")
				->joinLeft("$sPlaylistTblName as sPlaylist", "sPlaylist.playlist_id = report.item_id AND report.item_type = 'ynultimatevideo_playlist'", "");
			$select->where("sVideo.title LIKE ? OR sPlaylist.title LIKE ?", '%'.$params['item_title'].'%');
		}
		
		if (!empty($params['ori_title'])) {
			$mVideoTbl = Engine_Api::_()->getItemTable('video');
			$mVideoTblName = $mVideoTbl->info('name');
			$mPlaylistTbl = Engine_Api::_()->getItemTable('ynvideo_playlist');
			$mPlaylistTblName = $mPlaylistTbl->info('name');
			$select
				->joinLeft("$mVideoTblName as mVideo", "mVideo.video_id = report.from_id AND report.from_type = 'video'", "")
				->joinLeft("$mPlaylistTblName as mPlaylist", "mPlaylist.playlist_id = report.from_id AND report.from_type = 'ynvideo_playlist'", "");
			$select->where("mVideo.title LIKE ? OR mPlaylist.title LIKE ?", '%'.$params['ori_title'].'%');
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
                $import_item = Engine_Api::_()->getDbTable('imports','ynultimatevideo')->find($id)->current();
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
                $import_item = Engine_Api::_()->getDbTable('imports','ynultimatevideo')->find($id)->current();
                if ($import_item) {
                    $import_item->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynultimatevideo','controller'=>'migration', 'action'=>'report'), 'admin_default', TRUE);
        }
    }
}