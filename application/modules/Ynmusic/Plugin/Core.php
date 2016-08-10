<?php
class Ynmusic_Plugin_Core {
	public function onRenderLayoutDefault($event) {
        // Arg should be an instance of Zend_View
        $view = $event->getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
        $module = $request->getParam('module');
		$isPages = (Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_player_setting', 0)) ? true : ($module == 'ynmusic');
        if($view instanceof Zend_View && $isPages) {
        	$script = 'en4.core.language.addData({"ynmusic_can_not_play_item":"'.$view->translate('ynmusic_can_not_play_item').'"});'; 
            $view->headScript()->prependFile($view->layout()->staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/render-music-player.js');
        	$view->headScript()->prependScript($script);
        	$view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Ynmusic/externals/styles/mediaelementplayer.css');
			if (!Engine_Api::_()->hasModuleBootstrap('mp3music')) {
				$view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/mediaelement-and-player.min.js');
			}
		}
    }
    
	public function onRenderLayoutMobileDefault($event) {
		$view = $event->getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
        $module = $request->getParam('module');
		$isPages = (Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_player_setting', 0)) ? true : ($module == 'ynmusic');
        
        if($view instanceof Zend_View && $isPages) {
        	$script = 'en4.core.language.addData({"ynmusic_can_not_play_item":"'.$view->translate('ynmusic_can_not_play_item').'"});';
            $view->headScript()->prependFile($view->layout()->staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/render-music-player.js');
			$view->headScript()->prependScript($script);
			$view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Ynmusic/externals/styles/mediaelementplayer.css');
			if (!Engine_Api::_()->hasModuleBootstrap('mp3music')) {
				$view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/mediaelement-and-player.min.js');
			}
        }
	}
}
