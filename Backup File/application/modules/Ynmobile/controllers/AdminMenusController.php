<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: AdminMenusController.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_AdminMenusController extends Core_Controller_Action_Admin
{
	protected $_enabledModuleNames;

	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmobile_admin_main', array(), 'ynmobile_admin_main_menus');
		$this -> _enabledModuleNames = Engine_Api::_() -> getDbtable('modules', 'core') -> getEnabledModuleNames();
		// update
	}

	public function indexAction()
	{
		// Get menu items
		$menuItemsTable = Engine_Api::_() -> getDbtable('menuitems', 'ynmobile');
		$menuItemsSelect = $menuItemsTable -> select()
		// ->      where('menu = ?', 1)
		-> order('order');
		if (!empty($this -> _enabledModuleNames))
		{
			$menuItemsSelect -> where('module IN(?)', $this -> _enabledModuleNames);
		}
		$this -> view -> menuItems = $menuItems = $menuItemsTable -> fetchAll($menuItemsSelect);
	}

	public function createAction()
	{
		// Get form
		$this -> view -> form = $form = new Ynmobile_Form_Admin_Menu_ItemCreate();

		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Save
		$values = $form -> getValues();
		$menuItemsTable = Engine_Api::_() -> getDbtable('menuitems', 'ynmobile');
		$values['name'] = str_replace(" ", "_",strtolower($values['name']));
		//check name
		if ($values['name'] != $menuItem -> name)
		{
			$select = $menuItemsTable -> select() -> where('name = ?', $values['name']);
			$otherItem = $menuItemsTable -> fetchRow($select);
			if ($otherItem)
			{
				$form -> getElement('name') -> addError(Zend_Registry::get('Zend_Translate') -> _("Someone has already added this name."));
				return;
			}
		}
		$menuItem = $menuItemsTable -> createRow();
		$menuItem -> setFromArray($values);
		$menuItem -> save();
		$this -> view -> status = true;
		$this -> view -> form = null;
	}

	public function editAction()
	{
		$this -> view -> name = $name = $this -> _getParam('name');
		// Get menu item
		$menuItemsTable = Engine_Api::_() -> getDbtable('menuitems', 'ynmobile');
		$menuItemsSelect = $menuItemsTable -> select() -> where('name = ?', $name);
		if (!empty($this -> _enabledModuleNames))
		{
			$menuItemsSelect -> where('module IN(?)', $this -> _enabledModuleNames);
		}
		$this -> view -> menuItem = $menuItem = $menuItemsTable -> fetchRow($menuItemsSelect);

		if (!$menuItem)
		{
			throw new Core_Model_Exception('missing menu item');
		}

		// Get form
		$this -> view -> form = $form = new Ynmobile_Form_Admin_Menu_ItemEdit();

		// Make safe
		$menuItemData = $menuItem -> toArray();
		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($menuItemData);
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Save
		$values = $form -> getValues();
		$values['name'] = str_replace(" ", "_",strtolower($values['name']));
		//check name
		if ($values['name'] != $menuItem -> name)
		{
			$select = $menuItemsTable -> select() -> where('name = ?', $values['name']);
			$otherItem = $menuItemsTable -> fetchRow($select);
			if ($otherItem)
			{
				$form -> getElement('name') -> addError(Zend_Registry::get('Zend_Translate') -> _("Someone has already added this name."));
				return;
			}
		}
		$menuItem -> setFromArray($values);
		$menuItem -> save();

		$this -> view -> status = true;
		$this -> view -> form = null;
	}

	public function deleteAction()
	{
		$this -> view -> name = $name = $this -> _getParam('name');

		// Get menu item
		$menuItemsTable = Engine_Api::_() -> getDbtable('menuitems', 'ynmobile');
		$menuItemsSelect = $menuItemsTable -> select() -> where('name = ?', $name) -> order('order ASC');
		if (!empty($this -> _enabledModuleNames))
		{
			$menuItemsSelect -> where('module IN(?)', $this -> _enabledModuleNames);
		}
		$this -> view -> menuItem = $menuItem = $menuItemsTable -> fetchRow($menuItemsSelect);

		if (!$menuItem)
		{
			throw new Core_Model_Exception('missing menu item');
		}

		// Get form
		$this -> view -> form = $form = new Ynmobile_Form_Admin_Menu_ItemDelete();

		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$menuItem -> delete();

		$this -> view -> form = null;
		$this -> view -> status = true;
	}

	public function orderAction()
	{
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$table = Engine_Api::_() -> getDbtable('menuitems', 'ynmobile');
		$menuitems = $table -> fetchAll($table -> select()
		//-> where('menu = ?', $this -> getRequest() -> getParam('menu'))
		);
		foreach ($menuitems as $menuitem)
		{
			$order = $this -> getRequest() -> getParam('admin_menus_item_' . $menuitem -> name);
			if (!$order)
			{
				$order = 999;
			}
			$menuitem -> order = $order;
			$menuitem -> save();
		}
		return;
	}

}
