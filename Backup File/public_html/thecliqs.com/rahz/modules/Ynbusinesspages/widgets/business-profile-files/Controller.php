<?php
class Ynbusinesspages_Widget_BusinessProfileFilesController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() 
	{
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		//check auth for view business
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynfilesharing_folder') || !Engine_Api::_() -> hasModuleBootstrap('ynfilesharing')) {
            return $this -> setNoRender();
        }
		$this->getElement()->removeDecorator('Title');
		$this -> view -> canCreate = $canCreate = $business -> isAllowed('file_create');
		$this -> view -> canDeleteRemove = $canDeleteRemove = $business -> isAllowed('file_delete');
		
		$this -> view -> parentId = $parentId = $business -> getIdentity();
		$this -> view -> parentType = $parentType ='ynbusinesspages_business';
		
		$filesharingApi = Engine_Api::_() -> ynfilesharing();

		// Search Params
		$folders = $filesharingApi -> getSubFolders(NULL, $business);
		// Do not render if nothing to show and cannot upload
		if ($folders -> count() <= 0 && !$canCreate) {
			return $this -> setNoRender();
		}
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $folders -> count()) {
	    	 
	      $this->_childCount = $folders -> count();	
	    }
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> subFolders = $filesharingApi -> getFolders($folders, 'view', $viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders, $viewer);
		$totalUploaded = Engine_Api::_()->ynfilesharing()->getCurrentFolderSizeOfObject($business);
		$totalUploaded = number_format($totalUploaded/1048576, 2);
		$this -> view ->totalUploaded = $totalUploaded;
		$maxSizeKB = (INT)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $viewer, 'usertotal');
		$space_limit = 0;
		if($viewer -> getIdentity())
		{
			$space_limit = (int) Engine_Api::_()->authorization()->getPermission($viewer -> level_id, 'user', 'quota');
		}
		if($space_limit && $space_limit < $maxSizeKB)
		{
			$maxSizeKB = $space_limit;
		}
		$maxSizeKB = number_format($maxSizeKB/1024,2);
		$this -> view -> maxSizeKB = $maxSizeKB;	
	}
	public function getChildCount() {
        return $this->_childCount;
    }
}