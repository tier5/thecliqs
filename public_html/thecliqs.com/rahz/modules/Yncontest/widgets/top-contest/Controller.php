<?php
class Yncontest_Widget_TopContestController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){		
		$limit = $this->_getParam('number',5);
		$params = array(
			"contest_status" => "published",
			"approve_status" => "approved",
			"limit" => $limit,
			"orderby" => 'like_count',
			'direction' => 'desc'
		);
		$this -> view -> items = $items = Engine_Api::_() -> yncontest() -> getContestPaginator($params);
		if($items  -> getTotalItemCount() == 0) {
			$this -> setNoRender();
		}
	}
}
