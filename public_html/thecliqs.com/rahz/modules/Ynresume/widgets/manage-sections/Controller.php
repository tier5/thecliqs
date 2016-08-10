<?php
class Ynresume_Widget_ManageSectionsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
     	if(!$viewer -> getIdentity()) {
			$this -> setNoRender();
		}
        
        $this -> view -> resume = $resume = Engine_Api::_()->ynresume()->getUserResume();
        if(!$resume) {
            $this->setNoRender();
            return;   
        }
        if (!$resume->active) {
            $this->view->disable = true;
        }
	}
}
