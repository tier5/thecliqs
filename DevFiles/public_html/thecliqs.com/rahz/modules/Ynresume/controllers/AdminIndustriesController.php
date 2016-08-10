<?php
class Ynresume_AdminIndustriesController extends Core_Controller_Action_Admin {
	
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresume_admin_main', array(), 'ynresume_admin_main_industries');
	}

	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('industries', 'ynresume');
	}

	public function indexAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$this -> view -> industries = $node -> getChilren();
		$this -> view -> industry  =  $node;
	}

	public function addIndustryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$parentId = $this -> _getParam('parent_id', 0);
		$form = $this -> view -> form = new Ynresume_Form_Admin_Industry_Create();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$table = $this -> getDbTable();
		$node = $table -> getNode($parentId);
		//maximum 3 level industry
		if ($node -> level > 2) {
			throw new Zend_Exception('Maximum 3 levels of industry.');
		}
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the industry
			$values = $form -> getValues();
			$user = Engine_Api::_() -> user() -> getViewer();
			$data = array('user_id' => $user -> getIdentity(), 'title' => $values["title"]);
			$table -> addChild($node, $data);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-industries/form.tpl');
	}

	public function deleteIndustryAction() {
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> industry_id = $id;
		$table = $this -> getDbTable();
		$node = $table -> getNode($id);
		$industries = array();
		$table -> appendChildToTree($node, $industries);
		$level = $node -> level;
		unset($industries[0]);
		
		$hasResume = $node -> checkHasResume();
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynresume_resume');
		$tableResume = Engine_Api::_() -> getItemTable('ynresume_resume');
		if ($hasResume || (count($industries) > 0)) 
		{
			$this -> view -> moveIns = $moveIns = $node -> getMoveIndustriesByLevel($node -> level);
		}
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$move_industry_id = $this -> _getParam('move_industry');
			$node_id = $this -> getRequest() -> getPost('node_id', 0);
			// go through logs and see which classified used this industry and set it to ZERO
			if (is_object($node)) {
				if ($hasResume || (count($industries) > 0)) {
					if ($move_industry_id != 'none') {
						//move its resume to another industry
						if ($hasResume) 
						{
							$resumes = $tableResume -> getResumesByIndustry($node -> industry_id);
							foreach ($resumes as $resume) 
							{
								$resumeId = $resume -> resume_id;
								$resume -> industry_id = $move_industry_id;
								$resume -> save();
								
								// clear profile type
								// Remove old data custom fields 
								$old_industry = Engine_Api::_()->getItem('ynresume_industry', $node->industry_id);
								$tableMaps = Engine_Api::_() -> getDbTable('maps','ynresume');
								$tableValues = Engine_Api::_() -> getDbTable('values','ynresume');
								$tableSearch = Engine_Api::_() -> getDbTable('search','ynresume');
								//$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_industry->option_id));
								$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  1));
								$arr_ids = array();
								if(count($fieldIds) > 0)
								{
									//clear values in search table
									$searchItem  = $tableSearch->fetchRow($tableSearch -> select() -> where('item_id = ?', $resumeId) -> limit(1));
									foreach($fieldIds as $id)
									{
										try{
											$column_name = 'field_'.$id -> child_id;
											if($searchItem)
											{
												$searchItem -> $column_name = NULL;
											}
											$arr_ids[] = $id -> child_id;
										}
										catch(exception $e)
										{
											continue;
										}
									}
									if($searchItem)
									{
										$searchItem -> save();
									}	
									//delele in values table
									if(count($arr_ids) > 0)
									{
										$valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $resumeId) -> where('field_id IN (?)', $arr_ids));
										foreach($valueItems as $item)
										{
											$item -> delete();
										}
									}
								}
							}
						}
						//delete its type + node
						//$this -> typeDelete($node -> option_id);
						$table -> deleteNode($node);
						//move sub industry
						$move_node = $table -> getNode($move_industry_id);
						foreach ($industries as $item) 
						{
							
							$arr_item = $item -> toArray();
							unset($arr_item['industry_id']);
							unset($arr_item['parent_id']);
							unset($arr_item['pleft']);
							unset($arr_item['pright']);
							
							$update_industry_id = $item -> industry_id;
							//$update_option_id = $item -> option_id;
							
							if($item -> level - $move_node -> level == 1)
							{
								$newNode = $table -> addChild($move_node, $arr_item);
								//delete profile type of new node
								//$this -> typeDelete($newNode -> option_id);
								//update new node with old option_id
								//$newNode -> option_id = $update_option_id;
								//udpate resumes with new industry_id
								$list_resume = $tableResume -> getResumesByIndustry($update_industry_id);
								foreach($list_resume as $item_resume)
								{
									$item_resume -> industry_id = $newNode -> industry_id;
									$item_resume -> save();
								}
								$newNode -> save();
								
								//update jobposting map
								$tableIndustryMaps = Engine_Api::_() -> getDbTable('industrymaps', 'ynresume');
								$row = $tableIndustryMaps -> getRowByIndustryId($update_industry_id);
								if(!empty($row))
								{
									$row -> industry_id = $newNode -> industry_id;
									$row -> save();	
								}	
								$move_node = $newNode;
								
							}
							else
							{
								while($item -> level - $move_node -> level < 1)
								{
									$move_node = $table -> getNode($move_node -> parent_id);
								}
								$newNode = $table -> addChild($move_node, $arr_item);
								//delete profile type of new node
								//$this -> typeDelete($newNode -> option_id);
								//update new node with old option_id
								//$newNode -> option_id = $update_option_id;
								//udpate resumes with new industry_id
								$list_resume = $tableResume -> getResumesByIndustry($update_industry_id);
								foreach($list_resume as $item_resume)
								{
									$item_resume -> industry_id = $newNode -> industry_id;
									$item_resume -> save();
								}
								$newNode -> save();
								
								//update jobposting map
								$tableIndustryMaps = Engine_Api::_() -> getDbTable('industrymaps', 'ynresume');
								$row = $tableIndustryMaps -> getRowByIndustryId($update_industry_id);
								if(!empty($row))
								{
									$row -> industry_id = $newNode -> industry_id;
									$row -> save();	
								}	
								
								$move_node = $newNode;
							}
						}
					} 
					else //delete all
					{
						//delete type + custom field of sub categories
						foreach ($industries as $item) 
						{
							$option = $optionData -> getRowMatching('label', $item -> title);
							if ($option) {
								//$this -> typeDelete($option -> option_id);
							}
						}
						//delete its resumes
						$resumes = $tableResume -> getAllChildrenIdeasByIndustry($node);
						if (count($resumes) > 0) {
							foreach ($resumes as $item) {
								foreach ($item->toArray() as $resume) {
									$delete_item = Engine_Api::_() -> getItem('ynresume_resume', $resume['resume_id']);
									$db = $tableResume -> getAdapter();
									try {
										$db -> beginTransaction();
										//delete
										$delete_item -> delete();
										$db -> commit();
									} catch(Exception $e) {
										$db -> rollBack();
										throw $e;
									}
								}
							}
						}
						//delete its type + node
						//$this -> typeDelete($node -> option_id);
						$table -> deleteNode($node);
					}
				}
				else
				{
					//delete type + custom field of sub categories
						foreach ($industries as $item) 
						{
							$option = $optionData -> getRowMatching('label', $item -> title);
							if ($option) {
								//$this -> typeDelete($option -> option_id);
							}
						}
						//delete its resumes
						$resumes = $tableResume -> getAllChildrenIdeasByIndustry($node);
						if (count($resumes) > 0) {
							foreach ($resumes as $item) {
								foreach ($item->toArray() as $resume) {
									$delete_item = Engine_Api::_() -> getItem('ynresume_resume', $resume['resume_id']);
									$db = $tableResume -> getAdapter();
									try {
										$db -> beginTransaction();
										$delete_item -> delete();
										$db -> commit();
									} catch(Exception $e) {
										$db -> rollBack();
										throw $e;
									}
								}
							}
						}
						//delete its type + node
						//$this -> typeDelete($node -> option_id);
						$table -> deleteNode($node);
				}
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
	}

	public function editIndustryAction() {

		// Must have an id
		if (!($id = $this -> _getParam('id'))) {
			throw new Zend_Exception('No identifier specified');
		}
		// Generate and assign form
		$industry = Engine_Api::_() -> getItem('ynresume_industry', $id);

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Ynresume_Form_Admin_Industry_Edit();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$form -> populate($industry -> toArray());
		
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();
			$industry -> title = $values["title"];
			$industry -> save();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-industries/form.tpl');
	}
	
	/* 
	public function typeDelete($option_id) {
		$option = Engine_Api::_() -> fields() -> getOption($option_id, 'ynresume_resume');
		$field = Engine_Api::_() -> fields() -> getField($option -> field_id, 'ynresume_resume');

		// Validate input
		if ($field -> type !== 'profile_type') {
			throw new Exception('invalid input');
		}

		// Do not allow delete if only one type left
		if (count($field -> getOptions()) <= 1) {
			throw new Exception('only one left');
		}

		// Process
		Engine_Api::_() -> fields() -> deleteOption('ynresume_resume', $option);

		// @todo reassign stuff
	} */

	public function sortAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$industries = $node -> getChilren();
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $item) {
			$industry_id = substr($item, strrpos($item, '_') + 1);
			foreach ($industries as $industry) {
				if ($industry -> industry_id == $industry_id) {
					$industry -> order = $i;
					$industry -> save();
				}
			}
		}
	}

}
