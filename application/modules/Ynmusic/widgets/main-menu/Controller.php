<?php
class Ynmusic_Widget_MainMenuController extends Engine_Content_Widget_Abstract  {
    public function indexAction() {
        // Get navigation
        $this->view->navigation = Engine_Api::_()
            ->getApi('menus', 'core')
            ->getNavigation('ynmusic_main', array());
      
        if( count($this->view->navigation) == 1 ) {
            $this->view->navigation = null;
        }
    }
}
