<?php
class Ynsocialads_AdminAdBlocksController extends Core_Controller_Action_Admin
{
	protected $_placementMap = array(
		'middle_top' => 'Middle Ads - Top',
		'middle_bottom' => 'Middle Ads - Bottom',
		'left_top' => 'Left Column Ads - Top',
		'left_bottom' => 'Left Column Ads - Bottom',
		'right_top' => 'Right Column Ads - Top',
		'right_bottom' => 'Right Column Ads - Bottom',
	);
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_adblocks');
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value)
			{
				if ($key == 'delete_' . $value)
				{
					$adblock = Engine_Api::_() -> getItem('ynsocialads_adblock', $value);
					$adblock -> deleted = 1;
					$adblock -> save();
				}
			}
		}
		$table = Engine_Api::_() -> getItemTable('ynsocialads_adblock');
		$adblocks = $table -> fetchAll();
		$this -> view -> placementMap = $this -> _placementMap;
		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($adblocks);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function createAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_adblocks');
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Adblocks_Create();
		$pageTable = Engine_Api::_() -> getDbtable('pages', 'core');
		$contentTable = Engine_Api::_() -> getDbtable('content', 'core');

		//get pages list
		$pageSelect = $pageTable -> select() ->order('fragment DESC') -> order('displayname ASC') -> where('page_id > 1');
		$pageList = $pageTable -> fetchAll($pageSelect);
		
		$pages = array();
		foreach ($pageList as $pageRow)
		{
			if( false === stripos($pageRow->displayname, 'mobile') ) 
			{
				$pages[$pageRow -> page_id] = $pageRow -> displayname;
			}
		}
		$pageListAssoc['Widgetized Pages'] = $pages;
		
		if(Engine_Api::_() -> hasModuleBootstrap('ynsocialadspage'))
		{
			// add page transfomation
			$proxyTable = Engine_Api::_()->getDbtable('proxies', 'ynsocialadspage');
			// Get page list
		    $pageSelect = $proxyTable -> select()
		        ->order('title ASC');
			$tranformPages = array();
		    foreach ($proxyTable -> fetchAll($pageSelect) as $page) 
		    {
				$tranformPages[$page -> page_id] = $page -> title;
			}
			if($tranformPages)
			{
				$pageListAssoc["Pages Transformation"] = $tranformPages;
			}
		}
		$form -> page_id -> setMultiOptions($pageListAssoc);
		
		if (null == ($page_id = $this -> _getParam('page_id')))
		{
			$page_id = 3;
		}
		$form -> page_id -> setValue($page_id);
		$placement = $this -> _getParam('placement');
		$contentRowset = $contentTable -> fetchAll($contentTable -> select() -> where('page_id = ?', $page_id) -> order('order'));

		if ($page_id == 2)
		{
			$form -> removeElement('page_layout');
			$form -> removeElement('placement');
			$widget_previewSrc = 'application/modules/Ynsocialads/externals/images/widgets/footer.png';
			$form -> widget_preview -> src = $widget_previewSrc;
		}
		else
		{
			$contentStructure = $pageTable -> prepareContentArea($contentRowset);
			$cols = 1;
			$col = 1;
			$position = '';
			foreach ($contentStructure as $info1)
			{
				if ($info1['name'] == 'top' && $info1['type'] == 'container')
				{
					$cols = 2;
					break;
				}
				if ($info1['name'] == 'bottom' && $info1['type'] == 'container')
				{
					$cols = 3;
					break;
				}
			}
			foreach ($contentStructure as $info1)
			{
				if ($info1['name'] == 'main' && $info1['type'] == 'container')
				{
					$col = count($info1['elements']);
					if (2 == $col)
					{
						foreach ($info1['elements'] as $info2)
						{
							if ($info2['name'] == 'left' && $info2['type'] == 'container')
							{
								$position = 'left';
								break;
							}
							if ($info2['name'] == 'right' && $info2['type'] == 'container')
							{
								$position = 'right';
								break;
							}
						}
					}
					break;
				}
			}
			$page_layoutSrc = 'application/modules/Ynsocialads/externals/images/page_layout/' . 'cols' . $cols . '_' . $col . $position . '.png';
			$form -> page_layout -> src = $page_layoutSrc;

			$placementArr = array(
				'middle_top' => 'Middle Ads - Top',
				'middle_bottom' => 'Middle Ads - Bottom'
			);
			if ($col == 3)
			{
				$placementArr = array_merge($placementArr, array(
					'left_top' => 'Left Column Ads - Top',
					'left_bottom' => 'Left Column Ads - Bottom',
					'right_top' => 'Right Column Ads - Top',
					'right_bottom' => 'Right Column Ads - Bottom'
				));
			}
			else
			if ($col == 2)
			{
				if ($position == 'left')
				{
					$placementArr = array_merge($placementArr, array(
						'left_top' => 'Left Column Ads - Top',
						'left_bottom' => 'Left Column Ads - Bottom',
					));
				}
				else
				{
					$placementArr = array_merge($placementArr, array(
						'right_top' => 'Right Column Ads - Top',
						'right_bottom' => 'Right Column Ads - Bottom'
					));
				}
			}
			$form -> placement -> setMultiOptions($placementArr);
			if ($placement && $placementArr[$placement])
			{
				$form -> placement -> setValue($placement);
			}
			else
			{
				$placement = key($placementArr);
			}

			//    $placement = $form->placement;

			$fixArr = explode('_', $placement);
			$widget_previewSrc = 'application/modules/Ynsocialads/externals/images/widgets/' . $fixArr[0] . '.png';
			$form -> widget_preview -> src = $widget_previewSrc;

		}

		if ($this -> getRequest() -> isPost())
		{
			if (!$form -> isValid($this -> getRequest() -> getPost()))
			{
				return;
			}

			$values = $this -> getRequest() -> getPost();
			$viewer = Engine_Api::_() -> user() -> getViewer();

			$timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core_locale_timezone', 'GMT');
			if ($viewer && $viewer -> getIdentity() && !empty($viewer -> timezone))
			{
				$timezone = $viewer -> timezone;
			}
			$create_date = new Zend_Date();
			$create_date -> setTimezone($timezone);
			$values['create_date'] = $create_date -> get(Zend_Date::DATETIME_MEDIUM);
			$db = Engine_Db_Table::getDefaultAdapter();
			$table = Engine_Api::_() -> getDbtable('adblocks', 'ynsocialads');
			$select = $table -> select() -> where('page_id = ?', $values['page_id'])->where('deleted = ?', 0);
			if (intval($page_id) != 2)
				$select -> where('placement = ?', $values['placement']);
			$adblock = $table -> fetchRow($select);
            
            if ($adblock) {
                $form->addError('This ad block has already existed.');
                return;
            }
            
			$db -> beginTransaction();
			try
			{
				$contentRow = $contentTable -> createRow();
				$contentRow -> page_id = $page_id;
				$contentRow -> type = 'widget';
				$contentRow -> name = 'ynsocialads.ads-content';
				
				foreach ($contentRowset as $content)
				{
					if ($content['name'] == 'main')
					{
						$main_content_id = $content -> content_id;
						break;
					}
				}
				$arr_place_contents = array();
				if (intval($page_id) == 2)
				{

					$parent_content_id = $main_content_id;
				}
				else
				{
					$placement_position = explode('_', $values['placement']);
					foreach ($contentRowset as $content)
					{
						if ($content['name'] == $placement_position[0] && $content['parent_content_id'] == $main_content_id)
						{
							$parent_content_id = $content -> content_id;
							break;
						}
					}
				}
				foreach ($contentRowset as $content)
				{
					if ($content['parent_content_id'] == $parent_content_id)
					{
						$arr_place_contents[] = $content;
					}
				}
				$contentRow -> parent_content_id = $parent_content_id;
				$order = 1;
				if (intval($page_id) != 2 && $arr_place_contents)
				{
					if ($placement_position[1] == 'top')
					{
						$topContent = current($arr_place_contents);
						if($topContent)
							$order = $topContent['order'] - 1;
					}
					else
					{
						$bottomContent = end($arr_place_contents);
						if($bottomContent)
							$order = $bottomContent['order'] + 1;
					}
				}
				$contentRow -> order = $order;
				$contentRow -> save();
				
				$values['content_id'] = $contentRow -> content_id;
				$adblock = $table -> createRow();
                    
				$adblock -> setFromArray($values);
				$adblock -> save();

				$contentRow -> params = array(
					'adblock_id' => $adblock['adblock_id'],
					'ads_limit' => $adblock['ads_limit'],
					'ajax' => $adblock['ajax'],
				);
				$contentRow -> save();
				$success = TRUE;
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$db -> commit();
			if ($success)
			{
				$this -> _redirect('admin/ynsocialads/ad-blocks');
			}
		}
	}

	public function deleteAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> adblock_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				$adblock = Engine_Api::_() -> getItem('ynsocialads_adblock', $id);
				$adblock -> deleted = 1;
				$adblock -> save();

				$contentTable = Engine_Api::_() -> getDbtable('content', 'core');
                $contentRow = $contentTable->fetchRow($contentTable->select()->where('content_id = ?', $adblock['content_id']));
                if ($contentRow) $contentRow->delete();
				$db -> commit();
			}

			catch(Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}

		// Output
		$this -> renderScript('admin-ad-blocks/delete.tpl');
	}

	public function editAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$adblock_id = $this -> _getParam('id');
		$this -> view -> campaign_id = $id;
		$adblock = Engine_Api::_() -> getItem('ynsocialads_adblock', $adblock_id);
		if (!$adblock)
			return;
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Adblocks_Edit();
		$form -> populate($adblock -> toArray());
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			if (!$form -> isValid($this -> getRequest() -> getPost()))
			{
				return;
			}
			$values = $this -> getRequest() -> getPost();
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				$adblock -> setFromArray($values);
				$adblock -> save();
				$db -> commit();
				$adblock -> updateContent();
			}

			catch(Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}

		// Output
		$this -> renderScript('admin-ad-blocks/edit.tpl');
	}

	public function enableAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		if (null == ($adblock_id = $this -> _getParam('id')))
		{
			return;
		}
		$adblock = Engine_Api::_() -> getItem('ynsocialads_adblock', $adblock_id);
		if (!$adblock)
			return;
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try
		{
			$adblock -> enable = !$adblock -> enable;
			$adblock -> save();
			$db -> commit();
			$adblock -> updateContent();
		}

		catch(Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		echo $adblock -> enable;
		return true;
	}

	public function ajaxAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		if (null == ($adblock_id = $this -> _getParam('id')))
		{
			return;
		}
		$adblock = Engine_Api::_() -> getItem('ynsocialads_adblock', $adblock_id);
		if (!$adblock)
			return;
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try
		{
			$adblock -> ajax = !$adblock -> ajax;
			$adblock -> save();
			$db -> commit();
			$adblock -> updateContent();
		}

		catch(Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		return true;
	}

	public function multideleteAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));

		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			//Process delete
			$ids_array = explode(",", $ids);
			foreach ($ids_array as $id)
			{
				$adblock = Engine_Api::_() -> getItem('ynsocialads_adblock', $id);
				if ($adblock)
				{
					$adblock -> deleted = 1;
					$adblock -> save();
				}
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}

}
