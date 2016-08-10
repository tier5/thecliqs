<?php
class Ynjobposting_AdminIndustriesController extends Core_Controller_Action_Admin {
	
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_manage_industries');
	}

	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('industries', 'ynjobposting');
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
		$form = $this -> view -> form = new Ynjobposting_Form_Admin_Industry_Create();
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
		$tableIndustryMaps = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
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
		
		$hasCompany = $node -> checkHasCompany();
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynjobposting_company');
		$tableCompany = Engine_Api::_() -> getItemTable('ynjobposting_company');
		if ($hasCompany || (count($industries) > 0)) 
		{
			$this -> view -> moveIns = $moveIns = $node -> getMoveIndustriesByLevel($node -> level);
		}
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$move_industry_id = $this -> _getParam('move_industry');
			$node_id = $this -> getRequest() -> getPost('node_id', 0);
			// go through logs and see which classified used this industry and set it to ZERO
			if (is_object($node)) {
				if ($hasCompany || (count($industries) > 0)) {
					if ($move_industry_id != 'none') {
						//move companies to another industry
						if ($hasCompany) 
						{
							$companies = $tableCompany -> getCompaniesByIndustry($node -> industry_id);
							foreach ($companies as $company) 
							{
								$companyId = $company -> company_id;
								//TODO check if move to the category it was already belong to
								$row = $tableIndustryMaps -> checkExistIndustryByCompany($move_industry_id, $companyId);
								if(empty($row))
								{
									//set to be main
									$company -> industry_id = $move_industry_id;
									$company -> main = 1;
									$company -> save();
									
									//set others to sub
									$select = $tableIndustryMaps -> select() 
																 -> where('company_id = ?', $companyId)
																 -> where('industry_id NOT IN (?)', $move_industry_id);
									$subIndustries = $tableIndustryMaps -> fetchAll($select);
									foreach($subIndustries as $subIndustry)
									{
										if($subIndustry -> main  == 1)
										{
											$subIndustry -> main = 0;
											$subIndustry -> save();
										}
									}			
								}
								else
								{
									$company -> delete();
								}
								
								//TODO clear profile type
								// Remove old data custom fields 
								$old_industry = Engine_Api::_()->getItem('ynjobposting_industry', $node->industry_id);
								$tableMaps = Engine_Api::_() -> getDbTable('maps','ynjobposting');
								$tableValues = Engine_Api::_() -> getDbTable('values','ynjobposting');
								$tableSearch = Engine_Api::_() -> getDbTable('search','ynjobposting');
								$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_industry->option_id));
								$arr_ids = array();
								if(count($fieldIds) > 0)
								{
									//clear values in search table
									$searchItem  = $tableSearch->fetchRow($tableSearch -> select() -> where('item_id = ?', $companyId) -> limit(1));
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
										$searchItem -> save();
									//delele in values table
									if(count($arr_ids) > 0)
									{
										$valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $companyId) -> where('field_id IN (?)', $arr_ids));
										foreach($valueItems as $item)
										{
											$item -> delete();
										}
									}
								}
							}
						}
						//delete its type + node
						$this -> typeDelete($node -> option_id);
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
							$update_option_id = $item -> option_id;
							
							if($item -> level - $move_node -> level == 1)
							{
								$newNode = $table -> addChild($move_node, $arr_item);
								//delete profile type of new node
								$this -> typeDelete($newNode -> option_id);
								//update new node with old option_id
								$newNode -> option_id = $update_option_id;
								//udpate companies with new industry_id
								$list_company = $tableCompany -> getCompaniesByIndustry($update_industry_id);
								foreach($list_company as $item_company)
								{
									$item_company -> industry_id = $newNode -> industry_id;
									$item_company -> save();
								}
								$newNode -> save();
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
								$this -> typeDelete($newNode -> option_id);
								//update new node with old option_id
								$newNode -> option_id = $update_option_id;
								//udpate companies with new industry_id
								$list_company = $tableCompany -> getCompaniesByIndustry($update_industry_id);
								foreach($list_company as $item_company)
								{
									$item_company -> industry_id = $newNode -> industry_id;
									$item_company -> save();
								}
								$newNode -> save();
								$move_node = $newNode;
							}
						}
					} 
					else //delete all
					{
						$tableIndustryMaps = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
						$tableJob = Engine_Api::_() -> getItemTable('ynjobposting_job');
						//delete type + custom field of sub categories
						foreach ($industries as $item) 
						{
							$option = $optionData -> getRowMatching('label', $item -> title);
							if ($option) {
								$this -> typeDelete($option -> option_id);
							}
						}
						//delete its companies
						$companies = $tableCompany -> getAllChildrenCompaniesByIndustry($node);
						if (count($companies) > 0) {
							foreach ($companies as $item) {
								foreach ($item->toArray() as $company) {
									$delete_item = Engine_Api::_() -> getItem('ynjobposting_company', $company['company_id']);
									$db = $tableCompany -> getAdapter();
									try {
										$db -> beginTransaction();
										//delete job 
										$list_jobs = $tableJob -> getJobsByCompanyId($company['company_id']);
										foreach($list_jobs as $job)
										{
											if($job -> status != 'deleted')
											{
												$job -> delete();
												$owner = $job -> getOwner();
												$notifyApi -> addNotification($owner, $job, $job, 'ynjobposting_job_deleted');
											}
										}
										//delete company
										if($delete_item -> status != 'deleted')
										{
											$delete_item -> deleted = true;
											$delete_item -> status = 'deleted';
											$delete_item -> save();
											$company_owner = $delete_item -> getOwner();
											$notifyApi -> addNotification($company_owner, $delete_item, $delete_item, 'ynjobposting_company_deleted');
										}
										$db -> commit();
									} catch(Exception $e) {
										$db -> rollBack();
										throw $e;
									}
								}
							}
						}
						//delete its type + node
						$tableIndustryMaps -> deleteCompaniesByIndustryId($node -> industry_id);
						$this -> typeDelete($node -> option_id);
						$table -> deleteNode($node);
					}
				}
				else
				{
					$tableIndustryMaps = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
					$tableJob = Engine_Api::_() -> getItemTable('ynjobposting_job');
					//delete type + custom field of sub categories
						foreach ($industries as $item) 
						{
							$option = $optionData -> getRowMatching('label', $item -> title);
							if ($option) {
								$this -> typeDelete($option -> option_id);
							}
						}
						//delete its companies
						$companies = $tableCompany -> getAllChildrenCompaniesByIndustry($node);
						if (count($companies) > 0) {
							foreach ($companies as $item) {
								foreach ($item->toArray() as $company) {
									$delete_item = Engine_Api::_() -> getItem('ynjobposting_company', $company['company_id']);
									$db = $tableCompany -> getAdapter();
									try {
										$db -> beginTransaction();
										//delete job 
										$list_jobs = $tableJob -> getJobsByCompanyId($company['company_id']);
										foreach($list_jobs as $job)
										{
											if($job -> status != 'deleted')
											{
												$job -> delete();
												$owner = $job -> getOwner();
												$notifyApi -> addNotification($owner, $job, $job, 'ynjobposting_job_deleted');
											}
										}
										//delete company
										if($delete_item -> status != 'deleted')
										{
											$delete_item -> deleted = true;
											$delete_item -> status = 'deleted';
											$delete_item -> save();
											$company_owner = $delete_item -> getOwner();
											$notifyApi -> addNotification($company_owner, $delete_item, $delete_item, 'ynjobposting_company_deleted');
										}
										$db -> commit();
									} catch(Exception $e) {
										$db -> rollBack();
										throw $e;
									}
								}
							}
						}
						//delete its type + node
						$tableIndustryMaps -> deleteCompaniesByIndustryId($node -> industry_id);
						$this -> typeDelete($node -> option_id);
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
		$industry = Engine_Api::_() -> getItem('ynjobposting_industry', $id);

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Ynjobposting_Form_Admin_Industry_Edit();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$form -> populate($industry -> toArray());
		
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();
			$industry -> title = $values["title"];
			$industry -> save();
			$option = Engine_Api::_() -> fields() -> getOption($industry -> option_id, 'ynjobposting_company');
			$option -> label = $values["title"];
			$option -> save();
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-industries/form.tpl');
	}

	public function typeDelete($option_id) {
		$option = Engine_Api::_() -> fields() -> getOption($option_id, 'ynjobposting_company');
		$field = Engine_Api::_() -> fields() -> getField($option -> field_id, 'ynjobposting_company');

		// Validate input
		if ($field -> type !== 'profile_type') {
			throw new Exception('invalid input');
		}

		// Do not allow delete if only one type left
		if (count($field -> getOptions()) <= 1) {
			throw new Exception('only one left');
		}

		// Process
		Engine_Api::_() -> fields() -> deleteOption('ynjobposting_company', $option);

		// @todo reassign stuff
	}

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
