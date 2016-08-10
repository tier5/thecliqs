<?php
class Ynmusic_Model_Alonesong extends Core_Model_Item_Abstract {
	protected $_searchTriggers = false;
	
	public function getRichContent($view = false, $params = array()) {
		$zend_View = Zend_Registry::get('Zend_View');
	    // $view == false means that this rich content is requested from the activity feed
	    if($view == false){
			return $zend_View -> partial('_alonesong_feed.tpl', 'ynmusic', array('item' => $this));
	    }
  	}
	
	public function getSongs() {
		$songTable   = Engine_Api::_()->getDbTable('songs', 'ynmusic');
        $select  = $songTable -> select();
        $select -> where("song_id IN (?)", $this -> song_ids);
		$select -> order('order ASC');
        return $songTable -> fetchAll($select);
	}
}
