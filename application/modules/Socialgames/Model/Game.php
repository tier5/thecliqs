<?php

class Socialgames_Model_Game extends Core_Model_Item_Abstract{
	protected $_parent_type = 'user';
    protected $_owner_type = 'user';
    protected $_parent_is_owner = true;
	
	public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'games_view',
            'reset' => true,
            'game_id' => $this->game_id,
            'slug' => $this->title,
            ), $params);

        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
    }
	
	public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    public function likes()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }
}