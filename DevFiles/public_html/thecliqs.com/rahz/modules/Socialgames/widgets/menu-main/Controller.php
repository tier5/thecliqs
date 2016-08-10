<?php

class Socialgames_Widget_MenuMainController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('socialgames_main', array(), '');
    }
}