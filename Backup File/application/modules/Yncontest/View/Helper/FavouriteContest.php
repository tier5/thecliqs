<?php

class Yncontest_View_Helper_Favourite extends Zend_View_Helper_Abstract{
	
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
	
	public function favouriteContest($item){
		$xhtml = "";		
		$text = $this->getUrlReturn();
		$contest_id =  $item->getIdentity();
		if($item->isFavourited($this->getViewerId())){
			$xhtml =  sprintf('<a  href="javascript:void(0);" onclick="en4.yncontest.fav(%s,%s,\'%s\')" class="contest_fav_unfavourite contest_fav_%s">%s</a>',$contest_id, $this->getViewerId(), $text, $contest_id , $this->view->translate('Unfavorite'));
		}else{
			$xhtml =  sprintf('<a href="javascript:void(0);" onclick="en4.yncontest.fav(%s,%s,\'%s\')" class="contest_fav_favourite contest_fav_%s">%s</a>',$contest_id, $this->getViewerId(), $text, $contest_id , $this->view->translate('Favorite'));
		}
		return $xhtml;
	}
}
