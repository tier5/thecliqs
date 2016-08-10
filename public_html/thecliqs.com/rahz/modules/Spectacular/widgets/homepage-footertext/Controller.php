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
class Spectacular_Widget_HomepageFootertextController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->isSitemenuExist = Engine_Api::_()->hasModuleBootstrap('sitemenu');
        $this->view->sitemenuEnable = false;
        $this->view->show_signup_popup_footer = $this->_getParam("show_signup_popup_footer", 1);

        $spectacular_landing_page_footertext = Zend_Registry::isRegistered('spectacular_landing_page_footertext') ? Zend_Registry::get('spectacular_landing_page_footertext') : null;
        if (empty($spectacular_landing_page_footertext)) {
            return $this->setNoRender();
        }

        if (Engine_Api::_()->hasModuleBootstrap('sitemenu')) {
            $this->view->sitemenu_mini_menu_widget = Zend_Registry::isRegistered('sitemenu_mini_menu_widget') ? Zend_Registry::get('sitemenu_mini_menu_widget') : null;
            $this->view->sitemenuEnable = true;

            $front = Zend_Controller_Front::getInstance();
            $module = $front->getRequest()->getModuleName();
            $action = $front->getRequest()->getActionName();
            $controller = $front->getRequest()->getControllerName();
            $this->view->isPost = $front->getRequest()->isPost();

            if (($module == 'user' && $controller == 'auth' && $action == 'login') || ($module == 'core' && $controller == 'error' && $action == 'requireuser')) {
                $this->view->isUserLoginPage = true;
            }
            if ($module == 'user' && $controller == 'signup' && $action == 'index') {
                $this->view->isUserSignupPage = true;
            }
            if ($module == 'core' && $controller == 'error' && $action == 'notfound') {
                $this->view->isUserSignupPage = true;
            }
        }

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $isSubscriptionEnabled = false;
        $this->view->show_signup_popup = true;
        if (empty($viewer_id)) {
            $tempClassArray = array(
                'Payment_Plugin_Signup_Subscription',
                'Sladvsubscription_Plugin_Signup_Subscription'
            );
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $subscriptionObj = $db->query('SELECT `class` FROM `engine4_user_signup` WHERE  `enable` = 1 ORDER BY `engine4_user_signup`.`order` ASC LIMIT 1')->fetch();
            if (!empty($subscriptionObj) && isset($subscriptionObj['class']) && !empty($subscriptionObj['class']) && in_array($subscriptionObj['class'], $tempClassArray)) {
                $isSubscriptionEnabled = true;
            }
        }
        if (!empty($isSubscriptionEnabled)) {
            $this->view->show_signup_popup = false;
        }
    }

}
