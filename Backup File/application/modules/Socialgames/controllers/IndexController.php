<?php
class Socialgames_IndexController extends Core_Controller_Action_Standard {
	public function init()
	{
		if (!$this->_helper->requireAuth()->setAuthParams('socialgames_game', null, 'view')->isValid())
            return;
	}
	public function browseAction()
	{
		// Prepare data
		$viewer = Engine_Api::_()->user()->getViewer();
		
		// Note: this code is duplicated in the blog.browse-search widget
		$this->view->form = $form = new Socialgames_Form_Search();

		// Process form
		$form->isValid($this->_getAllParams());
		$values = $form->getValues();
		$this->view->formValues = array_filter($values);
		$values['is_active'] = "1";
		
		$this->view->assign($values);
		
		// Get blogs
		$paginator = Engine_Api::_()->getDbtable('games', 'socialgames')->getGamesPaginator($values);

		$items_per_page = 18;
		$paginator->setItemCountPerPage($items_per_page);

		$this->view->paginator = $paginator->setCurrentPageNumber( $values['page'] );
		
		// Render
		$this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
	}
	
	public function randomAction()
	{
		$game = Engine_Api::_()->getDbtable('games', 'socialgames')->select()->order("RAND()")->limit(1)->query()->fetch();
		return $this->_helper->redirector->gotoRoute(array('game_id' => $game["game_id"],"slug"=>$game["title"]), 'games_view', true);
	}
	
	public function favouriteAction()
	{
		// Prepare data
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity())
		{
			return $this->_helper->redirector->gotoRoute(array(),'user_login');
		}
		// Note: this code is duplicated in the blog.browse-search widget
		$this->view->form = $form = new Socialgames_Form_Search();

		// Process form
		$form->isValid($this->_getAllParams());
		$values = $form->getValues();
		$this->view->formValues = array_filter($values);
		$values['is_active'] = "1";
		$values['is_favourite'] = 1;
		
		$this->view->assign($values);
		
		// Get blogs
		$paginator = Engine_Api::_()->getDbtable('games', 'socialgames')->getGamesPaginator($values);

		$items_per_page = 18;
		$paginator->setItemCountPerPage($items_per_page);

		$this->view->paginator = $paginator->setCurrentPageNumber( $values['page'] );
		
		// Render
		$this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
	}
	public function featuredAction()
	{
		// Prepare data
		$viewer = Engine_Api::_()->user()->getViewer();
		
		// Note: this code is duplicated in the blog.browse-search widget
		$this->view->form = $form = new Socialgames_Form_Search();

		// Process form
		$form->isValid($this->_getAllParams());
		$values = $form->getValues();
		$this->view->formValues = array_filter($values);
		$values['is_active'] = "1";
		$values['is_featured'] = "1";
		
		$this->view->assign($values);
		
		// Get blogs
		$paginator = Engine_Api::_()->getDbtable('games', 'socialgames')->getGamesPaginator($values);

		$items_per_page = 18;
		$paginator->setItemCountPerPage($items_per_page);

		$this->view->paginator = $paginator->setCurrentPageNumber( $values['page'] );
		
		// Render
		$this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
	}
}