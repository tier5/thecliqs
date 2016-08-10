<?php
class Ynresume_Widget_ViewSectionsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this -> view -> resume = $resume = Engine_Api::_()->core()->getSubject();
        if(!$resume || !$resume->isViewable()) {
            $this->setNoRender();
            return;   
        }
	}
}
