<?php

class Yncontest_Widget_ContestCategoriesController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$maxCategory = (int)$this -> _getParam('max',8);		
		$Model = new Yncontest_Model_DbTable_Categories();
		$select = $Model -> select();
		$select -> where('level = ? ', 1)->limit($maxCategory);
		$category = $Model->fetchAll($select);
		$this->view->category = $category;
	}
}
