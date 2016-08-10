<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_FeaturedChannelsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $params['featured'] = 1;
        $params['search'] = 1;
        $table = Engine_Api::_()->getItemTable('ynvideochannel_channel');
        $this->view->paginator = $paginator = $table->getChannelsPaginator($params);
        $paginator->setItemCountPerPage($this -> _getParam('itemCountPerPage', 6));

        // Hide if nothing to show
        if (!$paginator -> getTotalItemCount()) {
            return $this->setNoRender();
        }
    }
}
