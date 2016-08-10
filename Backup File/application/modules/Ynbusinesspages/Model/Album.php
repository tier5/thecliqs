<?php
class Ynbusinesspages_Model_Album extends Core_Model_Item_Collection {
	protected $_parent_type = 'ynbusinesspages_business';
	protected $_type = 'ynbusinesspages_album';
	protected $_owner_type = 'ynbusinesspages_business';

	protected $_children_types = array('ynbusinesspages_photo');

	protected $_collectible_type = 'ynbusinesspages_photo';

	public function getHref($params = array()) 
	{
		$params = array_merge(array('route' => 'ynbusinesspages_profile', 'reset' => true, 'id' => $this -> getParent('ynbusinesspages_business') -> getIdentity(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getAuthorizationItem() {
		return $this -> getParent('ynbusinesspages_business');
	}

	protected function _delete() {
		// Delete all child posts
		$photoTable = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
		$photoSelect = $photoTable -> select() -> where('album_id = ?', $this -> getIdentity());
		foreach ($photoTable->fetchAll($photoSelect) as $businessPhoto) {
			$businessPhoto -> delete();
		}

		parent::_delete();
	}

	public function getFeaturedPaginator($params) {
		$tbl_photos = Engine_Api::_() -> getItemTable('ynbusinesspages_photo');
		$photoSelect = $tbl_photos -> select() -> where('album_id = ?', $this -> getIdentity());

		if (isset($params['is_featured'])) {
			$photoSelect -> where("is_featured = ?", $params['is_featured']);
		}
		return Zend_Paginator::factory($photoSelect);
	}

}
