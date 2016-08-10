<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Model_Folder extends Core_Model_Item_Abstract {
	protected $_type = 'folder';

	private $_parentFolders = array();
	// Interfaces
	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	* */
	public function comments() {
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function likes() {
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
	}

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function tags() {
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
	}

	public function getHref($p = array()) {
		$params = array_merge(array(
			'route' => 'ynfilesharing_folder_specific',
			'reset' => true,
			'slug' => $this->getSlug(),
			'folder_id' => $this->getIdentity()
		), $p);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}

	/**
	 * fetch folder to find parent folders
	 */
	private function _fetchParentFolders() {
		if (empty($this->_parentFolders)) {
			$folder = $this;
			$arr = array();
			while ($folder->parent_folder_id) {
				$folder = $folder->getParentFolder();
				array_push($arr, $folder);
			}
			$this->_parentFolders = array_reverse($arr);
		}
	}

	/**
	 * get the parent folders of a folder ordered by the root level to the children level
	 * @return array:
	 */
	public function getParentFolders() {
		if (empty($this->_parentFolders)) {
			$this->_fetchParentFolders();
		}
		return $this->_parentFolders;
	}
	
	public function getParentFolder() {
		if ($this->parent_folder_id) {
			return Engine_Api::_()->getItem('folder', $this->parent_folder_id);
		}
		
		return NULL;
	}

	public function isAllowed($user, $action = 'view') {		
		if ($this->authorization()->isAllowed($user, $action)) {
			// when checking the view permission, loop the parent folders to check
			if ($action == 'view') {
				$parentFolders = array_reverse($this->getParentFolders());
				foreach ($parentFolders as $folder) {
					if (!$folder->authorization()->isAllowed($user, $action)) {
						return false;
					}
				}
			} 
			
			return true;
		} else {
			if ($action == 'edit' || $action == 'edit_perm') {
				$parentFolders = array_reverse($this->getParentFolders());
						
			}
		}
		
		return false;
	}

	protected function _insert() {
		$folder = Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR;
		if (!is_dir($folder))
		{
			mkdir($folder);
		}

		$parent = $this->getParent();
		$destination = $folder . $parent->getGuid() . DIRECTORY_SEPARATOR;
		if (!is_dir($destination)) {
			mkdir($destination);
		}

		if ($this->parent_folder_id) {
			$parent = $this->getParentFolder();
			$destination = $parent->path . $this->title . DIRECTORY_SEPARATOR;
		} else {
			$destination .= $this->title . DIRECTORY_SEPARATOR;
		}

		if (!is_dir($destination)) {
			mkdir($destination);
		}

		$this->path = $destination;
	}

	public function getParentPath() {
		if ($this->parent_folder_id) {
			$parent = $this->getParentFolder();
				
			return $parent->path;
		}

		return Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $this->getParent()->getGuid() . DIRECTORY_SEPARATOR;
	}

	public function getSubFolders() {
		$table = new Ynfilesharing_Model_DbTable_Folders();
		$select = $table->select();
		$select->where('parent_folder_id = ?', $this->getIdentity());
		return $table->fetchAll($select);
	}

	public function moveTo($folderId = 0) {
		if ($folderId != 0) {
			$folder = Engine_Api::_()->getItem('folder', $folderId);
			if ($folder == NULL) {
				throw new Ynfilesharing_Model_Exception(sprintf('Folder with the id %d does not exist', $folderId));
			}
			$subFolders = $folder->getSubFolders();
			foreach ($subFolders as $f) {
				if ($f->title == $this->title) {
					throw new Ynfilesharing_Model_NameException(
							sprintf(
									Zend_Registry::get('Zend_Translate')->translate(
											'Folder %s (#%d) cannot be moved to the folder %s (#%d) because the folder name is the same with an existing sub folder.'),
									$this->title, $this->getIdentity(), $folder->title, $folder->getIdentity())
					);
				}
			}
			
			$parentFolders = $folder->getParentFolders();
			if (isset($parentFolders)) {
				foreach ($parentFolders as $f) {
					if ($f->title == $this->title) {
						throw new Ynfilesharing_Model_HierachyException(
								sprintf(
										Zend_Registry::get('Zend_Translate')->translate('Folder %s (#%d) cannot be moved to the folder %s (#%d) because the folder name is the same with an existing sub folder.'),
										$this->title, $this->getIdentity(), $folder->title, $folder->getIdentity())
						);
					}
				}
			}
		}
		
		$this->parent_folder_id = $folderId;
		
		$newPath = $folder->path . $this->title . DIRECTORY_SEPARATOR;		
		$oldPath = $this->path;
		
		// recursive copying folders and file, and then remove folders and files
		mkdir($newPath);
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($oldPath, RecursiveDirectoryIterator::KEY_AS_PATHNAME), RecursiveIteratorIterator::SELF_FIRST);
		foreach( $it as $item ) {
			$partial = str_replace($oldPath, '', $item->getPathname());
			$fDest = $newPath . $partial;
			// Ignore errors on mkdir (only fail if the file fails to copy
			if( $item->isDir() ) {
				@mkdir($fDest, $item->getPerms(), true);
			} else if( $item->isFile() ) {
				@mkdir(dirname($fDest), 755, true);
				if( !copy($item->getPathname(), $fDest) ) {
					throw new Engine_Package_Exception('Unable to copy.');
				}
			}
		}
		Engine_Package_Utilities::fsRmdirRecursive($oldPath, true);
		
		$this->save();
		
		$this->updatePath();
	}

	public function updatePath($parentPath = null) {
		if ($parentPath == null) {
			$this->path = $this->getParentPath() . $this->title . DIRECTORY_SEPARATOR;
		} else {
			$this->path = $parentPath . $this->title . DIRECTORY_SEPARATOR;
		}
		$this->save();

		$this->updateSubFoldersPath($this->path);
	}

	public function updateSubFoldersPath($parentPath) {
		foreach ($this->getSubFolders() as $subFolder) {
			$subFolder->updatePath($parentPath);
		}
	}

	protected function _update() {
		parent::_update();

		// check the update to avoid the looping situation when choosing an appropriate parent category
		if (array_key_exists('title', $this->_modifiedFields)) {
			$newPath = $this->getParentPath() . $this->title;
			rename($this->path, $newPath);
			$this->path = $newPath . DIRECTORY_SEPARATOR;
			
			$this->updateSubFoldersPath($this->path);
		}
	}

	protected function _delete() {
		parent::_delete();

		$filesharingApi = Engine_Api::_()->ynfilesharing();
		$subFolders = $filesharingApi->getSubFolders($this);
		foreach ($subFolders as $subFolder)
		{
			$subFolder->delete();
		}
		$files = Engine_Api::_()->ynfilesharing()->getFilesInFolder($this);
		foreach ($files as $file) {
			$file->delete();
		}
	}

	protected function _postDelete()
	{
		parent::_postDelete();
		Ynfilesharing_Plugin_Utilities::removeDir($this->path);
	}
	
	public function getTopFolder()
	{
		$currentFolder = $this;
		while($currentFolder->parent_folder_id > 0)
		{
			$parentFolderId = $currentFolder->parent_folder_id;
			$currentFolder = Engine_Api::_()->getItem('ynfilesharing_folder', $parentFolderId);
		}
		return $currentFolder;
	}
	
	public function canViewByShareCode($code){
		$tempArr = $this->getParentFolders();
		$parentArr = array_reverse($tempArr);
		foreach ($parentArr as $parentFoler)
		{
			if ($parentFoler->share_code == $code)
				return true;
		}
		return false;
	}
}