<?php
class Ynmusic_Model_Genre extends Core_Model_Item_Abstract {
	public function setTitle($newTitle) {
		$this -> title = $newTitle;
		$this -> save();
		return $this;
	}
	
	public function getTitle() {
		$view = Zend_Registry::get('Zend_View');
		return $view -> translate($this -> title);
	}
	
	public function getHref($params = array()) {
		$params = array_merge(array(
			'route' => 'ynmusic_general',
			'action' => 'listing',
			'reset' => true,
		), $params);
		
		if (!empty($params['type']) && in_array($params['type'], array('artist', 'album', 'song', 'playlist'))) {
			$params['route'] = 'ynmusic_'.$params['type'];
			$params['action'] = 'index';
			unset($params['type']);
		}
		
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset).'?genre='.$this->getTitle();
	}
}
