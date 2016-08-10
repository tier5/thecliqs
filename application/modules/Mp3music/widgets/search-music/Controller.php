<?php
class Mp3music_Widget_SearchMusicController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	  $request = Zend_Controller_Front::getInstance()->getRequest();
	  $params = $request->getParams ();
	  if(empty($params['search']))
	  {
	  	$params['search'] = 'songs';
			$params['title'] = '';
	  }
		$params['title'] = htmlspecialchars($params['title']);
	  $this->view->params = $params; 
  }
}