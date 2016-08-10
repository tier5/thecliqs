<?php

class Socialgames_Widget_ProfileGamesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
		$subject = Engine_Api::_()->core()->getSubject();
		$this->view->users_played = $users_played = Engine_Api::_()->getDbtable('play', 'socialgames')->getUserPlayed($subject->getIdentity());
		
        if(!count($users_played)){
            return $this->setNoRender();
        }
    }
}