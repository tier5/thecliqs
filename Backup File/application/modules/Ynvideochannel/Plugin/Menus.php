<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Plugin_Menus
{
    public function onMenuInitialize_YnvideochannelMainManage()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YnvideochannelMainSubscriptions()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

    public function canShareVideos()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('ynvideochannel_video', $viewer, 'create')) {
            return false;
        }

        return true;
    }

    public function canAddChannel()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->authorization()->isAllowed('ynvideochannel_channel', $viewer, 'create')) {
            return false;
        }

        return true;
    }

    public function canAddPlaylist()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('ynvideochannel_playlist', $viewer, 'create')) {
            return false;
        }

        return true;
    }

}