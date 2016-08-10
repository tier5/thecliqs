<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Plugin_Menus
{
    // core_mini

    public function onMenuInitialize_UserSettingsTimeline($row)
    {
        /**
         * @var $settings Core_Api_Settings
         */
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($settings->__get('timeline.usage') == 'force') {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_UserEditTimeline($row)
    {
        /**
         * @var $settings Core_Api_Settings
         */
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($settings->__get('timeline.usage') == 'force') {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_CoreMainTimeline($row)
    {

    }
}