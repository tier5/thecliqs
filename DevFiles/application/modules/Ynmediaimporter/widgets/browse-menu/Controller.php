<?php

class Ynmediaimporter_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Get navigation
        $this -> view -> navigation = $navigation
         = Engine_Api::_() -> getApi('menus', 'core')
          -> getNavigation('ynmediaimporter_main', array(), null);
    }
}
