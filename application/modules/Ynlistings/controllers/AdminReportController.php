<?php
class Ynlistings_AdminReportController extends Core_Controller_Action_Admin
{
	public function init()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynlistings_admin_main', array(), 'ynlistings_admin_main_reports');
	}
	public function manageAction()
	{
		//Get Data
		$reportTable = Engine_Api::_()->getItemTable('ynlistings_report');
	    $reportName = $reportTable ->info('name');
	   	$listingTable = Engine_Api::_()->getItemTable('ynlistings_listing');
	    $listingName = $listingTable ->info('name');
		$select = $reportTable->select()->from($reportName);
		
      	$this->view->paginator = $paginator = Zend_Paginator::factory($select);
     
  		// Set item count per page and current page number
      	$paginator->setItemCountPerPage(12);
      	$paginator->setCurrentPageNumber($this->_getParam('page', 1));
	}
	
	public function deleteSelectedAction(){
		$this->view->ids = $ids = $this->_getParam('ids', null);
		$confirm = $this->_getParam('confirm', false);
		$this->view->count = count(explode(",", $ids));
	
		// Check post
		if( $this->getRequest()->isPost() && $confirm == true )
		{
			//Process delete
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try{
				$ids_array = explode(",", $ids);
				foreach( $ids_array as $id ){
					$report = Engine_Api::_()->getItem('ynlistings_report', $id);
					if( $report ) $report->delete();
				}
				$db->commit();
			}
			catch( Exception $e )
			{
				$db->rollBack();
				throw $e;
			}
	
			$this->_helper->redirector->gotoRoute(array('controller'=>'report', 'action' => 'manage'));
		}
	}
	
	public function deleteAction()
	{
		// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
		$id = $this->_getParam('id');
		$this->view->report_id=$id;
		// Check post
		if( $this->getRequest()->isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
	
			try
			{
				$group = Engine_Api::_()->getItem('ynlistings_report', $id);
				$group->delete();
				$db->commit();
			}
	
			catch( Exception $e )
			{
				$db->rollBack();
				throw $e;
			}
	
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh'=> 10,
					'messages' => array('')
			));
		}
		// Output
		$this->renderScript('admin-report/delete.tpl');
	}
}