<?php

class Ynauction_FaqsController extends Core_Controller_Action_Standard {
	
	public function indexAction() {
		$this -> _helper -> content -> setEnabled();
		$Table = new Ynauction_Model_DbTable_Faqs;
		$select = $Table->select()->where('status=?','show')->order('ordering asc');
		$this->view->items = $items =  $Table->fetchAll($select);
	}

}
