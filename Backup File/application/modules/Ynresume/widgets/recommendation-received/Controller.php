<?php
class Ynresume_Widget_RecommendationReceivedController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            $this->setNoRender();
            return;
        }
        
        $this->view->occupations = $occupations = Engine_Api::_()->ynresume()->getOccupations();
	}
}