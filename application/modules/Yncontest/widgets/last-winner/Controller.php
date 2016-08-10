<?php
class Yncontest_Widget_LastWinnerController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		$Model = new Yncontest_Model_DbTable_Entries;
		$select = $Model -> select();
		$limit = 6;
        if($this->_getParam('number') != '' && $this->_getParam('number') >= 0)
        {
            $limit = $this->_getParam('number');
        }
		$select -> where("entry_status = 'win'")
				-> order('start_date desc')
				-> limit($limit);

		$this -> view -> entries = $entries = $Model -> fetchAll($select);
		$this -> view -> totalItems = $totalItems = count($entries);	
		if(!$totalItems) {
			$this -> setNoRender();
		}
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();	
	}
}
