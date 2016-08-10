<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Form_Admin_Settings extends Engine_Form {

    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode", "spectacular_landing_page_layout"
    );

    public function init() {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->setTitle(sprintf(Zend_Registry::get('Zend_Translate')->_("Global Settings")))
                ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("These settings affect all members in your community.")));

        //$this->loadDefaultDecorators();
        // ELEMENT FOR LICENSE KEY
        $this->addElement('Text', 'spectacular_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => $coreSettings->getSetting('spectacular.lsettings'),
        ));

        $this->addElement('Radio', 'spectacular_landing_page_layout', array(
            'label' => 'Change Landing Page Layout',
            'description' => "Do you want the layout of your home page to be changed as per the default set-up of this theme ? If you choose 'Yes' then your current layout of Home page will be replaced with a new one.<br />
[<strong>Recommendation</strong>: You need to place widgets related to content from ‘Layout Editor’, to beautifying your demo like us.]",
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1,
        ));
        $this->spectacular_landing_page_layout->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        if (APPLICATION_ENV == 'production') {
            $this->addElement('Checkbox', 'environment_mode', array(
                'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few stores of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
                'description' => 'System Mode',
                'value' => 1,
            ));
        } else {
            $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $baseURL = $view->baseUrl();
        $spectacularLendingBlockValue = $coreSettings->getSetting('spectacular.lending.block', null);
        if (empty($spectacularLendingBlockValue) || is_array($spectacularLendingBlockValue)) {
            $spectacularLendingBlockValue = '<div style="width:1200px;margin:0 auto;display:table;">
            <span style="font-size:48px;color:#292929;float:left;width:100%;text-align:center;margin:80px 0 0 0;position:absolute;top:0;left:0;right:0;clear:both;">How It Works !</span>
                <div style="transition: opacity 0.8s ease, top 800ms ease;float: left; margin: 150px 0; opacity: 1; padding: 56px 0; text-align: center; width: 33.3%;">
                    <a href="events/">
                        <div style="background-position: center 50%; background-repeat: no-repeat; margin: 0 auto; width: 200px; height: 200px; background-image: url(' . $baseURL . '/application/modules/Spectacular/externals/images/create-event.png);"></div>
                        <span style="color: #fff; float: left; font-family: sans-serif; font-size: 27px; margin-top: 20px; text-align: center; width: 100%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Create Events</span>
                        <span style="color: #fff; display:inline-block; font-family: sans-serif; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Create your event, define the tickets and ready for action!</span>
                    </a>
                </div>

                <div style="transition: opacity 0.8s ease, top 800ms ease;float: left; margin: 150px 0; opacity: 1; padding: 56px 0; text-align: center; width: 33.3%;">
                    <a href="events/">
                        <div style="background-position: center 50%; background-repeat: no-repeat; margin: 0 auto; width: 200px; height: 200px; background-image: url(' . $baseURL . '/application/modules/Spectacular/externals/images/sell-tickets.png);"></div>
                        <span style="color: #fff; float: left; font-family: sans-serif; font-size: 27px; margin-top: 20px; text-align: center; width: 100%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Sell Tickets</span>
                        <span style="color: #fff; display:inline-block; font-family: sans-serif; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Sell your event tickets Online.</span>
                    </a>
                </div>

                <div style="transition: opacity 0.8s ease, top 800ms ease;float: left; margin: 150px 0; opacity: 1; padding: 56px 0; text-align: center; width: 33.3%;">
                    <a href="events/">
                        <div style="background-position: center 50%; background-repeat: no-repeat; margin: 0 auto; width: 200px; height: 200px; background-image: url(' . $baseURL . '/application/modules/Spectacular/externals/images/invitepeople.png);"></div>
                        <span style="color: #fff; float: left; font-family: sans-serif; font-size: 27px; margin-top: 20px; text-align: center; width: 100%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Share Events</span>
                        <span style="color: #fff; display:inline-block; font-family: sans-serif; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Promote and Invite people to your events.</span>
                    </a>
                </div>
                <a href="#" style="text-indent:100px;height:20px;width:20px;position:absolute;top:12px;background-image:url(' . $baseURL . '/application/modules/Spectacular/externals/images/close-icon.png);">Close</a>
            </div>';
        } else {
            $spectacularLendingBlockValue = @base64_decode($spectacularLendingBlockValue);
        }

        //WORK FOR MULTILANGUAGES START
        $localeMultiOptions = Engine_Api::_()->spectacular()->getLanguageArray();
        
        $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
        $total_allowed_languages = Count($localeMultiOptions);
        if (!empty($localeMultiOptions)) {
            foreach ($localeMultiOptions as $key => $label) {
                $lang_name = $label;
                if (isset($localeMultiOptions[$label])) {
                    $lang_name = $localeMultiOptions[$label];
                }

                $page_block_field = "spectacular_lending_page_block_$key";
                $page_block_title_field = "spectacular_lending_page_block_title_$key";

                if (!strstr($key, '_')) {
                    $key = $key . '_default';
                }

                $keyForSettings = str_replace('_', '.', $key);
                $spectacularLendingBlockValueMulti = $coreSettings->getSetting('spectacular.lending.block.languages.' . $keyForSettings, null);
                if (empty($spectacularLendingBlockValueMulti)) {
                    $spectacularLendingBlockValueMulti = $spectacularLendingBlockValue;
                } else {
                    $spectacularLendingBlockValueMulti = @base64_decode($spectacularLendingBlockValueMulti);
                }

                $spectacularLendingBlockTitleValueMulti = $coreSettings->getSetting('spectacular.lending.block.title.languages.' . $keyForSettings, 'Get Started');
                if (empty($spectacularLendingBlockTitleValueMulti)) {
                    $spectacularLendingBlockTitleValueMulti = 'Get Started';
                } else {
                    $spectacularLendingBlockTitleValueMulti = @base64_decode($spectacularLendingBlockTitleValueMulti);
                }

                $page_block_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Action Button's Slide Down Content in %s"), $lang_name);
                $page_block_title_label = sprintf(Zend_Registry::get('Zend_Translate')->_("Get Started Action Button's Slide Down Content in %s"), $lang_name);

                if ($total_allowed_languages <= 1) {
                    $page_block_field = "spectacular_lending_page_block";
                    $page_block_title_field = "spectacular_lending_page_block_title";
                    $page_block_label = "Action Button's Slide Down Content";
                    $page_block_title_label = "Action Button Title";
                } elseif ($label == 'en' && $total_allowed_languages > 1) {
                    $page_block_field = "spectacular_lending_page_block";
                    $page_block_title_field = "spectacular_lending_page_block_title";
                }

                $this->addElement('Text', $page_block_title_field, array(
                    'label' => $page_block_title_label,
                    'description' => "Choose a title for the action button, like 'Get Started', etc, that is currently getting displayed on the image rotator on the landing page. Clicking on this will attractively slide down related content that you can configure below. [This can be used to show useful information regarding your website like 'How it Works', 'Get Going', 'Tours', 'Contact Us', etc.] (Note: For this button to be displayed, it must be enabled in the settings of the widget: 'Responsive Spectacular Theme - Landing Page Images'.)",
                    'value' => $spectacularLendingBlockTitleValueMulti,
                    'filters' => array(
                        new Engine_Filter_Html(),
                        new Engine_Filter_Censor()),
                ));

                $editorOptions = Engine_Api::_()->seaocore()->tinymceEditorOptions();
                $editorOptions['height'] = '500px';

                $this->addElement('TinyMce', $page_block_field, array(
                    'label' => $page_block_label,
                    'description' => "Configure the content that gets shown in an attractive slide-down manner, when someone clicks on the Action Button. In this content, you can include important links of your website, or a quick overview of your website to enable users to get started.",
                    'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px; height:240px;'),
                    'value' => $spectacularLendingBlockValueMulti,
                    'filters' => array(
                        new Engine_Filter_Html(),
                        new Engine_Filter_Censor()),
                    'editorOptions' => $editorOptions,
                ));
            }
        }
        //WORK FOR MULTILANGUAGES END
        //Add submit button
        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
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
