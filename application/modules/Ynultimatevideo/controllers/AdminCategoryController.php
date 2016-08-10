<?php
class Ynultimatevideo_AdminCategoryController extends Core_Controller_Action_Admin {
	protected $_paginate_params = array();
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynultimatevideo_admin_main', array(), 'ynultimatevideo_admin_main_categories');
	}

	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('categories', 'ynultimatevideo');
	}

	public function indexAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$this -> view -> categories = $node -> getChilren();
		$this -> view -> category = $node;
	}

	public function addCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$parentId = $this -> _getParam('parent_id', 0);
		$form = $this -> view -> form = new Ynultimatevideo_Form_Admin_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$table = $this -> getDbTable();
		$node = $table -> getNode($parentId);
		//maximum 4 level category
		if ($node -> level > 3) {
			throw new Zend_Exception('Maximum 4 levels of category.');
		}
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();
			$user = Engine_Api::_() -> user() -> getViewer();
			$data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
			$table -> addChild($node, $data);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-category/form.tpl');
	}

	public function deleteCategoryAction() {

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> category_id = $id;
		$table = $this -> getDbTable();
		$node = $table -> getNode($id);
		$categories = array();
		$table -> appendChildToTree($node, $categories);
		$level = $node -> level;
		unset($categories[0]);

		$hasVideo = $node -> checkHasVideo();
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynultimatevideo_video');
		$tableVideo = Engine_Api::_() -> getItemTable('ynultimatevideo_video');
		$this -> view -> moveCates = array();;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			// go through logs and see which classified used this category and set it to ZERO
			if (is_object($node)) {
				if ($hasVideo || (count($categories) > 0)) {

					//delete type + custom field of sub categories
					foreach ($categories as $item)
					{
						$option = $optionData -> getRowMatching('label', $item -> title);
						if ($option) {
							$this -> typeDelete($option -> option_id);
						}
					}
					//set video category to 0
					$videos = $tableVideo -> getAllChildrenVideosByCategory($node);
					if (count($videos) > 0) {
						foreach ($videos as $item) {
							foreach ($item->toArray() as $videoItem) {
								$modify_item = Engine_Api::_() -> getItem('ynultimatevideo_video', $videoItem['video_id']);
								$db = $tableVideo -> getAdapter();
								try {
									$db -> beginTransaction();
									$modify_item -> category_id = 0;
									$modify_item -> save();
									$db -> commit();
								} catch(Exception $e) {
									$db -> rollBack();
									throw $e;
								}
							}
						}
					}
					//delete its type + node
					$this -> typeDelete($node -> option_id);
					$table -> deleteNode($node);
				}
				else
				{
					//delete type + custom field of sub categories
					foreach ($categories as $item)
					{
						$option = $optionData -> getRowMatching('label', $item -> title);
						if ($option) {
							$this -> typeDelete($option -> option_id);
						}
					}
					//delete its type + node
					$this -> typeDelete($node -> option_id);
					$table -> deleteNode($node);
				}
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
	}

	public function editCategoryAction() {

		// Must have an id
		if (!($id = $this -> _getParam('id'))) {
			throw new Zend_Exception('No identifier specified');
		}
		// Generate and assign form
		$category = Engine_Api::_() -> getItem('ynultimatevideo_category', $id);

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Ynultimatevideo_Form_Admin_Category( array('category' => $category));
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$isSub = false;
		if ($category -> parent_id != '1') {
			$isSub = true;
		}

		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// edit category in the database
				// Transaction
				$row = Engine_Api::_() -> getItem('ynultimatevideo_category', $values["id"]);
				$row -> title = $values["label"];
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		$form -> setField($category, $isSub);

		// Output
		$this -> renderScript('admin-category/form.tpl');
	}

	public function ajaxUseParentCategoryAction() {
		$categoryId = $this -> _getParam('id');
		$category = Engine_Api::_() -> getItem('ynultimatevideo_category', $categoryId);
		$category -> save();
	}

	public function typeDelete($option_id) {
		$option = Engine_Api::_() -> fields() -> getOption($option_id, 'ynultimatevideo_video');
		$field = Engine_Api::_() -> fields() -> getField($option -> field_id, 'ynultimatevideo_video');

		// Validate input
		if ($field -> type !== 'profile_type') {
			throw new Exception('invalid input');
		}

		// Do not allow delete if only one type left
		if (count($field -> getOptions()) <= 1) {
			throw new Exception('only one left');
		}

		// Process
		Engine_Api::_() -> fields() -> deleteOption('ynultimatevideo_video', $option);

		// @todo reassign stuff
	}

	public function sortAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$categories = $node -> getChilren();
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $item) {
			$category_id = substr($item, strrpos($item, '_') + 1);
			foreach ($categories as $category) {
				if ($category -> category_id == $category_id) {
					$category -> order = $i;
					$category -> save();
				}
			}
		}
	}

}
