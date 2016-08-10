<?php

class Yncontest_Widget_ItemCommentController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $subject = '';	
	    if( Engine_Api::_()->core()->hasSubject() ) { 
      		$subject = Engine_Api::_()->core()->getSubject();
		}
		if($subject!='')
		{
			$this->view->item = $subject;
			
			
		}
		else{
			$this->setNoRender(true);
			return;	
		}	
		
	}
}
