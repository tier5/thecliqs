<?php
class Ynultimatevideo_Widget_ListHistoryController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity()) {
			return $this->setNoRender();
		}
        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        $params['user_id'] = $viewer->getIdentity();

		// paginator
		$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('history', 'ynultimatevideo')->getHistoryPaginator($params);
//		echo '<pre>',print_r($paginator->getTotalItemCount());die;
		$page = (!empty($params['page'])) ? $params['page'] : 1;
		$numberOfItems = $this->_getParam('numberOfItems', 10);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($numberOfItems);
		$this->view->paginator = $paginator;
		$this->view->formValues = $params;
	}
}