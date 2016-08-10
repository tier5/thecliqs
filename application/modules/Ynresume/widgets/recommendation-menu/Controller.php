<?php
class Ynresume_Widget_RecommendationMenuController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
    	
        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()
            ->getApi('menus', 'core')
            ->getNavigation('ynresume_recommendation', array());
			
        if( count($this->view->navigation) == 1 ) {
            $this->view->navigation = null;
        }
		
    }
}
