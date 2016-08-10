<?php
class Ynmusic_Widget_RecentPlayedController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Get paginator
		$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('history', 'ynmusic')->getHistoryPaginator(array());
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$page = $request -> getParam('page', 1);
		//$this -> _getParam('itemCountPerPage', 8)
		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 5));
		$paginator -> setCurrentPageNumber($page);

	}
}
?>
