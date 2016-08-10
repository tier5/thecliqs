<?php
class Yncontest_Content_Widget_ContestSmallList extends Engine_Content_Widget_Abstract {

	/**
	 * @var int [0,20]
	 */
	protected $_limit = 5;

	public function init() {
		
		// set script path for all item
		$this -> setScriptPath('application/modules/Yncontest/views/scripts/widgets/contest-small-list');

		$limit = (int)$this -> _getParam('max');
		$limit = $limit < 1 ? 5 : $limit;

		// check some thing else
		$this -> _limit = $limit > 10 ? 5 : $limit;		
	}

}
