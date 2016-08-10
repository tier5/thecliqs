<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_IndexController extends Core_Controller_Action_Standard
{
	protected $_parentType;
	protected $_parentId;
	protected $_viewer;

	public function init()
	{
		$this -> view -> viewer = $this -> _viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _parentType = $this -> _getParam('parent_type', 'user');
		if ($this -> _parentType == 'user')
		{
			$this -> _parentId = $this -> _viewer -> getIdentity();
		}
		else
		{
			$this -> _parentId = $this -> _getParam('parent_id');
			$object = Engine_Api::_() -> getItem($this -> _parentType, $this -> _parentId);
			if (!($object && $object -> membership() -> isMember($this -> _viewer)))
			{
				$this -> _parentType = 'user';
				$this -> _parentId = $this -> _viewer -> getIdentity();
			}
		}

		$this -> view -> parentId = $this -> _parentId;
		$this -> view -> parentType = $this -> _parentType;
	}

	public function indexAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('folder', null, 'view') -> isValid())
			return;

		$filesharingApi = Engine_Api::_() -> ynfilesharing();

		// Get filesharing table
		$file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
		$file_name = $file_table -> info('name');
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');

		// Search Params
		$form = new Ynfilesharing_Form_Search();
		$form -> setAction($this -> view -> baseUrl() . "filesharing/");
		$params = $this -> _getAllParams();
		$files = array();
		$folders = array();
		if (isset($params['type']))
		{
			switch ($params ['type'])
			{
				case 'file' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					break;
				case 'folder' :
					$folders = $filesharingApi -> selectFoldesByOptions($params);
					break;
				case 'all' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					$folders = $filesharingApi -> selectFoldesByOptions($params);
				default :
					break;
			}
		}
		else
		{
			$folders = $filesharingApi -> getSubFolders(NULL, NULL);
		}

		$formFolderId = $this -> _getParam('from_folder_id', 0);
		$limit = Ynfilesharing_Plugin_Constants::DEFAULT_LIMIT;
		$viewedFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer, $formFolderId, $limit);
		if (count($viewedFolders) > $limit)
		{
			$this -> view -> canViewMore = true;
			$this -> view -> lastFolder = array_pop($viewedFolders);
		}
		else
		{
			$this -> view -> canViewMore = false;
		}
		$this -> view -> folders = $viewedFolders;
		$this -> view -> files = $filesharingApi -> getFiles($files, 'view', $this -> _viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders);
		$this -> view -> params = $_GET;

		if (Engine_Api::_() -> authorization() -> isAllowed('folder', $this -> _viewer, 'view'))
		{
			$this -> view -> canCreate = Engine_Api::_() -> authorization() -> isAllowed('folder', $this -> _viewer, 'create');
		}

		// Render
		if ($this -> _getParam('view_more') == true)
		{
			$this -> view -> isViewMore = true;
			$this -> _helper -> layout -> disableLayout();
		}
		else
		{
			$this -> _helper -> content -> setEnabled();
		}
	}

	public function deleteAction()
	{
		if ($this -> getRequest() -> isPost())
		{
			$folderIds = $this -> _getParam('folderIds');
			$fileIds = $this -> _getParam('fileIds');

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				foreach ($folderIds as $folderId)
				{
					$folder = Engine_Api::_() -> getItem('ynfilesharing_folder', $folderId);
					if (isset($folder) && is_object($folder))
					{
						if ($folder -> isAllowed($this -> _viewer, 'delete'))
						{
							$folder -> delete();
						}
					}
				}

				foreach ($fileIds as $fileId)
				{
					$file = Engine_Api::_() -> getItem('ynfilesharing_file', $fileId);
					if (isset($file) && is_object($file))
					{
						$parentFolder = $file -> getParentFolder();
						if ($parentFolder && $parentFolder -> isAllowed($this -> _viewer, 'view'))
						{
							$file -> delete();
						}
					}
				}
				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> translate('Deleted successfully.'))
			));
		}
	}

	private function _moveFolder($sourceFolderId, $destFolderId, $maxSizeKB, $destSize, $parent)
	{
		if (!empty($sourceFolderId))
		{
			$sourceFolder = Engine_Api::_() -> getItem('folder', $sourceFolderId);
		}
		if (intval($sourceFolderId) == intval($destFolderId))
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'parentRefresh' => false,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Cannot move to the folder to itself.'))
			));
		}

		if (isset($sourceFolder) && is_object($sourceFolder))
		{
			$sourceFolderSize = Ynfilesharing_Plugin_Utilities::getFolderSize($sourceFolder -> path);
			$space_limit = (int)Engine_Api::_() -> authorization() -> getPermission($this -> _viewer -> level_id, 'user', 'quota');
			if ($space_limit > 0 && $destSize + $sourceFolderSize > $space_limit)
			{
				if ($parent -> getType() == 'user')
				{
					$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this user is allowed to store %d KB');
				}
				elseif ($parent -> getType() == 'group')
				{
					$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this group is allowed to store %d KB');
				}
				elseif ($parent -> getType() == 'event')
                {
                    $mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this event is allowed to store %d KB');
                }
                elseif ($parent -> getType() == 'ynbusinesspages_business')
                {
                    $mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this business is allowed to store %d KB');
                }

				return $this -> _forward('success', 'utility', 'core', array(
					'layout' => 'default-simple',
					'parentRefresh' => true,
					'messages' => array(sprintf($mess, $space_limit / 1024))
				));
			}
			if ($maxSizeKB > 0 && $destSize + $sourceFolderSize > $maxSizeKB * Ynfilesharing_Plugin_Constants::KILOBYTE)
			{
				if ($parent -> getType() == 'user')
				{
					$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this user is allowed to store %d KB');
				}
				elseif ($parent -> getType() == 'group')
				{
					$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this group is allowed to store %d KB');
				}
				elseif ($parent -> getType() == 'event')
                {
                    $mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this event is allowed to store %d KB');
                }
                elseif ($parent -> getType() == 'ynbusinesspages_business')
                {
                    $mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this business is allowed to store %d KB');
                }

				return $this -> _forward('success', 'utility', 'core', array(
					'layout' => 'default-simple',
					'parentRefresh' => true,
					'messages' => array(sprintf($mess, $maxSizeKB))
				));
			}

			$db = Engine_Db_Table::getDefaultAdapter();

			$db -> beginTransaction();
			try
			{
				$sourceFolder -> moveTo($destFolderId);
				$db -> commit();
			}
			catch (Ynfilesharing_Model_NameException $e)
			{
				$db -> rollBack();
				return $this -> _forward('success', 'utility', 'core', array(
					'layout' => 'default-simple',
					'messages' => array($e -> getMessage())
				));
			}
			catch (Ynfilesharing_Model_HierachyException $e)
			{
				$db -> rollBack();

				return $this -> _forward('success', 'utility', 'core', array(
					'layout' => 'default-simple',
					'parentRefresh' => false,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Moved unsuccessfully ! The destination folder is one of the subfolders with the folder ') . $sourceFolder -> title)
				));
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Moved successfully.'))
			));
		}
	}

	private function _moveFoldersAndFiles($folderIds, $fileIds, $destFolderId, $maxSizeKB, $destSize, $parent)
	{
		$folders = array();
		$files = array();

		if (!empty($folderIds))
		{
			$folderTbl = new Ynfilesharing_Model_DbTable_Folders();
			$folderSelect = $folderTbl -> select() -> where('folder_id IN (?)', $folderIds);
			$folders = $folderTbl -> fetchAll($folderSelect);
		}

		if (!empty($fileIds))
		{
			$fileTbl = new Ynfilesharing_Model_DbTable_Files();
			$fileSelect = $fileTbl -> select() -> where('file_id IN (?)', $fileIds);
			$files = $fileTbl -> fetchAll($fileSelect);
		}

		$sourceSize = 0;
		foreach ($folders as $folder)
		{
			$sourceSize = $sourceSize + Ynfilesharing_Plugin_Utilities::getFolderSize($folder -> path);
		}
		foreach ($files as $file)
		{
			$sourceSize = $sourceSize + $file -> size;
		}

		if ($maxSizeKB > 0 && $destSize + $sourceSize > $maxSizeKB * Ynfilesharing_Plugin_Constants::KILOBYTE)
		{
			if ($parent -> getType() == 'user')
			{
				$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this user is allowed to store %d KB.');
			}
			elseif ($parent -> getType() == 'group')
			{
				$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this group is allowed to store %d KB');
			}
			elseif ($parent -> getType() == 'event')
			{
				$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this event is allowed to store %d KB');
			}
            elseif ($parent -> getType() == 'ynbusinesspages_business')
            {
                $mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this business is allowed to store %d KB');
            }

			return $this -> _forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'parentRefresh' => true,
				'messages' => array(sprintf($mess, $maxSizeKB))
			));
		}

		$db = Engine_Db_Table::getDefaultAdapter();

		$db -> beginTransaction();
		try
		{
			foreach ($folders as $folder)
			{
				$folder -> moveTo($destFolderId);
			}
			foreach ($files as $file)
			{
				$file -> moveTo($destFolderId);
			}
			$db -> commit();
		}
		catch (Ynfilesharing_Model_NameException $e)
		{
			$db -> rollBack();
			return $this -> _forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'messages' => array($e -> getMessage())
			));
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'layout' => 'default-simple',
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Moved successfully.'))
		));
	}

	private function _moveFile($fileId, $destFolderId, $maxSizeKB, $destSize, $parent)
	{
		if (!empty($fileId))
		{
			$file = Engine_Api::_() -> getItem('ynfilesharing_file', $fileId);
		}
		if (isset($file) && is_object($file))
		{
			$sourceFileSize = $file -> size;
			if ($maxSizeKB > 0 && $destSize + $sourceFileSize > $maxSizeKB * Ynfilesharing_Plugin_Constants::KILOBYTE)
			{
				if ($parent -> getType() == 'user')
				{
					$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this user is allowed to store %d KB.');
				}
				elseif ($parent -> getType() == 'group')
				{
					$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this group is allowed to store %d KB.');
				}
				elseif ($parent -> getType() == 'event')
				{
					$mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this event is allowed to store %d KB.');
				}
                elseif ($parent -> getType() == 'ynbusinesspages_business')
                {
                    $mess = Zend_Registry::get('Zend_Translate') -> _('Cannot move to this folder, because this business is allowed to store %d KB.');
                }

				return $this -> _forward('success', 'utility', 'core', array(
					'layout' => 'default-simple',
					'parentRefresh' => true,
					'messages' => array(sprintf($mess, $maxSizeKB))
				));
			}

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$file -> moveTo($destFolderId);
				$db -> commit();
			}
			catch (Ynfilesharing_Model_NameException $e)
			{
				$db -> rollBack();
				return $this -> _forward('success', 'utility', 'core', array(
					'layout' => 'default-simple',
					'messages' => array($e -> getMessage())
				));
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
			return $this -> _forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Moved successfully.'))
			));
		}
	}

	public function moveAction()
	{
		$destFolderId = $this -> _getParam('dest_folder_id', 0);
		$folderIds = $this -> _getParam('folderIds');
		$fileIds = $this -> _getParam('fileIds');
		if ($this -> getRequest() -> isPost())
		{
			$settings = Engine_Api::_() -> getApi('settings', 'core');

			// get the settings of user total and group total (max size of a group or a user)
			if ($destFolderId != 0)
			{
				$destFolder = Engine_Api::_() -> getItem('folder', $destFolderId);
				$parent = $destFolder -> getParent();
			}
			else
			{
				$parent = Engine_Api::_() -> getItem($this -> _parentType, $this -> _parentId);
			}
			$destSize = Engine_Api::_() -> ynfilesharing() -> getCurrentFolderSizeOfObject($parent);

			// check the max size of a group or a user is allowed to store
			if ($parent -> getType() == 'user')
			{
				$maxSizeKB = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'usertotal');
			}
			elseif ($parent -> getType() == 'group')
			{
				$maxSizeKB = $settings -> getSetting('ynfilesharing.grouptotal', 0);
			}
			elseif ($parent -> getType() == 'event')
			{
				$maxSizeKB = $settings -> getSetting('ynfilesharing.eventtotal', 0);
			}
            elseif ($parent -> getType() == 'ynbusinesspages_business')
            {
                $maxSizeKB = $settings -> getSetting('ynfilesharing.businesstotal', 0);
            }

			if ($folderIds == NULL && $fileIds == NULL)
			{
				$this -> view -> sourceFolderId = $sourceFolderId = $this -> _getParam('folder_id');
				$this -> view -> fileId = $fileId = $this -> _getParam('file_id');

				if (isset($sourceFolderId) && $sourceFolderId != NULL)
				{
					$this -> _moveFolder($sourceFolderId, $destFolderId, $maxSizeKB, $destSize, $parent);
				}
				if (isset($fileId) && $fileId != NULL)
				{
					$this -> _moveFile($fileId, $destFolderId, $maxSizeKB, $destSize, $parent);
				}
			}
			else
			{
				$this -> _moveFoldersAndFiles($folderIds, $fileIds, $destFolderId, $maxSizeKB, $destSize, $parent);
			}
		}
		else
		{
			if (!$this -> _helper -> requireUser -> isValid())
			{
				return;
			}

			$parentId = $this -> _getParam('parent_id');
			$parentType = $this -> _getParam('parent_type', 'user');
			if (empty($parentType))
			{
				$parentType = 'user';
			}
			if (empty($parentId) && $parentType == 'user')
			{
				$parentId = $this -> _viewer -> getIdentity();
			}
			$this -> view -> parent = $parent = Engine_Api::_() -> getItem($parentType, $parentId);
			$this -> view -> data = array( array(
					'property' => array('name' => $this -> view -> string() -> truncate($parent -> getTitle(), 50)),
					'type' => 'folder',
					'data' => array(
						'abs_path' => Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $parent -> getGuid(),
						'parent_type' => $parent -> getType(),
						'parent_id' => $parent -> getIdentity()
					)
				));
		}
	}

	public function manageAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		$messages = $this -> _helper -> flashMessenger -> getMessages();
		if (count($messages))
		{
			$message = current($messages);
			$this -> view -> messages = array($message['message']);
			$this -> view -> error = $message['error'];
		}

		$parent = Engine_Api::_() -> getItem($this -> _parentType, $this -> _parentId);

		$filesharingApi = Engine_Api::_() -> ynfilesharing();

		// Get filesharing table
		$file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
		$file_name = $file_table -> info('name');
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');

		// Search Params
		$form = new Ynfilesharing_Form_Search();
		$form -> setAction($this -> view -> baseUrl() . "/filesharing/index/manage");
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$params['parent_type'] = $this -> _parentType;
		$params['parent_id'] = $this -> _parentId;
		$files = array();
		$folders = array();
		if (isset($params['type']))
		{
			switch ($params ['type'])
			{
				case 'file' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					break;
				case 'folder' :
					$folders = $filesharingApi -> selectFoldesByOptions($params);
					break;
				case 'all' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					$folders = $filesharingApi -> selectFoldesByOptions($params);
				default :
					break;
			}
		}
		else
		{
			$folders = $filesharingApi -> getSubFolders(NULL, $parent);
		}

		$this -> view -> files = $files;
		$this -> view -> subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders, $this -> _viewer);
		$totalUploaded = Engine_Api::_()->ynfilesharing()->getCurrentFolderSizeOfObject($parent);
		$totalUploaded = number_format($totalUploaded/1048576, 2);
		$this -> view ->totalUploaded = $totalUploaded;
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
		$maxSizeKB = number_format($maxSizeKB/1024,2);
		$this -> view -> maxSizeKB = $maxSizeKB;
		if (Engine_Api::_() -> authorization() -> isAllowed('folder', $this -> _viewer, 'view'))
		{
			$this -> view -> canCreate = Engine_Api::_() -> authorization() -> isAllowed('folder', $this -> _viewer, 'create');
		}

		$this -> _helper -> content -> setEnabled();
	}

	public function browseAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		$folderId = $this -> _getParam('folder_id', 0);
		if ($folderId != 0)
		{
			$folder = Engine_Api::_() -> getItem('folder', $folderId);
		}

		$folderOnly = $this -> _getParam('folder_only');

		$filesharingApi = Engine_Api::_() -> ynfilesharing();
		if (isset($folder))
		{
			$folders = $filesharingApi -> getSubFolders($folder);

			if (!$folderOnly)
			{
				$files = $filesharingApi -> getFilesInFolder($folder);
			}
		}
		else
		{
			$parentId = $this -> _getParam('parent_id');
			$parentType = $this -> _getParam('parent_type', 'user');
			if (empty($parentId) && $parentType == 'user')
			{
				$parentId = $this -> _viewer -> getIdentity();
			}
			$parent = Engine_Api::_() -> getItem($parentType, $parentId);
			$folders = $filesharingApi -> getSubFolders(NULL, $parent);
		}
		$subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);

		$data = array();
		foreach ($subFolders as $f)
		{
			$d = array(
				'property' => array('name' => $this -> view -> string() -> truncate($f -> title, 50)),
				'type' => 'folder',
				'data' => array(
					'abs_path' => $f -> path,
					'id' => $f -> getIdentity()
				)
			);
			array_push($data, $d);
		}

		if (isset($files))
		{
			foreach ($files as $fi)
			{
				$d = array(
					'property' => array('name' => $fi -> name),
					'type' => 'file',
					'data' => array('id' => $fi -> getIdentity())
				);
				array_push($data, $d);
			}
		}

		return $this -> _helper -> json($data);
	}

	public function browseByTreeAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		$parentId = $this -> _getParam('parent_id');
		$parentType = $this -> _getParam('parent_type', 'user');
		if (empty($parentId) && $parentType == 'user')
		{
			$parentId = $this -> _viewer -> getIdentity();
		}
		$this -> view -> parent = $parent = Engine_Api::_() -> getItem($parentType, $parentId);
		$this -> view -> data = array( array(
				'property' => array('name' => $parent -> getTitle()),
				'type' => 'folder',
				'data' => array(
					'abs_path' => Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $parent -> getGuid(),
					'parent_type' => $parent -> getType(),
					'parent_id' => $parent -> getIdentity()
				)
			));
	}

	public function shareAction()
	{
		$this -> view -> sourceFolderId = $sourceFolderId = $this -> _getParam('folder_id');
		$this -> view -> fileId = $fileId = $this -> _getParam('file_id');
		$this -> view -> base_url = $baseUrl = Ynfilesharing_Plugin_Utilities::getBaseUrl();
		if (isset($sourceFolderId) && $sourceFolderId != NULL)
		{
			if (!empty($sourceFolderId))
			{
				$sourceFolder = Engine_Api::_() -> getItem('folder', $sourceFolderId);
				$this -> view -> object_type = $objectType = 'folder';
				if ($sourceFolder -> share_code == NULL || $sourceFolder -> share_code == '')
				{
					$code = Ynfilesharing_Plugin_Utilities::random_gen(10);
					$sourceFolder -> share_code = $code;
					$sourceFolder -> save();
				}
				else
				{
					$code = $sourceFolder -> share_code;
				}
				$this -> view -> code = $code;
				$this -> view -> object_id = $sourceFolderId;
			}
		}
		else
		if (isset($fileId) && $fileId != NULL)
		{
			if (!empty($fileId))
			{
				$file = Engine_Api::_() -> getItem('file', $fileId);
				$this -> view -> object_type = $objectType = 'file';
				if ($file -> share_code == NULL || $file -> share_code == '')
				{
					$code = Ynfilesharing_Plugin_Utilities::random_gen(10);
					$file -> share_code = $code;
					$file -> save();
				}
				else
				{
					$code = $file -> share_code;
				}
				$this -> view -> code = $code;
				$this -> view -> object_id = $fileId;
			}
		}
	}

	public function shareviewAction()
	{
		// Get navigation
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynfilesharing_main', array(),'ynfilesharing_main_link');
		
		$objectType = $this -> _getParam('object_type');
		$objectId = $this -> _getParam('object_id');
		$shareCode = $this -> _getParam('code');
		if ($objectType == 'file')
		{
			$file = Engine_Api::_() -> getItem("file", $objectId);
			if (($file -> share_code == $shareCode) || $file -> canViewByShareCode($shareCode))
			{
				$this -> shareviewFile($file);
				$this -> renderScript("index/shareview_file.tpl");
			}
			else
			{
				$this -> renderScript("index/error_file.tpl");
			}
		}
		else
		if ($objectType == 'folder')
		{
			$folder = Engine_Api::_() -> getItem("folder", $objectId);
			if (($folder -> share_code == $shareCode) || $folder -> canViewByShareCode($shareCode))
			{
				$this -> shareviewFolder($folder);
				$this -> renderScript("index/shareview_folder.tpl");
			}
			else
			{
					$this -> renderScript("index/error_file.tpl");
			}
		}
	}

	public function shareviewFile($file)
	{
		$headScript = new Zend_View_Helper_HeadScript();
		$headScript -> appendFile('application/modules/Ynfilesharing/externals/scripts/scribd_api.js');
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$fileId = $file -> file_id;
		if ($fileId != 0)
		{
			$folder = Engine_Api::_() -> getItem('folder', $file -> folder_id);
		}
		if ($file)
		{
			Engine_Api::_() -> core() -> setSubject($file);
		}
		$is_success = 1;
		$status = 'PROCESSING';
		// get settings
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$scribd_api_key = $settings -> getSetting('ynfilesharing.apikey');
		$scribd_secret = $settings -> getSetting('ynfilesharing.apisecret');
		$this -> view -> mode = $settings -> getSetting('ynfilesharing.mode', 'list');
		$this -> view -> width = $settings -> getSetting('ynfilesharing.width', 'auto');
		$this -> view -> height = $settings -> getSetting('ynfilesharing.height', 'auto');
		$this -> view -> is_embed = 1;
		$this -> view -> is_support = 1;
		$this -> view -> is_image = 1;
		$this -> view -> status = $status;
		$this -> view -> is_success = $is_success;
		$this -> view -> file = $file;
		$this -> view -> folder = $folder;

		$file_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $folder -> path . $file -> name;
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		if (!$folder -> isAllowed($viewer, 'view'))
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		// Increase view count
		$file -> view_count += 1;
		$file -> save();

		$arr_ext = array(
			'doc',
			'docx',
			'pdf',
			'xls',
			'xlsx',
			'txt',
			'rtf',
			'ps',
			'pps',
			'ppt',
			'pptx',
			'odt',
			'sxw',
			'odp',
			'sxi',
			'ods',
			'sxc',
			'fodt',
			'fods',
			'fodp',
			'odb',
			'odg',
			'fodg',
			'odf',
			'odt',
			'ods',
			'odp'
		);
		if (!in_array($file -> ext, $arr_ext))
		{
			$this -> view -> is_support = 0;
			$arr_img = array(
				'tif',
				'jpg',
				'png',
				'bmp'
			);
			if (in_array($file -> ext, $arr_img))
			{
				$this -> view -> is_image = 0;
				$path = str_replace(DIRECTORY_SEPARATOR, '/', $folder -> path);
				$this -> view -> image = $this -> view -> baseUrl() . '/' . $path . $file -> name;
			}
		}
		else
		{
			if ($scribd_api_key == null || $scribd_secret == null)
			{
				$this -> view -> is_embed = 0;
				return;
			}
			$doc_type = null;
			$access = 'private';
			$rev_id = null;
			$scribd = new Scribd($scribd_api_key, $scribd_secret);
			try
			{
				$db = Engine_Api::_() -> getDbtable('documents', 'ynfilesharing') -> getAdapter();
				$is_uploaded = Engine_Api::_() -> getDbtable('documents', 'ynfilesharing') -> checkFileUploaded($file -> getIdentity());
				if (!$is_uploaded)
				{
					$data = $scribd -> upload($file_path, $doc_type, $access, $rev_id);
					if (is_array($data))
					{
						$tbl_documents = Engine_Api::_() -> getDbtable('documents', 'ynfilesharing');
						// $db->beginTransaction ();
						$row = $tbl_documents -> createRow();
						$row -> document_id = $file -> getIdentity();
						$row -> doc_id = $data['doc_id'];
						$row -> access_key = $data['access_key'];
						if ($data['secret_password'])
						{
							$row -> secret_password = $data['secret_password'];
						}
						$row -> save();
						$is_success = 1;
						$status = $scribd -> getConversionStatus($data['doc_id']);
					}
				}

				$document = Engine_Api::_() -> getItem('ynfilesharing_document', $file -> getIdentity());
				// file is existed in database
				if ($is_uploaded)
				{
					// check if file is existed on Scribd
					$data = $scribd -> getSettings($document -> doc_id);
					// $db->beginTransaction ();
					if (!is_array($data))
					{
						$data = $scribd -> upload($file_path, $doc_type, $access, $rev_id);
						if (is_array($data))
						{
							$document -> doc_id = $data['doc_id'];
							$document -> access_key = $data['access_key'];
							if ($data['secret_password'])
							{
								$document -> secret_password = $data['secret_password'];
							}
							$document -> save();
							$is_success = 1;
						}
					}
				}

				$status = $scribd -> getConversionStatus($document -> doc_id);
				$this -> view -> data = $document -> toArray();
			}
			catch ( exception $ex )
			{
				$is_success = 0;
			}
			$this -> view -> status = $status;
			$this -> view -> is_success = $is_success;
		}
	}

	public function shareviewFolder($folder)
	{
		$fileTbl = new Ynfilesharing_Model_DbTable_Files();
		$folderName = $this -> _viewer -> getGuid();
		$this -> view -> folder = $folder;
		$this -> view -> code = $this -> _getParam('code');
		$this -> view -> base_url = $baseUrl = Ynfilesharing_Plugin_Utilities::getBaseUrl();
		if ($folder)
		{
			Engine_Api::_() -> core() -> setSubject($folder);
		}
		if (!$this -> _helper -> requireSubject('folder') -> isValid())
		{
			return;
		}
		if (!$folder -> isAllowed($this -> _viewer, 'view'))
		{
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
		foreach ($folders as $f)
		{
			array_push($foldersArr, $f);
		}
		array_push($foldersArr, $folder);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($foldersArr);

		// Get filesharing table
		$file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
		$file_name = $file_table -> info('name');
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');

		$files = array();
		$folders = array();

		$folders = $filesharingApi -> getSubFolders($folder);
		$this -> view -> files = $filesharingApi -> getFilesInFolder($folder);

		$this -> view -> subFolders = $subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders, 'view', $this -> _viewer);
	}

}
