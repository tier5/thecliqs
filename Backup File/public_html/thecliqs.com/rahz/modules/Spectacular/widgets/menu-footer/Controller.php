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
class Spectacular_Widget_MenuFooterController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("spectacular_footer");
        $spectacular_landing_page_footer_menu = Zend_Registry::isRegistered('spectacular_landing_page_footer_menu') ? Zend_Registry::get('spectacular_landing_page_footer_menu') : null;
        if (empty($spectacular_landing_page_footer_menu)) {
            return $this->setNoRender();
        }
    }

}
