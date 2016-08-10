<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2015-05-15 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Form_Admin_Widget_Search extends Engine_Form {

    public function init() {

        if (Engine_Api::_()->hasModuleBootstrap('sitecitycontent') && Engine_Api::_()->hasModuleBootstrap('siteadvsearch')) {
            if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        2 => 'Advanced Events Search',
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 2,
                ));
            } else {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 1,
                ));
            }

            $this->addElement('Radio', 'showLocationBasedContent', array(
                'label' => 'Show results based on the location, saved in user’s browser cookie.',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0,
            ));

            $this->addElement('Radio', 'showLocationSearch', array(
                'label' => 'Do you want to enable location based searching?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => 0,
            ));
        } else {

            if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        2 => 'Advanced Events Search',
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 2,
                ));
            } else {
                $this->addElement('Radio', 'spectacularSearchBox', array(
                    'label' => 'Select the Search Box that you want to display in this widget.',
                    'multiOptions' => array(
                        1 => 'Advanced Search [Dependent on Advanced Search Plugin] / Global Search',
                        0 => 'None'
                    ),
                    'value' => 1,
                ));
            }
        }
    }

}
