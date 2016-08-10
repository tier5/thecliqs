<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Plugin_Menus
{
	private $_parentType;
	private $_parentId;
	private $_viewer;

	private function _initialize()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$this -> _parentType = $request -> getParam('parent_type', 'user');
		if ($this -> _parentType == 'user')
		{
			$this -> _parentId = $this -> _viewer -> getIdentity();
		}
		else
		{
			$this -> _parentId = $request -> getParam('parent_id');
			$object = Engine_Api::_() -> getItem($this -> _parentType, $this -> _parentId);
			if (!($object && $object -> membership() -> isMember($this -> _viewer)))
			{
				$this -> _parentType = 'user';
				$this -> _parentId = $this -> _viewer -> getIdentity();
			}
		}
	}

	public function onMenuInitialize_YnfilesharingMainManage($row)
	{
		$this -> _viewer = Engine_Api::_() -> user() -> getViewer();

		if ($this -> _viewer -> getIdentity())
		{
			if (Engine_Api::_() -> authorization() -> isAllowed('folder', $this -> _viewer, 'view'))
			{
				$this -> _initialize();

				return array(
					'label' => $row -> label,
					'uri' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
						'action' => 'manage',
						'parent_type' => $this -> _parentType,
						'parent_id' => $this -> _parentId
					), 'ynfilesharing_general', true)
				);
			}
		}

		return false;
	}

	public function onMenuInitialize_YnfilesharingMainLink($row)
	{
		if ($this -> _viewer -> getIdentity())
		{
			if (Engine_Api::_() -> authorization() -> isAllowed('folder', $this -> _viewer, 'view'))
			{
				$this -> _initialize();

				return array(
					'label' => $row -> label,
					'uri' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
						'controller' => 'link',
						'action' => 'browse',
						'parent_type' => $this -> _parentType,
						'parent_id' => $this -> _parentId
					), 'ynfilesharing_general', true)
				);
			}
		}

		return false;
	}

}
