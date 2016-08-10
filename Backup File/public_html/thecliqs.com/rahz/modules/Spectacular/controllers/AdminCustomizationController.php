<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminCustomizationController.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_AdminCustomizationController extends Core_Controller_Action_Admin {

    private function _getCustomCSS($values) {
        $returnTheme = null;
        if (!empty($values) && isset($values['theme_customization'])) {
            switch ($values['theme_customization']) {
                case 0: // DEFAULT THEME
                    $returnTheme .= 'theme_color: #ff5a5f; button_border_color:#ff2f34; landingpage_signinbtn:#ff5a5f; landingpage_signupbtn: rgba(255,95,63,.5); theme_background_color:#f7f7f7;navigation_background_color: #565a5c;';
                    break;

                case 1: // BLUE COLOR BASED THEME
                    $returnTheme .= 'theme_color: #005da4; button_border_color:#004b85; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5); theme_background_color:#eeeeee;navigation_background_color: #121212;';
                    break;

                case 2: // GREEN COLOR BASED THEME
                    $returnTheme .= 'theme_color: #038c7e; button_border_color:#007165; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5); theme_background_color:#cccccc;navigation_background_color: #000000;';
                    break;

                case 4: // DARK SKY BLUE COLOR BASED THEME
                    $returnTheme .= 'theme_color: #3eaacd; button_border_color:#047da5; landingpage_signinbtn:#BF548F; landingpage_signupbtn: rgba(191, 84, 143, .5); theme_background_color:#9b9b9b;navigation_background_color: #565a5c;';
                    break;

                case 5: // VOILET COLOR BASED THEME
                    $returnTheme .= 'theme_color: #9351a6; button_border_color:#7c3490; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5); theme_background_color:#dfdfdf;navigation_background_color: #222222;';
                    break;

                case 6: // ORANGE COLOR BASED THEME
                    $returnTheme .= 'theme_color: #bc5300; button_border_color:#a24a05; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5); theme_background_color:#bbbbbb;navigation_background_color: #bc5300;';
                    break;

                case 7: // DARK VOILET  COLOR BASED THEME
                    $returnTheme .= 'theme_color: #35245a; button_border_color:#2b1a50; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5); theme_background_color:#eeeeee;navigation_background_color: #000000;';
                    break;

                case 8: // YELLOW COLOR BASED THEME
                    $returnTheme .= 'theme_color: #b0bf0a; button_border_color:#909d00; landingpage_signinbtn:#3FC8F4; landingpage_signupbtn: rgba(63, 200, 244, .5); theme_background_color:#fff;navigation_background_color: #111111;';
                    break;

                case 9: // DARK BLUE COLOR BASED THEME
                    $returnTheme .= 'theme_color: #10477f; button_border_color:#003169; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5); theme_background_color:#e9e9e9;navigation_background_color: #1b1b1b;';
                    break;

                case 10: // DARK PINK COLOR BASED THEME
                    $returnTheme .= 'theme_color: #e63c61; button_border_color:#d90f3a; landingpage_signinbtn:#FF5F3F; landingpage_signupbtn: rgba(255, 95, 63, .5); theme_background_color:#efefef;navigation_background_color: #000018;';
                    break;

                case 3: // CUSTOM COLOR BASED THEME
                    $returnTheme .= 'theme_color: ' . $values['spectacular_theme_color'] . '; button_border_color:' . $values['spectacular_theme_button_border_color'] . '; landingpage_signinbtn: ' . $values['spectacular_landingpage_signinbtn'] . '; landingpage_signupbtn: ' . $values['spectacular_landingpage_signupbtn'] . '; theme_background_color: ' . $values['spectacular_theme_background_color'] . '; theme_containers_background_color: ' . $values['spectacular_theme_containers_background_color'] . '; navigation_background_color: ' . $values['spectacular_navigation_background_color']. ';';
                    break;
            }
        }
        return $returnTheme;
    }

    public function indexAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spectacular_admin_main', array(), 'spectacular_admin_theme_customization');

        $this->view->form = $form = new Spectacular_Form_Admin_Customization();

        if (!$this->getRequest()->isPost())
            return;

        if (!$form->isValid($this->getRequest()->getPost()))
            return;

        $values = $form->getValues();

        include_once APPLICATION_PATH . '/application/modules/Spectacular/controllers/license/license2.php';
    }

}
