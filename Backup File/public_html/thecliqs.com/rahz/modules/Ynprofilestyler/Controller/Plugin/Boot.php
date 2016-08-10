<?php

class Ynprofilestyler_Controller_Plugin_Boot extends Zend_Controller_Plugin_Abstract
{
	private function _addSlideshow($user) {		
		$view = Zend_Registry::get('Zend_View');
		$staticBaseUrl = $view->layout()->staticBaseUrl;
		$view->headScript()
			->appendFile($staticBaseUrl	. 'application/modules/Ynprofilestyler/externals/scripts/Loop.js')
			//->appendFile($staticBaseUrl	. 'application/modules/Ynprofilestyler/externals/scripts/SlideShow.js')
			->appendFile($view->layout()->staticBaseUrl . 'application/modules/Ynprofilestyler/externals/scripts/core.js');
		/*
		$slideshow = Engine_Api::_()->ynprofilestyler()->getUserSlideshow($user->getIdentity());
		if ($slideshow) {
			$slides = $slideshow->getSlides(1);
				
			if ($slides != NULL && $slides->count() > 0) {
				$cfg = json_decode($slideshow->configure);
				$data = array(
					'html' => $view->partial('_slideshow.tpl', 'ynprofilestyler', array(
						'slides' => $slides,						
						'width' => $cfg->slideWidth,
						'height' => $cfg->slideHeight,
						'left' => $cfg->slideLeft
					)),
					'interval' => $cfg->slideInterval,
					'top' => $cfg->slideTop,
					'left' => $cfg->slideLeft,
					'distance' => $cfg->slideDistance,
					'width' => $cfg->slideWidth,
					'height' => $cfg->slideHeight
				);
				$view->headScript()->appendScript("
					window.addEvent('domready',function(e) {
						ynps.addSlideshow(".json_encode($data).");
					});
				");
			}
		}
		*/
	}
	
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $module =  $request->getModuleName();
        $controller =$request->getControllerName();
        $action =  $request->getActionName();
        
        if ($module == 'user' && $controller == 'profile' && $action == 'index') {
        	$id = $request->getUserParam('id', NULL);
        	if ($id != NULL) {
				$user = Engine_Api::_()->user()->getUser($id);
				$this->_addSlideshow($user);
        	}        	
        }
    }
}
