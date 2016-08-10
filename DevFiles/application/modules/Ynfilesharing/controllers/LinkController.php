<?php
class Ynfilesharing_LinkController extends Core_Controller_Action_Standard
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

	public function browseAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		$this -> view -> parentId = $parentId = $this -> _getParam('parent_id');
		$this -> view -> parentType = $parentType = $this -> _getParam('parent_type', 'user');
		if (empty($parentId) && $parentType == 'user')
		{
			$this -> view -> parentId = $parentId = $this -> _viewer -> getIdentity();
		}
		//$parent = Engine_Api::_()->getItem($parentType, $parentId);

		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfilesharing_main', array(), 'ynfilesharing_main_link');
		$folderTable = Engine_Api::_() -> getDbTable("folders", 'ynfilesharing');
		$select = $folderTable -> select() -> where("(share_code IS NOT NULL) AND (share_code != '')") -> where('parent_id = ?', $parentId) -> where('parent_type = ?', $parentType);

		$this -> view -> sharedFolders = $sharedFolders = $folderTable -> fetchAll($select);

		$fileTable = Engine_Api::_() -> getDbTable("files", 'ynfilesharing');
		$select = $fileTable -> select() -> where("(share_code IS NOT NULL) AND (share_code != '')") -> where('parent_id = ?', $parentId) -> where('parent_type = ?', $parentType);

		$this -> view -> sharedFiles = $sharedFiles = $fileTable -> fetchAll($select);
	}

	public function deleteAction()
	{
		$objectType = $this -> _getParam('object_type', '');
		$this -> view -> code = $code = $this -> _getParam('code', '');
		if ($this -> getRequest() -> isPost())
		{
			if ($objectType && $code && ($objectType == 'files' || $objectType == 'folders'))
			{
				try
				{
					$tbl = Engine_Api::_() -> getDbTable($objectType, 'ynfilesharing');
					$select = $tbl -> select() -> where('share_code = ?', $code);
					$item = $tbl -> fetchRow($select);
					if (is_object($item))
					{
						$item -> share_code = '';
						$item -> save();
					}
				}
				catch ( Exception $e )
				{
				}

				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => 8,
					'parentRefresh' => 8,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> translate('Deleted link successfully'))
				));
			}
		}
		$this -> renderScript('link/delete.tpl');
	}

	public function viewAction()
	{
		$objectType = $this -> _getParam('object_type', '');
		$objectId = $this -> _getParam('object_id', 0);
		if ($objectType && $objectId && ($objectType == 'file' || $objectType == 'folder'))
		{
			$item = Engine_Api::_() -> getItem($objectType, $objectId);

			if (is_object($item))
			{
				$shareLink = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'object_type' => $objectType,
					'object_id' => $objectId,
					'code' => $item -> share_code
				), 'ynfilesharing_share_view', true);
				$this -> view -> shareLink = Ynfilesharing_Plugin_Utilities::getBaseUrl() . $shareLink;
			}
		}
	}

}
