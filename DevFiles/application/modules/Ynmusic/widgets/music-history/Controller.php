<?php
class Ynmusic_Widget_MusicHistoryController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity()) {
			return $this->setNoRender();
		}	
        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        $params['user_id'] = $viewer->getIdentity();
		
		$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('history', 'ynmusic')->getHistoryPaginator($params);
		$page = (!empty($params['page'])) ? $params['page'] : 1;
		$itemCountPerPage = (!empty($params['itemCountPerPage'])) ? $params['itemCountPerPage'] : 10;
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($itemCountPerPage);
		$this->view->paginator = $paginator;
		
		$this->view->formValues = $params;	
	}
}