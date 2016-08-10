<?php

class Socialgames_Widget_GamesFeaturedController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
		
        $params = array(
            'is_active' => 1,
            'is_featured' => 1,
            'limit' => $this->_getParam('itemCountPerPage', 3)
        );

        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('games', 'socialgames')->getGamesPaginator($params);
		
        if($paginator->getTotalItemCount() <= 0){
            return $this->setNoRender();
        }
    }
}