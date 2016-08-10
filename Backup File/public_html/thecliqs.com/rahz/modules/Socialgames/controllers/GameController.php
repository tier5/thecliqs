<?php
class Socialgames_GameController extends Core_Controller_Action_Standard {
	
	public function init()
	{
		if (!$this->_helper->requireAuth()->setAuthParams('socialgames_game', null, 'view')->isValid())
            return;

        $id = $this->_getParam('game_id', 0);
		
        if ($id) {
            $game = Engine_Api::_()->getItem('socialgames_game', $id);
            if ($game) {
                Engine_Api::_()->core()->setSubject($game);
            } else {
                return $this->_forward('notfound', 'error', 'core');
            }
        }
        if( !$this->_helper->requireAuth()->setAuthParams('socialgames_game', null, 'view')->isValid()) return;
	}
	public function indexAction()
	{
		$subject = Engine_Api::_()->core()->getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		//ADD VIEW
		$gameTable = Engine_Api::_()->getDbtable('games', 'socialgames');
		
		$gameTable->update(array(
			'total_views' => new Zend_Db_Expr('total_views + 1'),
		  ), array(
			'game_id = ?' => $subject->getIdentity(),
		  ));
		$subject->total_views++;
		
		//IS FAVOURITE
		$row = Engine_Api::_()->getDbtable('favourite', 'socialgames')->fetchRow(array("user_id = ?" => $viewer->getIdentity(),"game_id = ?" => $subject->game_id));
		if ($row)
		{
			$is_favourite = 1;
		}
		
		//IS PLAYED
		$row = Engine_Api::_()->getDbtable('play', 'socialgames')->fetchRow(array("user_id = ?" => $viewer->getIdentity(),"game_id = ?" => $subject->game_id));
		if ($row)
		{
			$is_played = 1;
		}
		
		//GET USERS
		$users_played = Engine_Api::_()->getDbtable('play', 'socialgames')->getUsers($subject->game_id);
		
		$this->view->game = $subject;
		$this->view->users_played = $users_played;
		$this->view->is_favourite = $is_favourite;
		$this->view->is_played = $is_played;
		
		
		
		// Render
		$this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
	}
}