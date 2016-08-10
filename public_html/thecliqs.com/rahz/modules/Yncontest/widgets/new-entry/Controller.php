<?php
class Yncontest_Widget_NewEntryController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		$limit = $this->_getParam('number', 6);
		$values = array(
			'max' => $limit,
			'status' => 'published',
			'browseby' => 'modified_date'
		);
		$table = Engine_Api::_()->getItemTable('yncontest_entries');
		$select = Engine_Api::_()->yncontest()->getEntries($values);
		$this -> view -> entries = $items = $table -> fetchAll($select);
		if(!count($items)) {
			return $this -> setNoRender();
		}
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();	
	}
}
