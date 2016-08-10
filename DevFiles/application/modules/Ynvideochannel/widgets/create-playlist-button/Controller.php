<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_CreatePlaylistButtonController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity() || !Engine_Api::_()->authorization()->isAllowed('ynvideochannel_playlist', $viewer, 'create')) {
            $this->setNoRender();
        }
    }
}
