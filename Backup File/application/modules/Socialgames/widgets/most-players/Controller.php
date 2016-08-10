<?php

class Socialgames_Widget_MostPlayersController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
		$this->view->users_played = $users_played = Engine_Api::_()->getDbtable('play', 'socialgames')->getTopUsers();
		
        if(!count($users_played)){
            return $this->setNoRender();
        }
    }
}