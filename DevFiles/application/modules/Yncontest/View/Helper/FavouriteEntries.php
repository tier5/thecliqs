<?php

class Yncontest_View_Helper_FavouriteEntries extends Zend_View_Helper_Abstract{
	
	static private $_viewerId;
	
	static private $_textUrl;
	
	public function getViewerId(){
		if(self::$_viewerId === NULL){
			self::$_viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
		}
		return self::$_viewerId;
	}
	
	public function getUrlReturn() {
		if (self::$_textUrl === NULL) {
			$textUrl = base64_encode($_SERVER['REQUEST_URI']);
			self::$_textUrl = $textUrl;
		}
		return self::$_textUrl;
	}
	
	public function favouriteEntries($item){
		$xhtml = "";		
		$text = $this->getUrlReturn();
		$entry_id =  $item->getIdentity();
		if($item->isFavourited($this->getViewerId())){
			$xhtml =  sprintf('<a  href="javascript:void(0);" onclick="en4.yncontest.entriesfav(%s,%s,\'%s\')" class="entries_fav_unfavourite entries_fav_%s">%s</a>',$entry_id, $this->getViewerId(), $text, $entry_id , $this->view->translate('Unfavorite'));
		}else{
			$xhtml =  sprintf('<a href="javascript:void(0);" onclick="en4.yncontest.entriesfav(%s,%s,\'%s\')" class="entries_fav_favourite entries_fav_%s">%s</a>',$entry_id, $this->getViewerId(), $text, $entry_id , $this->view->translate('Favorite'));
		}
		return $xhtml;
	}
}
