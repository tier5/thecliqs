<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitehomepagevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Layout.php 2015-05-15 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Form_Admin_Layout extends Engine_Form {

    protected $_field;

    public function init() {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $this->setTitle('Layout Settings')
                ->setDescription('Below, you can configure the landing page layout for your website.');

        $page_id = Engine_Api::_()->spectacular()->getBackupHomePageId();

        if (!$page_id) {
            $this->loadDefaultDecorators();
            $this->addElement('Radio', 'spectacular_landing_page_layout', array(
                'label' => 'Change Landing Page Layout',
                'description' => "Do you want the layout of your home page to be changed as per the default set-up of this theme ? If you choose 'Yes' then your current layout of Home page will be replaced with a new one.<br />
[<strong>Recommendation</strong>: You need to place widgets related to content from ‘Layout Editor’, to beautifying your demo like us.]",
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('spectacular.landing.page.layout', 0)
            ));
            $this->spectacular_landing_page_layout->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        } else {
            $this->loadDefaultDecorators();
            $this->setTitle('Layout Settings')
                    ->setDescription('Below, you can configure the landing page layout for your website.<br /><div class="tip"><span >It might be that some of your Landing Page settings had disappeared while setting up this plugin. So, we have taken a backup of your landing page. Please <a href="admin/content?page=' . $page_id . '" target="_blank">click here</a> to check your all previous settings of landing page before changing your landing page layout.</span></div>');
            $this->getDecorator('Description')->setOption('escape', false);

            $this->addElement('Radio', 'spectacular_landing_page_layout', array(
                'label' => 'Change Landing Page Layout',
                'description' => "Do you want the layout of your home page to be changed as per the default set-up of this theme ? If you choose 'Yes' then your current layout of Home page will be replaced with a new one.<br />
[<strong>Recommendation</strong>: You need to place widgets related to content from ‘Layout Editor’, to beautifying your demo like us.]",
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => $coreSettings->getSetting('spectacular.landing.page.layout', 0)
            ));
            $this->spectacular_landing_page_layout->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        }

        //WORK FOR MULTILANGUAGES END
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
