<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Plugin_Menus {

    public function onMenuInitialize_YnultimatevideoMainManage() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YnultimatevideoMainCreate($row) {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('ynultimatevideo_video', $viewer, 'create')) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YnultimatevideoMainCreatePlaylist($row) {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

	public function canMigrateVideo() {
		return (Engine_Api::_()->hasModuleBootstrap('ynvideo') || Engine_Api::_()->hasModuleBootstrap('video')?true:false);
	}

}