<?php

class Socialgames_Widget_SimilarGamesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
		
		$subject = Engine_Api::_()->core()->getSubject();
        $params = array(
			'category' => $subject->category,
            'is_active' => 1,
            'limit' => $this->_getParam('itemCountPerPage', 3),
			'orderby' => "RAND()"
        );

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('games', 'socialgames')->getGamesPaginator($params);
		
        if($paginator->getTotalItemCount() < 0){
            return $this->setNoRender();
        }
    }
}