<?php
class Yncontest_Widget_EndingSoonContestController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){		
		
		$this->view->limit = $limit = (int)$this->_getParam('number',8);		
		$this->view->height = (int)$this -> _getParam('height',200);
		$this->view->width = (int)$this -> _getParam('width',220);
		$before = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncontest.endingsoonbefore', 30);
		
		$params = array(
			'contest_status' => 'published',
			'approve_status' => 'approved',
			'endingsoon_id' => 1,
			'limit' => $limit,
			'order_by_str' => "datediff(end_date,now()) <= $before"
		);		
		
		$this -> view -> items = $paginator = Engine_Api::_()->yncontest()->getContestPaginator($params);
		
		if($paginator->getTotalItemCount()==0) {
			$this -> setNoRender();			
		}
		
	}
}
