<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_DetailPlaylistInfoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_() -> core() -> hasSubject('ynvideochannel_playlist')) {
			return $this -> setNoRender();
		}
		$this -> view -> playlist = Engine_Api::_() -> core() -> getSubject();
	}
}
