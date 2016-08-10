<?php

class Yncontest_Widget_CategoryController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{

		$table = Engine_Api::_() -> getDbtable('categories', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name);
		$select -> where('level = ? ', 1);
		$this -> view -> categories = $table -> fetchAll($select);
		$this -> view -> title = "Categories";
	}

}
