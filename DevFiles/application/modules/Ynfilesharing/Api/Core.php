<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
class Ynfilesharing_Api_Core extends Core_Api_Abstract
{
	protected $_isMobile = null;
	public function getItemTable($type)
	{
		if ($type == 'file')
		{
			return Engine_Loader::getInstance() -> load('Ynfilesharing_Model_DbTable_Files');
		}
		else
		{
			$class = Engine_Api::_() -> getItemTableClass($type);
			return Engine_Api::_() -> loadClass($class);
		}
	}

	public function getDirectorySize($directory)
	{
		$dirSize = 0;

		if (!$dh = opendir($directory))
		{
			return false;
		}

		while ($file = readdir($dh))
		{
			if ($file == "." || $file == "..")
			{
				continue;
			}

			if (is_file($directory . DIRECTORY_SEPARATOR . $file))
			{
				$dirSize += filesize($directory . DIRECTORY_SEPARATOR . $file);
			}

			if (is_dir($directory . DIRECTORY_SEPARATOR . $file))
			{
				$dirSize += $this -> getDirectorySize($directory . DIRECTORY_SEPARATOR . $file);
			}
		}

		closedir($dh);

		return $dirSize;
	}

	public function getCurrentFolderSizeOfObject($object)
	{
		$folder = Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $object -> getGuid();
		return Ynfilesharing_Plugin_Utilities::getFolderSize($folder);
	}

	public function getSubFolders($folder = NULL, $parent = NULL)
	{
		$folderTbl = new Ynfilesharing_Model_DbTable_Folders();
		$folderSelect = $folderTbl -> select();

		if ($parent != NULL)
		{
			$folderSelect -> where('parent_id = ?', $parent -> getIdentity());
			$folderSelect -> where('parent_type = ?', $parent -> getType());
		}
		if ($folder != NULL)
		{
			$folderSelect -> where('parent_folder_id = ?', $folder -> getIdentity());
		}
		else
		{
			$folderSelect -> where('parent_folder_id = 0');
		}

		$subFolders = $folderTbl -> fetchAll($folderSelect);

		return $subFolders;
	}

	public function getFilesByParent($parent = NULL)
	{
		$fileTbl = new Ynfilesharing_Model_DbTable_Files();
		$fileSelect = $fileTbl -> select();
		if ($parent != NULL)
		{
			$fileSelect -> where('parent_id = ?', $parent -> getIdentity());
			$fileSelect -> where('parent_type = ?', $parent -> getType());
		}
		$files = $fileTbl -> fetchAll($fileSelect);
		return $files;
	}

	public function getFolders($folders, $action = 'view', $viewer = NULL, $from = 0, $limit = NULL)
	{
		if ($viewer == NULL)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subFolders = array();
		foreach ($folders as $folder)
		{
			if ($folder -> getIdentity() < $from)
			{
				continue;
			}
			else
			{
				if ($folder -> isAllowed($viewer, $action))
				{
					array_push($subFolders, $folder);
					if ($limit != NULL && count($subFolders) > $limit)
					{
						break;
					}
				}
			}
		}

		return $subFolders;
	}

	public function getFilesInFolder($folder = NULL)
	{
		$fileTbl = new Ynfilesharing_Model_DbTable_Files();
		$fileSelect = $fileTbl -> select();

		if ($folder != NULL)
		{
			$fileSelect -> where('folder_id = ?', $folder -> getIdentity());
		}
		else
		{
			$fileSelect -> where('folder_id = 0');
		}

		$files = $fileTbl -> fetchAll($fileSelect);

		return $files;
	}

	public function getFoldersPermissions($folders, $viewer = NULL)
	{
		if ($viewer == NULL)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$foldersPermissions = array();
		foreach ($folders as $folder)
		{
			$permissions = array();
			if ($folder -> isAllowed($viewer, 'edit'))
			{
				array_push($permissions, 'edit');
			}
			if ($folder -> isAllowed($viewer, 'delete'))
			{
				array_push($permissions, 'delete');
			}
			$foldersPermissions[$folder -> getIdentity()] = $permissions;
		}
		return $foldersPermissions;
	}

	public function selectFilesByOptions($params = array())
	{
		// Get filesharing table
		$file_table = Engine_Api::_() -> getItemTable('ynfilesharing_file');
		$file_name = $file_table -> info('name');
		// Get Tagmaps table
		$tags_table = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tags_name = $tags_table -> info('name');
		// Get Search table
		$search_table = Engine_Api::_() -> getDbtable('search', 'core');
		$search_name = $search_table -> info('name');

		$select = $file_table -> select() -> from($file_name);

		if (!empty($params['search']))
		{
			$select -> where("name LIKE ?", '%' . $params['search'] . '%');
		}
		if (!empty($params['parent_type']) && !empty($params['parent_id']))
		{
			$select -> where("$file_name.parent_id = {$params ['parent_id']} and $file_name.parent_type = '{$params ['parent_type']}'");
		}
		if (!empty($params['folder_id']))
		{
			$select -> where("folder_id = ?", $params['folder_id']);
		}
		if (!empty($params['orderby']))
		{
			$select -> order("{$params['orderby']}  DESC");
		}
		return $file_table -> fetchAll($select);
	}

	/**
	 *
	 * @param $permission_files file
	 *        	before check permission
	 * @param
	 *        	$action
	 * @param
	 *        	$viewer
	 * @return multitype:
	 */
	public function getFiles($permission_files, $action = 'view', $viewer = NULL)
	{
		if ($viewer == NULL)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$files = array();
		foreach ($permission_files as $file)
		{
			$folder = $file -> getParentFolder();
			if ($folder -> isAllowed($viewer, $action))
			{
				array_push($files, $file);
			}
		}

		return $files;
	}

	public function selectFoldesByOptions($params = array())
	{
		// Get filesharing table
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');
		// Get Tagmaps table
		$tags_table = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tags_name = $tags_table -> info('name');
		// Get Search table
		$search_table = Engine_Api::_() -> getDbtable('search', 'core');
		$search_name = $search_table -> info('name');

		$text = '%' . preg_replace('/\s+/', '%', $params['search']) . '%';
		$select = $folder_table -> select() -> from($folder_name);

		if (!empty($params['search']))
		{
			$select -> where("($folder_name.title LIKE ?", $text);
			$select -> orWhere("$folder_name.folder_id IN ( SELECT $search_name.id FROM $search_name WHERE $search_name.type = 'folder' AND $search_name.keywords like ?))", $text);
		}
		if (!empty($params['parent_type']) && !empty($params['parent_id']))
		{
			$select -> where("$folder_name.parent_id = {$params ['parent_id']} and $folder_name.parent_type = '{$params ['parent_type']}'");
		}
		if (!empty($params['folder_id']))
		{
			$select -> where("$folder_name.folder_id = ?", $params['folder_id']);
		}
		if (!empty($params['tag']))
		{
			$select -> setIntegrityCheck(false) -> distinct() -> joinLeft($tags_name, "$tags_name.resource_id = $folder_name.folder_id") -> where($tags_name . '.resource_type = ?', 'folder') -> where($tags_name . '.tag_id = ?', $params['tag']);
		}
		if (!empty($params['orderby']) && $params['orderby'] != 'download_count')
		{
			if ($params['orderby'] == 'creation_date')
			{
				$select -> order("$folder_name.modified_date DESC");
			}
			else
			{
				$select -> order("{$params['orderby']}  DESC");
			}
		}
		//echo $select;
		return $folder_table -> fetchAll($select);
	}

	public function getScribdFileTypes()
	{
		return array(
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
	}

	function isMobile()
	{
		if (null === $this -> _isMobile)
		{
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
				if (preg_match('/(android|iphone|ipad|mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i', $user_agent))
				{
					$this -> _isMobile = true;
				}

			}
			else
			{
				$this -> _isMobile = false;
			}

		}
		return $this -> _isMobile;
	}

	public function getAllowed($resource, $role, $action)
	{
		$value = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed($resource, $role -> level_id, $action);
		if (is_null($value) || empty($value)){
			$permissionTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
			$select = $permissionTable->select()
			->where('level_id = ?', $role -> level_id)
      		->where('type = ?', $resource)
      		->where('name = ?', $action)
      		->limit(1);
      		
      		$row = $permissionTable -> fetchRow($select);
      		
      		if ( $row -> value == '3' && (is_null($row ->params) || empty($row -> params)) )
      		{
      			return 3;
      		}
			if ( $row -> value == '5' && (is_null($row ->params) || empty($row -> params)) )
      		{
      			return 5;
      		}	
		}
		return $value;
  	}

}
