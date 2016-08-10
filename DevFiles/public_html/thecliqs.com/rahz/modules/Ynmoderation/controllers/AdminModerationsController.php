<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynmoderation
 * @author     YouNet Company
 */

class Ynmoderation_AdminModerationsController extends Core_Controller_Action_Admin {
	public function indexAction() {
		if ($this->checkModuleExisted('ynidea')){
			if (!isset($_SESSION['ynmoderation_checked_idea_collation'])){ 
				$db_adapter = Engine_Db_Table::getDefaultAdapter();
				$sql = "ALTER TABLE  `engine4_ynidea_ideas` CHANGE  `title`  `title` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
				$db_adapter->query($sql);
				$_SESSION['ynmoderation_checked_idea_collation'] = 1;
			}
		}
		if ($this->checkModuleExisted('ynwiki')){
			if (!isset($_SESSION['ynmoderation_checked_wiki_collation'])){
				$db_adapter = Engine_Db_Table::getDefaultAdapter();
				$sql = "ALTER TABLE  `engine4_ynwiki_pages` CHANGE  `title`  `title` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
				$db_adapter->query($sql);
				$_SESSION['ynmoderation_checked_wiki_collation'] = 1;
			}
		}
		$this->view->formFilter = $formFilter = new Ynmoderation_Form_Admin_Moderations_Filter();
		$this->view->navigation = $navigation = Engine_Api::_ ()->getApi('menus', 'core')->getNavigation('ynmoderation_admin_main', array(), 'ynmoderation_admin_main_moderations');
		
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
				$type = $plugin->object_type;
				if (is_array($values[$type])){
					for ($i = 0; $i < count($values[$type]); $i++){
						$id = $values[$type][$i];
				        $item = Engine_Api::_()->getItem($type, $id);
				        $item->delete();
				        //Getting moderation module record
				        $tmpPlugin = $pluginTbl->fetchRow($pluginTbl->getModulesSelect(array('object_type' => $type)));
						if (is_object($tmpPlugin)){
				        	if ($tmpPlugin ->report_object_type){
				        		$reportObjType = $tmpPlugin ->report_object_type;
				        		$reportField = $tmpPlugin ->report_field;
				        		$reportTbl = Engine_Api::_()->getItemTable($reportObjType);
				        		$reportTbl->delete("$reportField = $id");
				        	}
				        }
					}
				}
			}
	    }
		//Getting content
		$modulesObj = new Core_Model_DbTable_Modules();
		$db = Engine_Db_Table::getDefaultAdapter();
		$select = $db->select();
		$unionArr = array();
		if (count($pluginList) <= 0) return;
		
		foreach ($pluginList as $plugin){
			//Checking module is existed/enabled or not
			if (Engine_Api::_()->hasItemType($plugin->object_type) && $plugin->moderation_query)
				$unionArr[] = $plugin->moderation_query;
		}		
		if (count($unionArr) == 0) return;
		
		$select = $select->union($unionArr);
		$mainSelect = $db->select()
				->from(array('t' => $select))
				->limit(500)
				->order(' t.creation_date DESC ');
		//Filter content by user
		$userName = $this->_getParam('username');
		if( !empty($userName) ){
			//$user = Engine_Api::_() -> user() -> getUser($userName);
			$userTable = new User_Model_DbTable_Users();
			$userSelect = $userTable
				->select()
				->from($userTable->info('name'), 'user_id')
				->where(" username = '$userName' OR displayname = '$userName' ");
			$users = $userTable->fetchAll($userSelect)->toArray();
			$uidArr = array();
			if (count($users)){
				foreach ($users as $user)
					$uidArr[] = $user['user_id'];
				$mainSelect->where( "t.creator IN (?)", $uidArr );
			}
			else 
				$mainSelect->where( "t.creator = 0" );
		}
		
		//Filter content by title
		$title = $this->_getParam('title');		
		if( !empty($title) ) {
			$mainSelect->where("t.title LIKE ? ", "%$title%");
	    }
		$page = $this->_getParam('page',1);
	    $this->view->paginator = $paginator = Zend_Paginator::factory($mainSelect);
	    $this->view->paginator->setItemCountPerPage(500);
	    $this->view->paginator->setCurrentPageNumber($page);
	}
	
	
	public function checkModuleExisted($moduleName)
   	{
   		$db     = Engine_Db_Table::getDefaultAdapter();
    	$select = $db->select();
	    $select
	      ->from('engine4_core_modules')
	      ->where('name = ?', $moduleName)
	      ;
	    $info = $select->query()->fetch();
	    if( empty($info) ) 
	    	return false;
	    else
	    	return true;
   	}
	
  	public function deleteAction()
	{
	    // In smoothbox
	    $this->_helper->layout->setLayout('admin-simple');
	    $this->view->id= $id = $this->_getParam('id');
	    $this->view->type= $type = $this->_getParam('type');
	    
	    // Check post
	    if( $this->getRequest()->isPost() )
	    {
		      $db = Engine_Db_Table::getDefaultAdapter();
		      $db->beginTransaction();
		      
		      //Get model filter by object_type
		      $pluginTbl = Engine_Api::_()->getDbTable('modules', 'ynmoderation');
			  $plugin = $pluginTbl->fetchRow($pluginTbl->getModulesSelect(array('object_type' => $type)));
			  if ($type && $id){
			  	if ( Engine_Api::_()->hasItemType($type) )
			  	{
				  	try {
				        $item = Engine_Api::_()->getItem($type, $id);
				        //I hate to do this, but I am having no choice to do
				        if ($type == 'ynwiki_page')
				        	$item->reDelete();
				       	else
				        	$item->delete();
				        if (is_object($plugin)){
				        	if ($plugin -> report_object_type){
				        		$reportObjType = $plugin ->report_object_type;
				        		$reportField = $plugin ->report_field;
				        		$reportTbl = Engine_Api::_()->getItemTable($reportObjType);
				        		//in engine4_core_reports table, we need subject_type to delete record exactly
				        		$deleteParams = "$reportField = $id" . (($reportObjType == 'core_report') ? " AND subject_type = '$type'" : "");
				        		$reportTbl->delete($deleteParams);	
				        	}
				        }
				        $db->commit();
					}
					catch( Exception $e ) {
				        $db->rollBack();
				        throw $e;
				  	}
			  	}
			  	else 
			  	{
			  		return $this->_forward('success', 'utility', 'core', array(
			          'smoothboxClose' => true,
			          'parentRefresh'=> true,
			          'messages' => array(Zend_Registry::get('Zend_Translate')->_('This item is not existed!'))
			  		));
			  	}
			  }

			  return $this->_forward('success', 'utility', 'core', array(
			          'smoothboxClose' => true,
			          'parentRefresh'=> true,
			          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Successfully.'))
			  ));
			  
	    }
	    // Output
	    $this->renderScript('admin-moderations/delete.tpl');
	}
	
	public function viewAction()
	{
		$this->view->id= $id = $this->_getParam('id');
	    $this->view->type= $type = $this->_getParam('type');
	    
		if ( (!empty($id) && is_numeric($id)) && (!empty($type)) ){
			if ( Engine_Api::_()->hasItemType($type) ){
				// first get the item and then redirect admin to the item page
				$item = Engine_Api::_()->getItem($type, $id);
			    if ( $item ) 
			      	$this->_redirectCustom($item->getHref());
			    else 
			    	$this->view->missing = true;
			}
			else
				$this->view->missing = true;
		}
	}
}