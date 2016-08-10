<?php
class Ynbusinesspages_Widget_BusinessNewestFilesController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		//check auth for view business
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynfilesharing_folder') || !Engine_Api::_() -> hasModuleBootstrap('ynfilesharing')) 
        {
            return $this -> setNoRender();
        }
		$this -> view -> parentId = $parentId = $business -> getIdentity();
		$this -> view -> parentType = $parentType ='ynbusinesspages_business';
		
		$filesharingApi = Engine_Api::_() -> ynfilesharing();

		// Params
		$params['parent_type'] = $parentType;
		$params['parent_id'] = $parentId;
		$params['limit'] = $this -> _getParam('itemCountPerPage', 1);
		
		$folder_table = Engine_Api::_() -> getItemTable('folder');
		$folder_name = $folder_table -> info('name');
		$select = $folder_table -> select() -> from($folder_name);
		$select -> where("$folder_name.parent_id = {$params ['parent_id']} and $folder_name.parent_type = '{$params ['parent_type']}'");
		$select -> order("$folder_name.modified_date DESC");
		$select -> limit($params['limit']);
		$folders = $folder_table -> fetchAll($select);
		
		// Do not render if nothing to show
		if ($folders -> count() <= 0 ) {
			return $this -> setNoRender();
		}
        
        $this -> view -> subFolders = $folders = $filesharingApi -> getFolders($folders, 'view', $viewer);
        if (!count($folders))
            return $this -> setNoRender();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
	}
}