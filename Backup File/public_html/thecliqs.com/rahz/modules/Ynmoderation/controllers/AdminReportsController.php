<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynmoderation
 * @author     YouNet Company
 */

class Ynmoderation_AdminReportsController extends Core_Controller_Action_Admin {
	public function indexAction() {
		$this->view->formFilter = $formFilter = new Ynmoderation_Form_Admin_Reports_Filter();
		$this->view->navigation = $navigation = Engine_Api::_ ()->getApi('menus', 'core')->getNavigation('ynmoderation_admin_main', array(), 'ynmoderation_admin_main_reports');
		
		//Getting Plugins list
		$type = $this->_getParam('type_id');
		$params = array( 'enabled'=>'1' );
		if( !empty($type) && is_numeric($type) ) {
			$params['id'] = $type; 
	    }
		
		$pluginTbl = Engine_Api::_()->getDbTable('modules', 'ynmoderation');
		$pluginList = $pluginTbl->fetchAll($pluginTbl->getModulesSelect($params));
		
		//Checking Post status for removing data
		if ($this->getRequest()->isPost()) {
			$values = $this->getRequest()->getPost();
			foreach ($pluginList as $plugin){
				$type = $plugin->report_object_type;
				if (is_array($values[$type])){
					for ($i = 0; $i < count($values[$type]); $i++){
						$id = $values[$type][$i];
				        $item = Engine_Api::_()->getItem($type, $id);
				        if (is_object($item))
				        	$item->delete();
					}
				}
			}
	    }
		
		//Getting report
		$db = Engine_Db_Table::getDefaultAdapter();
		$modulesObj = new Core_Model_DbTable_Modules();
		$select = $db->select();
		$unionArr = array();
		if (count($pluginList) <= 0) return;
		
		foreach ($pluginList as $plugin){
			//Checking module is existed/enabled or not
			if (Engine_Api::_()->hasItemType($plugin->object_type) && $plugin->report_query)
				$unionArr[] = $plugin->report_query;
		}
		if (count($unionArr) == 0) return;
		
		$select = $select->union($unionArr);
		$mainSelect = $db->select()
				->from(array('t' => $select))
				->limit(50)
				->order(' t.creation_date DESC ');
		
		//Filter content by description
		$description = $this->_getParam('description');		
		if( !empty($description) ) {
			$mainSelect->where("t.description LIKE ? ", "%$description%");
	    }		
		$page = $this->_getParam('page',1);
	    $this->view->paginator = $paginator = Zend_Paginator::factory($mainSelect);
	    $this->view->paginator->setItemCountPerPage(50);
	    $this->view->paginator->setCurrentPageNumber($page);
		
	}
	
	//Dismiss - Meaning delete report
	public function deleteAction()
	{
	    $this->view->r_id = $r_id = $this->_getParam('r_id', null);
	    $this->view->r_type = $r_type = $this->_getParam('r_type', null);
	
	    // Check post
	    if( $this->getRequest()->isPost() )
	    {
	    	if ( Engine_Api::_()->hasItemType($r_type) ){
	    		$db = Engine_Db_Table::getDefaultAdapter();
		      	$db->beginTransaction();
		
			      try {
			        $report = Engine_Api::_()->getItem($r_type, $r_id);
			        $report->delete();
			        $db->commit();
			      }
			      catch( Exception $e ) {
			        $db->rollBack();
			        throw $e;
			      }
		
			      return $this->_forward('success', 'utility', 'core', array(
			          'smoothboxClose' => true,
			          'parentRefresh'=> true,
			          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Dismissed Successfully.'))
			      ));
	    	}
	    	else
	    	{
	    		return $this->_forward('success', 'utility', 'core', array(
			          'smoothboxClose' => true,
			          'parentRefresh'=> true,
			          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Dismissed Unsuccessfully.'))
			      ));
	    	}
		      
	    }
	    // Output
	    $this->renderScript('admin-reports/delete.tpl');
	}
}