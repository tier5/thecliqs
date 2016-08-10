<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_FolderController extends Core_Controller_Action_Standard
{
    protected $_parentType;
    protected $_parentId;
    protected $_viewer;

    static protected $_roles_for_group = array('owner', 'parent_member', 'registered', 'everyone');
    static protected $_roles_for_event = array('owner', 'parent_member', 'registered', 'everyone');
    static protected $_roles_for_user = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

    /**
     *
     * @param Ynfilesharing_Model_Folder $folder
     * @param string $auth
     * @param string $parentType
     * @param Authorization_Api_Core $api
     * @param string $action
    */
    private function _setAuth($folder, $auth = NULL, $parentType, $api, $action) {
        if ($parentType == 'group')
        {
            $roles = self::$_roles_for_group;
        }
	else if($parentType == 'event')
	{
		$roles = self::$_roles_for_event;
	}
        else
        {
			$roles = self::$_roles_for_user;
        }

        if ($auth == NULL)
        {
            $auth = "everyone";
        }
        $authMax = array_search($auth, $roles);
        foreach ($roles as $i => $role)
        {
            $api->setAllowed($folder, $role, $action, ($i <= $authMax));
        }
    }

    /**
     *
     * @param Ynfilesharing_Model_Folder $folder
     * @param Authorization_Api_Core $authApi
     * @param string $action
     * @return string
     */
    private function _getAuth($folder, $authApi, $action) {
    	
        if ($folder->parent_type == 'group') {
        	$roles = self::$_roles_for_group;
        } 
	else if($folder->parent_type == 'event')
	{
		$roles = self::$_roles_for_event;
	}
        else {
            $roles = self::$_roles_for_user;
        }

        foreach (array_reverse($roles) as $role) {
            if ($authApi->isAllowed($folder, $role, $action)) {
                return $role;
            }
        }
    }

    public function init() 
    {
        $messages = $this->_helper->flashMessenger->getMessages();
        if (count($messages)) {
            $message = current($messages);
            $this->view->messages = array($message['message']);
            $this->view->error = $message['error'];
        }
        $this->view->viewer = $this->_viewer = Engine_Api::_()->user()->getViewer();
        $this->_parentType = $this->_getParam('parent_type', 'user');
        if ($this->_parentType == 'user') {
            $this->_parentId = $this->_viewer->getIdentity();
        } else {
            $this->_parentId = $this->_getParam('parent_id');
            $object = Engine_Api::_()->getItem($this->_parentType, $this->_parentId);
            if (!($object && $object->membership()->isMember($this->_viewer))) {
                $this->_parentType = 'user';
                $this->_parentId = $this->_viewer->getIdentity();
            }
        }

        $this->view->parentId = $this->_parentId;
        $this->view->parentType = $this->_parentType;
    }

    public function viewAction() {
        $fileTbl = new Ynfilesharing_Model_DbTable_Files();
        $parentObject = Engine_Api::_()->getItem($this->_parentType, $this->_parentId);
        $this->view->fileTotal = $fileTotal = $fileTbl->countAllFilesBy($parentObject);
        if ($this->_viewer->getIdentity())
        {
	        $this->view->maxFileTotal = $maxFileTotal = Engine_Api::_()->ynfilesharing() -> getAllowed('folder', $this->_viewer, 'userfile');
        }
        else 
        {
        	$this->view->maxFileTotal = $maxFileTotal = -1;
        }
        $folderName = $this->_viewer->getGuid();
        $this->view->totalSizePerUser = $totalSizePerUser = Ynfilesharing_Plugin_Utilities::getFolderSize(Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR);
        
        $maxSizeKB = (INT)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $this->_viewer, 'usertotal');
        if ($this->_viewer->getIdentity())
        {
	        $space_limit = (int) Engine_Api::_()->authorization()->getPermission($this->_viewer->level_id, 'user', 'quota');
	        if($space_limit)
	        {
	            $space_limit = $space_limit/ Ynfilesharing_Plugin_Constants::KILOBYTE;
	            if($space_limit < $maxSizeKB || !$maxSizeKB)
	            {
	                $maxSizeKB = $space_limit;
	            }
	        }
        }
        $this->view->maxTotalSizePerUser = $maxTotalSizePerUser = $maxSizeKB * 1024;

        $folderId = $this->_getParam('folder_id', 0);

        if ($folderId != 0) {
            $this->view->folder = $folder = Engine_Api::_()->getItem('folder', $folderId);
        }

        if ($folder)
        {
            Engine_Api::_()->core()->setSubject($folder);
        }

        if (!$this->_helper->requireSubject('folder')->isValid())
        {
            return;
        }

        if(!$folder->isAllowed($this->_viewer, 'view'))
        {
            return $this->_helper->requireAuth()->forward();
        }

        // increase the view count
        $folder->view_count = $folder->view_count + 1;
        $this->view->folderTags = $folder->tags()->getTagMaps();
        $folder->save();

        $filesharingApi = Engine_Api::_()->ynfilesharing();
        $folders = $filesharingApi->getSubFolders($folder);

        $this->view->subFolders = $subFolders = $filesharingApi->getFolders($folders);
        $this->view->files = $filesharingApi->getFilesInFolder($folder);
        $foldersArr = array();
        foreach ($folders as $f)
        {
            array_push($foldersArr, $f);
        }
        array_push($foldersArr, $folder);

        $this->view->foldersPermissions = $filesharingApi->getFoldersPermissions($foldersArr);

        // Get filesharing table
        $file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
        $file_name = $file_table -> info('name');
        $folder_table = Engine_Api::_() -> getItemTable('folder');
        $folder_name = $folder_table -> info('name');
        // Search Params
        $form = new Ynfilesharing_Form_Search ();
        $form->setAction($this->view->baseUrl() . "/filesharing/folder/view/" . $folderId);
        $form->isValid ( $this->_getAllParams () );
        $params = $form->getValues ();
        $params['user_id'] = $this->_viewer->getIdentity();
        $params['folder_id'] = $folderId;
        $files = array();
        $folders = array();
        if (isset ( $params ['type'] )) {
            switch ($params ['type']) {
                case 'file' :
                    $files = $filesharingApi->selectFilesByOptions($params);
                    break;
                case 'folder' :
                    $folders = $filesharingApi->selectFoldesByOptions($params);
                    break;
                case 'all' :
                    $files = $filesharingApi->selectFilesByOptions($params);
                    $folders = $filesharingApi->selectFoldesByOptions($params);
                    break;
                default :
                    break;
            }
            $this->view->files = $filesharingApi->getFiles($files, 'view', $this->_viewer);
        }
        else {
            foreach ($filesharingApi->getSubFolders($folder) as $f) {
                array_push($folders, $f);
            }
            $this->view->files = $filesharingApi->getFilesInFolder($folder);
        }

        $this->view->subFolders = $subFolders = $filesharingApi->getFolders($folders, 'view', $this->_viewer);
        $folderPermissions = $filesharingApi->getFoldersPermissions($folders, $this->_viewer);

        $this->view->canCreate = $folder->authorization()->isAllowed($this->_viewer, 'create');
        $this->view->canEdit = $canEdit = $folder->isAllowed($this->_viewer, 'edit');
        $this->view->canEditPerm = $folder->isAllowed($this->_viewer, 'edit_perm');
        $this->view->canDelete = $canDelete = $folder->isAllowed($this->_viewer, 'delete');

        if ($folder) {
            $perms = array();
            if (!empty($canEdit)) {
                array_push($perms, 'edit');
            }
            if (!empty($canDelete)) {
                array_push($perms, 'delete');
            }
            $folderPermissions[$folder->getIdentity()] = $perms;
        }
        $this->view->foldersPermissions = $folderPermissions;
        $this->_helper->content->setEnabled();
    }

    public function createAction()
    {
        if (!$this->_helper->requireUser->isValid())
        {
            return;
        }

        if (!$this->_helper->requireAuth->setAuthParams('folder', $this->_viewer, 'create')->isValid()) {
            return;
        }
        // Get navigation
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynfilesharing_main', array(),'ynfilesharing_main_manage');

        $parentFolderId = $this->_getParam('parent_folder_id', 0);

        if (!empty($this->_parentType) && !empty($this->_parentId)) {
            $this->view->form = $form = new Ynfilesharing_Form_Folder(array(
                'parentType' => $this->_parentType,
                'parentId'   => $this->_parentId
            ));
        }
        $parent = Engine_Api::_()->getItem($this->_parentType, $this->_parentId);

        if (!$this->getRequest()->isPost())
        {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost()))
        {
            return;
        }

        $currentSubjectSize = Engine_Api::_()->ynfilesharing()->getCurrentFolderSizeOfObject($parent);

        $values = $form->getValues();
        
        //check title
        $title = $values['title'];
        $bad = array("<", ">", ":", '"', "/", "\\", "|", "?", "*");
        $result = str_replace($bad, "", $title);
        if($result != $title)
        {
            $form->getElement('title')->addError(Zend_Registry::get('Zend_Translate')->_('A folder name can\'t contain any of the following character: /\:*?"<>|'));
            return false;
        }
        $nameSystemWin = array("CON", "PRN", "AUX", "NUL", "COM1", "COM2", "COM3", "COM4", "COM5", "COM6", "COM7", "COM8", "COM9", "LPT1", "LPT2", "LPT3", "LPT4", "LPT5", "LPT6", "LPT7", "LPT8", "LPT9");
        if(in_array(strtoupper($title), $nameSystemWin))
        {
            $form->getElement('title')->addError(Zend_Registry::get('Zend_Translate')->_('A folder name can\'t have any of the following names: CON, PRN, AUX, NUL, COM1, COM2, COM3, COM4, COM5, COM6, COM7, COM8, COM9, LPT1, LPT2, LPT3, LPT4, LPT5, LPT6, LPT7, LPT8, and LPT9'));
            return false;
        }
        
        $values['parent_type'] = $this->_parentType;
        $values['parent_id'] = $this->_parentId;
        $values['creation_date'] = date('Y-m-d H:i:s');
        $values['modified_date'] = $values['creation_date'];
        $values['user_id'] = $this->_viewer->getIdentity();
        $values['parent_folder_id'] = $parentFolderId;

        $folderTbl = new Ynfilesharing_Model_DbTable_Folders();
        $folderSelect = $folderTbl->select();
        $folderSelect->where('title LIKE ?', $values['title']);
        $folderSelect->where('parent_type = ?', $values['parent_type']);
        $folderSelect->where('parent_id = ?', $values['parent_id']);
        $folderSelect->where('parent_folder_id = ?', $parentFolderId);

        $existingFolder = $folderTbl->fetchRow($folderSelect);

        if ($existingFolder != NULL) {
            // redirect after creating folder unsuccessfully
            $this->_helper->flashMessenger->addMessage(
                array(
                    'message' => sprintf(
                        Zend_Registry::get('Zend_Translate')->_('There is an existing folder with the same name. Folder %s is created unsuccessfully !'),
                        $folder->title
                    ),
                    'error' => 1
                )
            );
        } else {
            $db = $folderTbl->getAdapter();
            $db->beginTransaction();
            try {
                $folder = $folderTbl->createRow();
                $folder->setFromArray($values);
                $folder->save();

                // Add tags
                $tags = preg_split('/[,]+/', $values['tags']);
                $folder->tags()->addTagMaps($this->_viewer, $tags);

                // TODO [DangTH] : check again, do we need to check the existing tag
                $searchTbl = Engine_Api::_()->getDbTable('search', 'core');
                $select = $searchTbl->select()
                    ->where('type = ?', $folder->getType())
                    ->where('id = ?', $folder->getIdentity());
                $row = $searchTbl->fetchRow($select);
                if($row)
                {
                    $row->keywords = $values['tags'];
                    $row->save();
                }
                else
                {
                    $row = $searchTbl->createRow();
                    $row->type = $folder->getType();
                    $row->id = $folder->getIdentity();
                    $row->title = $folder->title;
                    $row->description = $folder->title;
                    $row->keywords = $values['tags'];
                    $row->save();
                }

                // set permissions to view, create, edit, delete, comment
                $authApi = Engine_Api::_()->authorization()->context;

                $this->_setAuth($folder, $values['auth_view'], $this->_parentType, $authApi, 'view');
                $this->_setAuth($folder, $values['auth_create'], $this->_parentType, $authApi, 'create');
                $this->_setAuth($folder, $values['auth_edit'], $this->_parentType, $authApi, 'edit');
                $this->_setAuth($folder, $values['auth_delete'], $this->_parentType, $authApi, 'delete');
                $this->_setAuth($folder, $values['auth_comment'], $this->_parentType, $authApi, 'comment');
                $this->_setAuth($folder, $values['auth_edit_perm'], $this->_parentType, $authApi, 'edit_perm');

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_helper->flashMessenger->addMessage(
                array(
                    'message' => sprintf(Zend_Registry::get('Zend_Translate')->_('Folder "%s" is created successfully !'), $folder->title),
                    'error' => 0
                )
            );
        }

        // redirect after creating folder
        if ($folder) {
            $parent = $folder->getParentFolder();
        } else {
            $parent = Engine_Api::_()->getItem('folder', $parentFolderId);
        }

        if ($parent != NULL) {
            return $this -> _redirectCustom(
                $parent->getHref(array('parent_type' => $this->_parentType, 'parent_id' => $this->_parentId))
            );
        } else {
            return $this->_helper->redirector->gotoRoute(
                array(
                    'action' => 'manage',
                    'parent_type' => $this->_parentType,
                    'parent_id' => $this->_parentId
                ),
                'ynfilesharing_general',
                true
            );
        }
    }

    public function editPermAction() {
        if (!$this->_helper->requireUser->isValid())
        {
            return;
        }

        // Get navigation
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynfilesharing_main', array());

        $folderId = $this->_getParam('folder_id');
        if ($folderId) {
            $this->view->folder =  $folder = Engine_Api::_()->getItem('folder', $folderId);
        }
        if ($folder && is_object($folder)){
            Engine_Api::_()->core()->setSubject($folder);
        }
        if (!$this->_helper->requireSubject('folder')->isValid())
        {
            return;
        }

        if(!$folder->isAllowed($this->_viewer, 'edit_perm') || !$folder->isAllowed($this->_viewer, 'view'))
        {
            return $this->_helper->requireAuth()->forward();
        }

        $this->view->form = $form = new Ynfilesharing_Form_FolderPermission(array('folder' => $folder));
        $authApi = Engine_Api::_()->authorization()->context;

        if (!$this->getRequest()->isPost()) {
            // set view authentication and comment authentication for the two dropdownlists
            $form->getElement('auth_view')->setValue($this->_getAuth($folder, $authApi, 'view'));
            $form->getElement('auth_create')->setValue($this->_getAuth($folder, $authApi, 'create'));
            $form->getElement('auth_edit')->setValue($this->_getAuth($folder, $authApi, 'edit'));
            $form->getElement('auth_delete')->setValue($this->_getAuth($folder, $authApi, 'delete'));
            $form->getElement('auth_comment')->setValue($this->_getAuth($folder, $authApi, 'comment'));

            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $folder->setFromArray($values);
            $folder->save();
            $this->_setAuth($folder, $values['auth_view'], $folder->parent_type, $authApi, 'view');
            $this->_setAuth($folder, $values['auth_create'], $folder->parent_type, $authApi, 'create');
            $this->_setAuth($folder, $values['auth_edit'], $folder->parent_type, $authApi, 'edit');
            $this->_setAuth($folder, $values['auth_delete'], $folder->parent_type, $authApi, 'delete');
            $this->_setAuth($folder, $values['auth_comment'], $folder->parent_type, $authApi, 'comment');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this ->_redirectCustom(
                $folder->getHref(array('parent_id' => $this->_parentId, 'parent_type' => $this->_parentType))
        );
    }

    public function editAction() {
        if (!$this->_helper->requireUser->isValid())
        {
            return;
        }

        // Get navigation
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynfilesharing_main', array());

        $folderId = $this->_getParam('folder_id');
        if ($folderId) {
            $folder = Engine_Api::_()->getItem('folder', $folderId);
        }
        if ($folder && is_object($folder)){
            Engine_Api::_()->core()->setSubject($folder);
        }
        if (!$this->_helper->requireSubject('folder')->isValid())
        {
            return;
        }

        if(!$folder->isAllowed($this->_viewer, 'edit') || !$folder->isAllowed($this->_viewer, 'view'))
        {
            return $this->_helper->requireAuth()->forward();
        }

        $this->view->form = $form = new Ynfilesharing_Form_EditFolder(array('folder' => $folder));
        $authApi = Engine_Api::_()->authorization()->context;

        if (!$this->getRequest()->isPost()) {
            $form->populate($folder->toArray());

            // prepare tags
            $folderTags = $folder->tags()->getTagMaps();

            $tagString = '';
            foreach ($folderTags as $tagMap) {
                if ($tagString !== '')
                    $tagString .= ', ';
                $tagString .= $tagMap->getTag()->getTitle();
            }

            $form->getElement('tags')->setValue($tagString);

            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $values = $form->getValues();
        
        $title = $values['title'];
        $bad = array("<", ">", ":", '"', "/", "\\", "|", "?", "*");
        $result = str_replace($bad, "", $title);
        if($result != $title)
        {
            $form->getElement('title')->addError(Zend_Registry::get('Zend_Translate')->_('A folder name can\'t contain any of the following character: /\:*?"<>|'));
            return false;
        }
        
        $nameSystemWin = array("CON", "PRN", "AUX", "NUL", "COM1", "COM2", "COM3", "COM4", "COM5", "COM6", "COM7", "COM8", "COM9", "LPT1", "LPT2", "LPT3", "LPT4", "LPT5", "LPT6", "LPT7", "LPT8", "LPT9");
        if(in_array(strtoupper($title), $nameSystemWin))
        {
            $form->getElement('title')->addError(Zend_Registry::get('Zend_Translate')->_('A folder name can\'t have any of the following names: CON, PRN, AUX, NUL, COM1, COM2, COM3, COM4, COM5, COM6, COM7, COM8, COM9, LPT1, LPT2, LPT3, LPT4, LPT5, LPT6, LPT7, LPT8, and LPT9'));
            return false;
        }

        $folderTbl = new Ynfilesharing_Model_DbTable_Folders();
        $folderSelect = $folderTbl->select();
        $folderSelect->where('title LIKE ?', $values['title']);
        $folderSelect->where('parent_type = ?', $this->_parentType);
        $folderSelect->where('parent_id = ?', $this->_parentId);
        $folderSelect->where('parent_folder_id = ?', $folder->parent_folder_id);
        $folderSelect->where('folder_id != ?', $folderId);
        $existingFolder = $folderTbl->fetchRow($folderSelect);
        if ($existingFolder != NULL) {
            // redirect after creating folder unsuccessfully
            $this->_helper->flashMessenger->addMessage(
                array(
                    'message' => sprintf(
                            Zend_Registry::get('Zend_Translate')->_('There is an existing folder with the same name. Folder %s is edited unsuccessfully !'),
                            $folder->title
                    ),
                    'error' => 1
                )
            );
            // redirect after creating folder
            $parent = $folder->getParentFolder();
            if ($parent != NULL) {
                return $this -> _redirectCustom($parent->getHref(array('parent_id' => $this->_parentId, 'parent_type' => $this->_parentType)));
            } else {
                $parent = $folder->getParent();
                return $this->_helper->redirector->gotoRoute(
                    array(
                        'action' => 'manage',
                        'parent_type' => $parent->getType(),
                        'parent_id' => $parent->getIdentity()
                    ),
                    'ynfilesharing_general', true
                );
            }
        } else {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $folder->setFromArray($values);
                $folder->save();

                $tags = preg_split('/[,]+/', $values['tags']);
                $folder->tags()->setTagMaps($this->_viewer, $tags);

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this ->_redirectCustom(
                $folder->getHref(array('parent_id' => $this->_parentId, 'parent_type' => $this->_parentType))
            );
        }
    }

    public function moveAction() {
        $this->view->sourceFolderId = $sourceFolderId = $this->_getParam('folder_id');

        if ($this -> getRequest() -> isPost()) {
            $destFolderId = $this->_getParam('dest_folder_id', 0);
            if (!empty($sourceFolderId)) {
                $sourceFolder = Engine_Api::_()->getItem('folder', $sourceFolderId);
            }
            // TODO [DangTH] : think about the view permission when moving folder
            if (isset($sourceFolder) && is_object($sourceFolder)
                /*&& $sourceFolder->isAllowed($this->_viewer, 'view')*/) {
                $oldPath = $sourceFolder->path;
                $db = Engine_Db_Table::getDefaultAdapter();

                $db->beginTransaction();
                try {
                    $sourceFolder->moveTo($destFolderId);
                    $db->commit();
                } 
                catch (Ynfilesharing_Model_NameException $e) {
                    $db->rollBack();

                    return $this->_forward('success', 'utility', 'core', array(
                        'layout' => 'default-simple',
                        'parentRefresh' => true,
                        'messages' => array(
                            Zend_Registry::get('Zend_Translate')->_('Moved unsuccessfully ! There is a subfolder of the destination folder having the same name the the moved folder')
                        )
                    ));
                } 
                catch (Ynfilesharing_Model_HierachyException $e) {
                    $db->rollBack();

                    return $this->_forward('success', 'utility', 'core', array(
                        'layout' => 'default-simple',
                        'parentRefresh' => true,
                        'messages' => array(
                            Zend_Registry::get('Zend_Translate')->_('Moved unsuccessfully ! The destination folder is one of the subfolders with the folder ') . $sourceFolder->title 
                        )
                    ));
                } 
                catch (Exception $e) {
                    $db->rollBack();
                    var_dump($e);die;
                    if ($e instanceof Ynfilesharing_Model_HierachyException) {
                        return $this->_forward('success', 'utility', 'core', array(
                            'layout' => 'default-simple',
                            'parentRefresh' => false,
                            'messages' => array(
                                Zend_Registry::get('Zend_Translate')->_('Moved unsuccessfully ! The destination folder is one of the subfolders with the folder ') . $sourceFolder->title
                            )
                        ));
                    } else {
                        throw $e;
                    }
                }

                rename($oldPath, $sourceFolder->path);
                
                return $this->_forward('success', 'utility', 'core', array(
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Moved successfully.'))
                ));
            }
        } else {
            if (! $this->_helper->requireUser->isValid ()) {
                return;
            }

            $this->view->parent = $parent = Engine_Api::_()->getItem($this->_parentType, $this->_parentId);
            $this->view->data = array(
                array(
                    'property' => array('name' => $parent->getTitle()),
                    'type' => 'folder',
                    'data' => array(
                        'abs_path' => Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $parent->getGuid(),
                        'parent_type' => $parent->getType(),
                        'parent_id' => $parent->getIdentity()
                    )
                )
            );
        }
    }

    public function uploadAction()
    {
        if (!$this->_helper->requireUser->isValid()){
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('No permission to upload');
        }

        $folderId = $this->_getParam('folder_id');
        if (!is_numeric($folderId)){
            $this->view->error = 1;
            $this->view->message = Zend_Registray::get('Zend_Translate')->_('No folder to upload');
        }
        $folder = Engine_Api::_()->getItem('folder', $folderId);
        $parent = $folder->getParent();
        $topFoler = $folder->getTopFolder();

        $destination = $folder->path;

        if (!is_dir($destination))
        {
            mkdir($destination);
        }
        $adapter=new Zend_File_Transfer_Adapter_Http();
        $adapter->setDestination($destination);

        $fileTbl = new Ynfilesharing_Model_DbTable_Files();
        $db = $fileTbl->getAdapter();

        $user_level = $this->_viewer->level_id;
        $fileExts = Engine_Api::_()->authorization()->getPermission($user_level, 'folder', 'auth_ext');
        if ($fileExts == Authorization_Api_Core::LEVEL_NONBOOLEAN) {
            $fileExts = NULL;
        }
        $fileExts = ($fileExts) ? $fileExts : '*';
        
        $listWrongFormat = '';
        $listWrongSize = '';

        $settings = Engine_Api::_()->getApi('settings', 'core');
        if ($parent->getType() == 'user') 
        {
            $maxSizeKB = (INT)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $this->_viewer, 'usertotal');
            $space_limit = (int) Engine_Api::_()->authorization()->getPermission($this->_viewer->level_id, 'user', 'quota');
            if($space_limit)
            {
                $space_limit = $space_limit/ Ynfilesharing_Plugin_Constants::KILOBYTE;
                if($space_limit < $maxSizeKB || !$maxSizeKB)
                {
                    $maxSizeKB = $space_limit;
                }
            }
        } elseif ($parent->getType() == 'group') 
        {
                $maxSizeKB = $settings->getSetting('ynfilesharing.grouptotal', 0);
        }
	elseif ($parent->getType() == 'event') 
        {
                $maxSizeKB = $settings->getSetting('ynfilesharing.eventtotal', 0);
        }
        elseif ($parent->getType() == 'ynbusinesspages_business') 
        {
                $maxSizeKB = $settings->getSetting('ynfilesharing.businesstotal', 0);
        }
        
        $totalUploaded = Engine_Api::_()->ynfilesharing()->getCurrentFolderSizeOfObject($parent);
        $numberOfFiles = 0;
        foreach ($adapter->getFileInfo() as $info) {
            $totalUploaded += $info['size'];
            if ($info['name'])
                $numberOfFiles++;
        }

        //Checking no file for IE browser (IE using form upload)
        if ($numberOfFiles == 0){
            $this->view->error = 0;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Please select file to upload');
            $this->_returnData($folder);
            return;
        }

        //Checking max file of every user
        $parentObject = Engine_Api::_()->getItem($this->_parentType, $this->_parentId);
        $fileTotal = $fileTbl->countAllFilesBy($parentObject);
        //$maxFileTotal = (INT)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $this->_viewer, 'userfile');
        $maxFileTotal = Engine_Api::_()->ynfilesharing() -> getAllowed('folder', $this->_viewer, 'userfile');
        $choseFileTotal = $numberOfFiles;

        if ( (($fileTotal + $choseFileTotal) > $maxFileTotal) && ($maxFileTotal != 0) ){
            $mess = Zend_Registry::get('Zend_Translate')->_('Max number of files per user is %d the file(s)');
            $this->view->error = 0;
            $this->view->message = sprintf($mess, $maxFileTotal);
            $this->_returnData($folder);
            return;
        }
        //Checking max size of every user/group
        $space_limit = (int) Engine_Api::_()->authorization()->getPermission($this->_viewer->level_id, 'user', 'quota');
        if ($maxSizeKB > 0 || $space_limit > 0) 
        {
            $maxSize = $maxSizeKB * Ynfilesharing_Plugin_Constants::KILOBYTE;
            if ($space_limit > 0 && $space_limit < $totalUploaded) 
            {
                if ($parent->getType() == 'user') 
                {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This user is only allowed to used %d KB');
                } elseif ($parent->getType() == 'group') 
                {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This group is only allowed to used %d KB');
                }
		elseif ($parent->getType() == 'event') 
                {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This event is only allowed to used %d KB');
                }
                elseif ($parent->getType() == 'businesspages_business') 
                {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This business is only allowed to used %d KB');
                }
                $this->view->error = 0;
                $this->view->message = sprintf($mess, $space_limit/1024);
                $this->_returnData($folder);
                return;

            }
            if ($maxSize > 0 && $maxSize < $totalUploaded) 
            {
                if ($parent->getType() == 'user') {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This user is only allowed to used %d KB');
                } elseif ($parent->getType() == 'group') {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This group is only allowed to used %d KB');
                }
		elseif ($parent->getType() == 'event') {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This event is only allowed to used %d KB');
                }
                elseif ($parent->getType() == 'ynbusinesspages_business') 
                {
                    $mess = Zend_Registry::get('Zend_Translate')->_('This business is only allowed to used %d KB');
                }
                $this->view->error = 0;
                $this->view->message = sprintf($mess, $maxSizeKB);
                $this->_returnData($folder);
                return;

            }
        }

        //Checking max size with every folder
        $maxSizeTopFolderKB = $settings->getSetting('ynfilesharing.foldertotal', 0);
        if ($maxSizeTopFolderKB > 0) {
            $maxSizeTopFolder = $maxSizeTopFolderKB * Ynfilesharing_Plugin_Constants::KILOBYTE;
            if ($maxSizeTopFolder < $totalUploaded) {
                $mess = Zend_Registry::get('Zend_Translate')->_('You are only allowed to used %d KB in "%s" folder');
                $this->view->error = 0;
                $this->view->message = sprintf($mess, $maxSizeTopFolderKB, $topFoler->title);
                $this->_returnData($folder);
                return;
            }
        }

        $queuesTbl = Engine_Api::_()->getDbTable("queues","ynfilesharing");
        $maxFileSize = (INT)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $this->_viewer, 'usersize') * Ynfilesharing_Plugin_Constants::KILOBYTE;
        $uploadedFiles = 0;
        foreach ($adapter->getFileInfo() as $info) {
            $fileExtend = Ynfilesharing_Plugin_Utilities::findFileExt($info['name']);
            if (strpos($fileExts, $fileExtend) !== false || $fileExts == "*") {
                if ($info['size'] < $maxFileSize || $maxFileSize == 0){
                    if(!$adapter->receive($info['name'])){
                        continue;
                    }
                    else {
                        $uploadedFiles ++;
                        $values['folder_id'] = $folderId;
                        $values['parent_type'] = $this->_parentType;
                        $values['parent_id'] = $this->_parentId;
                        $values['user_id'] = $this->_viewer->getIdentity();
                        $values['name'] = $info['name'];
                        $values['size'] = $info['size'];
                        $values['ext'] = Ynfilesharing_Plugin_Utilities::findFileExt($info['name']);
                        $values['creation_date'] = date('Y-m-d H:i:s');
                        $values['modified_date'] = $values['creation_date'];

                        $db->beginTransaction();
                        try {
                            $file = $fileTbl->getExistedFile($values['folder_id'], $values['name']);
                            if (is_null($file))
                            {
                                $file = $fileTbl->createRow();
                            }
                            $file->setFromArray($values);
                            $file->save();
                            $db->commit();
                            
                            //add file to queue list for uploading to scribd
//                          if (in_array($values['ext'], Engine_Api::_()->ynfilesharing()->getScribdFileTypes() ))
//                          {
//                              $queuesTbl->insert(array(
//                                      'folder_id' => $folderId,
//                                      'file_id' => $file->getIdentity(),
//                                      'status' => 0
//                              ));
//                          }
                            
                            // Add to jobs
                            Engine_Api::_()->getDbtable('jobs', 'core')->addJob('ynfilesharing_scribd_uploader', array(
                                    'file_id' => $file->getIdentity(),
                            ));
                            
                        } catch (Exception $e) {
                            $db->rollBack();
                            throw $e;
                        }
                    }
                }
                else {
                    $listWrongSize .= $info['name'] . "|";
                }
            }
            else {
                $listWrongFormat .= $info['name'] . "|";
            }
        }

        $this->view->error = 0;
        $this->view->message = ($uploadedFiles > 0) ? (sprintf(
            Zend_Registry::get('Zend_Translate')->_(array("Uploaded %s file successfully", "Uploaded %s files successfully", $uploadedFiles)),
            $uploadedFiles)) : (Zend_Registry::get('Zend_Translate')->_('No files is uploaded'));

        $this->view->wrong_format = $listWrongFormat;
        $this->view->wrong_size = $listWrongSize;

        $this->_returnData($folder);
    }

    private function _returnData($folder)
    {
        if ($this->_getParam('format', NULL) != 'json') {
            $this->_helper->flashMessenger->addMessage(
                array(
                    'message' => $this->view->message,
                    'error' => 1
                )
            );
            $this->_redirectCustom(
                $folder,
                array('parent_id' => $this->_parentId, 'parent_type' => $this->_parentType)
            );
        }
    }

    public function deleteAction() {
        // In smoothbox
        $folderId = $this->_getParam('folder_id', 0);
        $this->view->folder_id = $folderId;

        // Check post
        if( $this->getRequest()->isPost() )
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try
            {
                $folder = Engine_Api::_()->getItem('ynfilesharing_folder', $folderId);
                $parentFolderId = $folder->parent_folder_id;
                if ($folder->isAllowed($this->_viewer, 'delete')) {
                    $folder->delete();
                }
                $db->commit();
            }
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }

            if (isset($parentFolderId) && !empty($parentFolderId)) {
                if ($this->_getParam('parent_redirect', NULL)) {
                    $parentFolder = Engine_Api::_()->getItem('folder', $parentFolderId);
                    return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => false,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->translate('Deleted folder successfully')),
                        'parentRedirect' => $parentFolder->getHref(
                            array('parent_type' => $this->_parentType, 'parent_id' => $this->_parentId)
                        )
                    ));
                }
                return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'messages' => array(Zend_Registry::get('Zend_Translate')->translate('Deleted folder successfully'))
                ));
            } else {
                return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => false,
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(
                        array('action' => 'manage', 'parent_id' => $this->_parentId, 'parent_type' => $this->_parentType),
                        'ynfilesharing_general',
                        true
                    ),
                    'messages' => array(Zend_Registry::get('Zend_Translate')->translate('Deleted folder successfully'))
                ));
            }
        }
    }
}
