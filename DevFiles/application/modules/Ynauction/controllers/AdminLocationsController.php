<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminLocationController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminLocationsController extends Core_Controller_Action_Admin {

	protected $_paginate_params = array();

	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_locations');
	}

	/**
	 *
	 *
	 * @return Ynauction_Model_DbTable_Location
	 */
	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('locations', 'ynauction');
	}

	public function indexAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$this -> view -> locations = $node -> getChilren();
		$this -> view -> location = $node;
	}

	public function addLocationAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$form = $this -> view -> form = new Ynauction_Form_Admin_Location();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble( array()));
		// Check post
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();
			$table = $this -> getDbTable();
			$parentId = $this -> _getParam('parent_id', 0);
			$node = $table -> getNode($parentId);
			$user = Engine_Api::_() -> user() -> getViewer();
			$data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
			$table -> addChild($node, $data);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-locations/form.tpl');
	}

	public function deleteLocationAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> location_id = $id;
        $options = Engine_Api::_()->getDbTable('locations','ynauction')->getDeleteOptions($id);
		if(!$options)
            $this->view->canNotDelete = true;
		$table = $this -> getDbTable();
		$node = $table -> find($id) -> current();
		$this->view->usedCount =  $usedCount = $node->getUsedCount();
		
		$moveNode = $this->view->moveNode = new Zend_Form_Element_Select('node_id', array(
			'label'=>'Location',
			'multiOptions'=> Engine_Api::_()->getDbTable('locations','ynauction')->getDeleteOptions($id),
			'description'=>'Select a location to relocale '.$usedCount. ' item(s)'
		));
		
		// Check post
		if($this -> getRequest() -> isPost()) {
			$node_id=  $this->getRequest()->getPost('node_id',0);
			// go through logs and see which classified used this category and set it to ZERO
			
			$node = $table -> find($id) -> current();
			if(is_object($node)) {
				$table -> deleteNode($node, $node_id);
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-locations/delete.tpl');
	}

	public function editLocationAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Ynauction_Form_Admin_Location();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble( array()));

		// Check post
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// edit category in the database
				// Transaction
				$row = Engine_Api::_() -> getItem('ynauction_location', $values["id"]);

				$row -> title = $values["label"];
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Must have an id
		if(!($id = $this -> _getParam('id'))) {
			throw new Zend_Exception('No identifier specified');
		}

		// Generate and assign form
		$location = Engine_Api::_() -> getItem('ynauction_location', $id);
		$form -> setField($location);

		// Output
		$this -> renderScript('admin-locations/form.tpl');
	}

}
