<?php

class Socialstore_View_Helper_Follow extends Zend_View_Helper_Abstract{
	
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
	public function follow($store){
		$xhtml = "";		
		$store_id =  $store->getIdentity();
		$text = $this->getUrlReturn();
		if($store->isFollowed($this->getViewerId())){
			$xhtml =  sprintf('<a href="javascript:void(0);" onclick="en4.store.follow(%s,%s,\'%s\')" class="store_follow_unfollow store_follow_%s" >%s</a>',$store_id, $this->getViewerId(), $text, $store_id , $this->view->translate('Unfollow'));
		}else{
			$xhtml =  sprintf('<a href="javascript:void(0);" onclick="en4.store.follow(%s,%s,\'%s\')" class="store_follow_follow store_follow_%s" >%s</a>',$store_id, $this->getViewerId(), $text, $store_id , $this->view->translate('Follow'));
		}
		return $xhtml;
	}
}
