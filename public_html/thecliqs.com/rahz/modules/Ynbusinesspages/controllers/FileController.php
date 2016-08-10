<?php
class Ynbusinesspages_FileController extends Core_Controller_Action_Standard {
	protected $_parentType;
	protected $_parentId;
	protected $_viewer;

	public function init() 
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> hasModuleBootstrap('ynfilesharing'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> viewer = $this -> _viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _parentType = "ynbusinesspages_business";

		if (!Engine_Api::_() -> core() -> hasSubject()) 
		{
			if ((0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id))))
			{
				Engine_Api::_() -> core() -> setSubject($business);
				$this -> _parentId = $business_id;
			}

		} else 
		{
			$business = Engine_Api::_() -> core() -> getSubject('ynbusinesspages_business');
			$this -> _parentId = $business -> getIdentity();
		}

		$this -> view -> parentId = $this -> _parentId;	
		$this -> view -> parentType = $this -> _parentType;

		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynfilesharing_folder')) {
			return $this -> _helper -> requireAuth -> forward();
		}
	}

	public function listAction() 
	{
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject('ynbusinesspages_business');
		//check auth create
		$canCreate = $business -> isAllowed('file_create');
		$this -> view -> canCreate = $canCreate;
		//check both auth remove folder and auth delete file
		$canDeleteRemove = $business -> isAllowed('file_delete');
		$this -> view -> canDeleteRemove = $canDeleteRemove;

		$messages = $this -> _helper -> flashMessenger -> getMessages();
		if (count($messages)) {
			$message = current($messages);
			$this -> view -> messages = array($message['message']);
			$this -> view -> error = $message['error'];
		}
		$parent = $business;
		$filesharingApi = Engine_Api::_() -> ynfilesharing();
		// Search Params
		$form = new Ynbusinesspages_Form_File_Search();
		$this -> view -> form = $form;
		$form -> setAction($this -> view -> url(array('controller' => 'file', 'action' => 'list', 'business_id' => $business -> getIdentity()), 'ynbusinesspages_extended', true));
		$params = $this -> getAllParams();
		if($params['type'] == 'folder')
		{
			unset($params['orderby']);
		}
		$form -> isValid($params);
		
		$params = $form -> getValues();
		$params['parent_type'] = $this -> _parentType;
		$params['parent_id'] = $this -> _parentId;
		$folders = $files = array();
		if (isset($params['type'])) 
		{
			switch ($params ['type']) {
				case 'file' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					break;
				case 'folder' :
					$folders = $filesharingApi -> selectFoldesByOptions($params);
					break;
				case 'all' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					$folders = $filesharingApi -> selectFoldesByOptions($params);
			}
		} else 
		{
			$folders = $filesharingApi -> getSubFolders(NULL, $parent);
		}

		$this -> view -> files = $files;
		$this -> view -> subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders, $this -> _viewer);
		$totalUploaded = Engine_Api::_() -> ynfilesharing() -> getCurrentFolderSizeOfObject($parent);
		$totalUploaded = number_format($totalUploaded / 1048576, 2);
		$this -> view -> totalUploaded = $totalUploaded;
		$maxSizeKB = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'usertotal');
		$space_limit = 0;
		if($this -> _viewer -> getIdentity())
			$space_limit = (int)Engine_Api::_() -> authorization() -> getPermission($this -> _viewer -> level_id, 'user', 'quota');
		if ($space_limit && $space_limit < $maxSizeKB) {
			$maxSizeKB = $space_limit;
		}
		$maxSizeKB = number_format($maxSizeKB / 1024, 2);
		$this -> view -> maxSizeKB = $maxSizeKB;
	}

	public function viewFolderAction() 
	{
		$folderId = $this -> _getParam('folder_id', 0);
		if ($folderId != 0) {
			$this -> view -> folder = $folder = Engine_Api::_() -> getItem('folder', $folderId);
		}
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject('ynbusinesspages_business');
		
		//check auth create
		$canCreate = $business -> isAllowed('file_create');
		$this -> view -> canCreate = $canCreate;
		//check both auth remove folder and auth delete file
		$canDeleteRemove = $business -> isAllowed('file_delete', null, $folder);
		$this -> view -> canDeleteRemove = $canDeleteRemove;
		$this -> view -> canUpload = $canDeleteRemove;
		// check download
		$this -> view -> canDownload = true;
		$this -> view -> canDelete = $canDelete = $this -> view -> canDeleteRemove;

		$fileTbl = Engine_Api::_() -> getDbTable('files', 'ynfilesharing');

		$parentObject = $business;
		$this -> view -> fileTotal = $fileTotal = $fileTbl -> countAllFilesBy($parentObject);
		$this -> view -> maxFileTotal = $maxFileTotal = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'userfile');
		$folderName = $this -> _viewer -> getGuid();
		$this -> view -> totalSizePerUser = $totalSizePerUser = Ynfilesharing_Plugin_Utilities::getFolderSize(Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR);
		$quota = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'usertotal');
		$this -> view -> maxTotalSizePerUser = $maxTotalSizePerUser = $quota * 1024;

		if ($folder) 
		{
			if(Engine_Api::_() -> core() -> hasSubject('ynbusinesspages_business'))
				Engine_Api::_()->core()->clearSubject('ynbusinesspages_business');
			Engine_Api::_() -> core() -> setSubject($folder);
		}

		if (!$this -> _helper -> requireSubject('folder') -> isValid()) 
		{
			return;
		}

		if (!$folder -> isAllowed($this -> _viewer, 'view')) {
			return $this -> _helper -> requireAuth() -> forward();
		}

		// increase the view count
		$folder -> view_count = $folder -> view_count + 1;
		$this -> view -> folderTags = $folder -> tags() -> getTagMaps();
		$folder -> save();

		$filesharingApi = Engine_Api::_() -> ynfilesharing();
		$folders = $filesharingApi -> getSubFolders($folder);

		$this -> view -> subFolders = $subFolders = $filesharingApi -> getFolders($folders);
		$this -> view -> files = $filesharingApi -> getFilesInFolder($folder);
		$foldersArr = array();
		foreach ($folders as $f) {
			array_push($foldersArr, $f);
		}
		array_push($foldersArr, $folder);

		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($foldersArr);
		$this -> view -> canEdit = $this -> view -> canEditPerm = $business -> isAllowed('file_delete', null, $folder);
	}
}
?>
