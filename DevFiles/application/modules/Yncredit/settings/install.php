<?php
class Yncredit_Installer extends Engine_Package_Installer_Module 
{
	function onInstall() 
	{
		parent::onInstall();
		$this -> _addGeneralPage();
		$this -> _addMyCreditPage();
		$this -> _addFAQsPage();
	}
	protected function _addGeneralPage()
	{
		$db = $this->getDb();
		// General page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'yncredit_index_index') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'yncredit_index_index',
				'displayname' => 'User Credit General Page',
				'title' => 'User Credit General Page',
				'description' => 'This page show general information.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
				'order' => 2,
			));
			$top_middle_id = $db -> lastInsertId();
			
			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 3,
			));
			$main_id = $db -> lastInsertId();
			
			// Insert main-left
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'left',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 1,
			));
			$main_left_id = $db -> lastInsertId();
			
			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_right_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 3,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));
			
			// Insert content 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
			
			// Insert right buy credit
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.buy-credit',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 1,
				'params' => '{"title":"Buy Credit"}',
			));
			// Insert right send credit
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.send-credit',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 2,
				'params' => '{"title":"Send Credit"}',
			));
			
			// Insert left top credits balance
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.top-credits-balance',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 1,
				'params' => '{"title":"Top Credits Balance"}',
			));
			
			// Insert left top active members
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.top-active-members',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 2,
				'params' => '{"title":"Top Active Members"}',
			));
			// Insert left statistics
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.statistics',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 3,
				'params' => '{"title":"Statistics"}',
			));
		}
	}
	protected function _addMyCreditPage()
	{
		$db = $this->getDb();
		// General page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'yncredit_profile_index') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'yncredit_profile_index',
				'displayname' => 'User Credit My Credit Page',
				'title' => 'User Credit My Credit Page',
				'description' => 'This page show my credit information.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
				'order' => 2,
			));
			$top_middle_id = $db -> lastInsertId();
			
			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 3,
			));
			$main_id = $db -> lastInsertId();
			
			// Insert main-left
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'left',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 1,
			));
			$main_left_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert top menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));
			
			// Insert middle my statistics 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.my-statistics',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
				'params' => '{"title":"My Statistics"}',
			));
			
			// Insert middle content 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 2,
			));
			
			// Insert left buy credit
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.buy-credit',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 1,
				'params' => '{"title":"Buy Credit"}',
			));
			// Insert left send credit
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.send-credit',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 2,
				'params' => '{"title":"Send Credit"}',
			));
			// Insert left statistics
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.statistics',
				'page_id' => $page_id,
				'parent_content_id' => $main_left_id,
				'order' => 3,
				'params' => '{"title":"Statistics"}',
			));
		}
	}
	protected function _addFAQsPage()
	{
		$db = $this->getDb();
		// General page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'yncredit_faq_index') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'yncredit_faq_index',
				'displayname' => 'User Credit FAQs Page',
				'title' => 'User Credit FAQs Page',
				'description' => 'This page show faqs.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
				'order' => 2,
			));
			$top_middle_id = $db -> lastInsertId();
			
			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 3,
			));
			$main_id = $db -> lastInsertId();
			
			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 1,
			));
			$main_right_id = $db -> lastInsertId();
			
			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert top menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));
			
			// Insert middle content 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
			
			// Insert right buy credit
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.buy-credit',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 1,
				'params' => '{"title":"Buy Credit"}',
			));
			// Insert right send credit
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.send-credit',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 2,
				'params' => '{"title":"Send Credit"}',
			));
			// Insert right statistics
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'yncredit.statistics',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 3,
				'params' => '{"title":"Statistics"}',
			));
		}
	}
}
?>