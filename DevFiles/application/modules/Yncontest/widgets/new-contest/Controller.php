<?php
class Yncontest_Widget_NewContestController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){		
		
		$this->view->limit = $limit = (int)$this->_getParam('number',8);
		$this->view->height = (int)$this -> _getParam('height',200);
		$this->view->width = (int)$this -> _getParam('width',200);		
		$params = array(			
			'contest_status' => 'published',
			'approve_status' => 'approved',
			'order' => 'approved_date',
			'limit' => $limit
		);		
		
		$this -> view -> items = $paginator = Engine_Api::_()->yncontest()->getContestPaginator($params);
		
		if($paginator->getTotalItemCount()==0) {
			$this -> setNoRender();			
		}
	}
}
