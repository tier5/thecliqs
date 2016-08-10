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
class Spectacular_Widget_ImagesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->getElement()->removeDecorator('Title');
        $this->view->defaultDuration = $this->_getParam("speed", 5000);
        $this->view->slideWidth = $this->_getParam("width", null);
        $this->view->slideHeight = $this->_getParam("height", 583);
        $this->getElement()->removeDecorator('Title');
        $this->view->showLogo = $this->_getParam('showLogo');
        $this->view->logo = $this->_getParam('logo');
        $this->view->isSitemenuExist = $isSitemenuExist = Engine_Api::_()->hasModuleBootstrap('sitemenu');
        $tempSitemenuLtype = $tempHostType = 8756;
        $this->view->spectacularSignupLoginLink = $this->_getParam("spectacularSignupLoginLink", 1);
        $this->view->spectacularBrowseMenus = $this->_getParam("spectacularBrowseMenus", 1);
        $this->view->spectacularFirstImprotantLink = $this->_getParam("spectacularFirstImprotantLink", 1);
        $this->view->spectacularFirstTitle = $this->_getParam("spectacularFirstTitle", 'Important Title & Link');
        $this->view->coreSettings = $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $spectacularGlobalType = $coreSettings->getSetting('spectacular.global.type', 0);
        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $spectacularManageType = $coreSettings->getSetting('spectacular.manage.type', 1);
        $this->view->spectacularFirstUrl = $this->_getParam("spectacularFirstUrl", '#');
        $this->view->spectacularHtmlTitle = $this->_getParam("spectacularHtmlTitle", 'BRING PEOPLE TOGETHER');
        $this->view->getImageSrcPoint = false;
        $this->view->spectacularHtmlDescription = $this->_getParam("spectacularHtmlDescription", 'Create an event. Sell tickets online.');
        $this->view->spectacularSignupLoginButton = $this->_getParam("spectacularSignupLoginButton", 0);
        $this->view->spectacularSearchBox = $this->_getParam("spectacularSearchBox", Engine_Api::_()->hasModuleBootstrap('siteevent') ? '2' : '1');

        $this->view->showLocationSearch = $this->_getParam('showLocationSearch', 0);
        $this->view->showLocationBasedContent = $this->_getParam('showLocationBasedContent', 0);
        $this->view->showNextLink = $this->_getParam('showNextLink', 1);

        $this->view->showImages = $this->_getParam('showImages', 1);
        $selectedImages = array();
        if (!$this->view->showImages) {
            $selectedImages = $this->_getParam('selectedImages');

            if (empty($selectedImages)) {
                return $this->setNoRender();
            }

            $this->view->list = $getImages = Engine_Api::_()->getItemTable('spectacular_image')->getImages(array('enabled' => 1, 'selectedImages' => $selectedImages), array('file_id'));
        } else {
            $this->view->list = $getImages = Engine_Api::_()->getItemTable('spectacular_image')->getImages(array('enabled' => 1), array('file_id'));
        }
        $order = $this->_getParam("order", 2);
        if (!COUNT($getImages)) {
            $this->view->list = array("1.jpg", "2.jpg", "3.jpg");
        } else {
            $getImagesArray = $getImages->toArray();
            if (!empty($order) && $order == 1) {
                $getImagesArray = @array_reverse($getImagesArray);
            } else if (!empty($order) && $order == 2) {
                @shuffle($getImagesArray);
            }
            $this->view->list = $getImagesArray;
        }

        $spectacular_landing_page_images = Zend_Registry::isRegistered('spectacular_landing_page_images') ? Zend_Registry::get('spectacular_landing_page_images') : null;
        $spectacularInfoType = $coreSettings->getSetting('spectacular.info.type', 1);
        $spectacularLtype = $coreSettings->getSetting('spectacular.lsettings', 0);

        if (!count($this->view->list)) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $page_id = Engine_Api::_()->spectacular()->getWidgetizedPageId(array('name' => 'core_index_index'));
            $db->query("UPDATE `engine4_core_pages` SET  `layout` =  '' WHERE  `engine4_core_pages`.`page_id` = $page_id LIMIT 1 ;");
            return $this->setNoRender();
        }

        if (empty($spectacularGlobalType)) {
            for ($check = 0; $check < strlen($hostType); $check++) {
                $tempHostType += @ord($hostType[$check]);
            }

            for ($check = 0; $check < strlen($spectacularLtype); $check++) {
                $tempSitemenuLtype += @ord($spectacularLtype[$check]);
            }
        }

        $this->view->max = $this->_getParam('max', 20);
        $this->view->spectacularHowItWorks = $this->_getParam('spectacularHowItWorks', 1);

        $islanguage = $this->view->translate()->getLocale();

        if (!strstr($islanguage, '_')) {
            $islanguage = $islanguage . '_default';
        }

        $keyForSettings = str_replace('_', '.', $islanguage);
        $spectacularLendingBlockValue = $coreSettings->getSetting('spectacular.lending.block.languages.' . $keyForSettings, null);

        $spectacularLendingBlockTitleValue = $coreSettings->getSetting('spectacular.lending.block.title.languages.' . $keyForSettings, null);
        if (empty($spectacularLendingBlockValue)) {
            $spectacularLendingBlockValue = $coreSettings->getSetting('spectacular.lending.block', null);
        }
        if (empty($spectacularLendingBlockTitleValue)) {
            $spectacularLendingBlockTitleValue = $coreSettings->getSetting('spectacular.lending.block.title', null);
        }

        if ((empty($spectacularGlobalType)) && (($spectacularManageType != $tempHostType) || ($spectacularInfoType != $tempSitemenuLtype))) {
            $this->view->getImageSrcPoint = true;
            return $this->setNoRender();
        }

        if (!empty($spectacularLendingBlockValue))
            $this->view->spectacularLendingBlockValue = @base64_decode($spectacularLendingBlockValue);
        if (!empty($spectacularLendingBlockTitleValue))
            $this->view->spectacularLendingBlockTitleValue = @base64_decode($spectacularLendingBlockTitleValue);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->removePadding = false;

        //GET CONTENT ID
        $content_id = $this->view->identity;
        $content_page_id = Engine_Api::_()->spectacular()->getContentPageId(array('content_id' => $content_id));
        $layoutValue = Engine_Api::_()->spectacular()->getWidgetizedPageLayoutValue(array('page_id' => $content_page_id));
        if ($layoutValue == 'default-simple') {
            Zend_Layout::startMvc()->setViewBasePath(APPLICATION_PATH . "/application/modules/Spectacular/layouts", 'Core_Layout_View');
            $this->view->removePadding = true;
        }
        $this->view->isPost = Zend_Controller_Front::getInstance()->getRequest()->isPost();

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

        if (empty($spectacular_landing_page_images)) {
            return $this->setNoRender();
        }
    }

}
