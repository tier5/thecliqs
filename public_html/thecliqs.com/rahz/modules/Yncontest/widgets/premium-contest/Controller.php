<?php

class Yncontest_Widget_PremiumContestController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){		
		$limit = $this->_getParam('number',5);
		$params = array(
			"contest_status" => "published",
			'premium_id' => 1,
			"approve_status" => "approved",
			"limit" => $limit,
			"orderby" => 'start_date',
			'direction' => 'desc'
		);
		$this -> view -> items = $items = Engine_Api::_() -> yncontest() -> getContestPaginator($params);
		if($items  -> getTotalItemCount() == 0) {
			$this -> setNoRender();
		}		
	}
}
