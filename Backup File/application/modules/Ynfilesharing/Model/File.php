<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Model_File extends Core_Model_Item_Abstract {
	protected $_type = 'file';
	
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

	public function getHref($params = array()) {
		$params = array_merge(array(
			'route' => 'ynfilesharing_file_specific',
			'reset' => true,
			'slug' => $this->getSlug(),
			'file_id' => $this->getIdentity()
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}

	public function getFileIcon(){
		$fileType = $this->ext;
		$iconName = (file_exists(APPLICATION_PATH . "/application/modules/Ynfilesharing/externals/images/file_types/".$fileType.".png"))
			? $fileType. ".png"
			: "unknown.png";
		return $iconName;
	}
	
	protected function _postDelete()
	{
		parent::_postDelete();
		$folder = $this->getParentFolder();
		@unlink($folder->path . $this->name);
	}
	
	public function getTitle(){
		return $this->name;
	}
	
	public function moveTo($folderId = 0) {
		if ($folderId != 0) {
			
			$destFolder = Engine_Api::_()->getItem('folder', $folderId);
			if (isset($destFolder) && is_object($destFolder)) {
				$fileTbl = new Ynfilesharing_Model_DbTable_Files();
				$existedFile = $fileTbl->getExistedFile($folderId, $this->name);
				if (!is_null($existedFile))
				{
					throw new Ynfilesharing_Model_NameException(
						sprintf(
							Zend_Registry::get('Zend_Translate')->translate('File %s (#%d) cannot be moved to the folder %s (#%d) because there is an existing file.'),
							$this->name, $this->getIdentity(), $destFolder->title, $folderId)
					);
				}
				// TODO [DangTH] : Think about whether the user can move to this folder
// 				if (!$destFolder->isAllowed($this->_viewer, 'create')) {
// 					throw new Ynfilesharing_Model_AuthException(
// 						sprintf('File with the id %d cannot be moved to the folder %d because the viewer does not have the create permission of folder %d',
// 							$this->getIdentity(), $folderId, $folderId)
// 					);
// 				}
				
				$newPath = $destFolder->path . $this->name;
				
				$parentFolder = $this->getParentFolder();
				$oldPath = $parentFolder->path .  $this->name;
				
				if ($destFolder == NULL) {
					throw new Ynfilesharing_Model_Exception(
						sprintf('Folder with the id %d doesnot exist', $folderId)
					);
				}
				@rename($oldPath, $newPath);
				$this->folder_id = $folderId;
				$this->save();
			} 
		}
	}
	
	public function getParentFolder() {
		if ($this->folder_id) {
			return Engine_Api::_()->getItem('folder', $this->folder_id);
		}
		return NULL;
	}
	
	public function canViewByShareCode($code){
		$folder = $this->getParentFolder();
		if ($folder->share_code == $code)
			return true;
		else {
			$tempFolderArr = $folder->getParentFolders();
			$parentFolderArr = array_reverse($tempFolderArr);
			foreach ($parentFolderArr as $parentFoler)
			{
				if ($parentFoler->share_code == $code)
					return true;
			}
			return false;	
		}
	}
}