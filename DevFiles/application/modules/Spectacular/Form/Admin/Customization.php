<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Customization.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Form_Admin_Customization extends Engine_Form {

    public function init() {

        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Theme Customization")));

        $this->addElement('Radio', 'theme_customization', array(
            'label' => 'Select Theme Color',
            'multiOptions' => array(
                0 => 'DEFAULT',
                1 => 'BLUE',
                2 => 'GREEN',
                4 => 'DARK SKY BLUE',
                5 => 'VOILET',
                6 => 'ORANGE',
                7 => 'DARK VOILET',
                8 => 'YELLOW',
                9 => 'DARK BLUE',
                10 => 'DARK PINK',
                3 => 'Custom Colors (Choosing this option will enable you to customize your theme according to your site.)'
            ),
            'onchange' => 'changeThemeCustomization();',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('theme.customization', 0),
        ));


        $this->addElement('Text', 'spectacular_theme_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_themeColor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'spectacular_theme_button_border_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_themeButtonBorderColor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'spectacular_landingpage_signinbtn', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_themeLandingPageSigninButtonColor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'spectacular_landingpage_signupbtn', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_themeLandingPageSignupButtonColor.tpl',
                        'class' => 'form element'
                    )))
        ));
        $this->addElement('Text', 'spectacular_theme_background_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_themeBackgroundColor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'spectacular_theme_containers_background_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_themeContainersBackgroundColor.tpl',
                        'class' => 'form element'
                    )))
        ));

        $this->addElement('Text', 'spectacular_navigation_background_color', array(
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_navigationBackgroundColor.tpl',
                        'class' => 'form element'
                    )))
        ));



        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }

}
