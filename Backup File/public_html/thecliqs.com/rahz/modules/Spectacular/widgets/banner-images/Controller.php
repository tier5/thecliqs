<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Widget_BannerImagesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->getElement()->removeDecorator('Title');
        $this->view->defaultDuration = $this->_getParam("speed", 5000);
        $this->view->slideWidth = $this->_getParam("width", null);
        $this->view->slideHeight = $this->_getParam("height", 583);
        $this->view->showBanners = $this->_getParam('showBanners', 1);
        $selectedBanners = array();
        if (!$this->view->showBanners) {
            $selectedBanners = $this->_getParam('selectedBanners');

            if (empty($selectedBanners)) {
                return $this->setNoRender();
            }

            $this->view->list = $getBanners = Engine_Api::_()->getItemTable('spectacular_banner')->getBanners(array('enabled' => 1, 'selectedBanners' => $selectedBanners), array('file_id'));
        } else {
            $this->view->list = $getBanners = Engine_Api::_()->getItemTable('spectacular_banner')->getBanners(array('enabled' => 1), array('file_id'));
        }
        $order = $this->_getParam("order", 2);
        $spectacular_landing_page_banner_image = Zend_Registry::isRegistered('spectacular_landing_page_banner_image') ? Zend_Registry::get('spectacular_landing_page_banner_image') : null;
        if (!COUNT($getBanners)) {
            $this->view->list = array("banner.jpg", "banner2.jpg", "banner3.jpg");
        } else {
            $getBannersArray = $getBanners->toArray();
            if (!empty($order) && $order == 1) {
                $getBannersArray = @array_reverse($getBannersArray);
            } else if (!empty($order) && $order == 2) {
                @shuffle($getBannersArray);
            }
            $this->view->list = $getBannersArray;
        }

        $this->view->spectacularHtmlTitle = $this->_getParam("spectacularHtmlTitle", "Events & Groups that you'd love");
        $this->view->spectacularHtmlDescription = $this->_getParam("spectacularHtmlDescription", "Discover new events in your town, interact with other party-goers and share the fun!");

        if (empty($spectacular_landing_page_banner_image)) {
            return $this->setNoRender();
        }
    }

}
