<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Welcome_Installer extends Engine_Package_Installer_Module {

    public function onPreInstall() {
        parent::onPreInstall();

        $db = $this->getDb();
        $translate = Zend_Registry::get('Zend_Translate');

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'hecore')
                ->where('enabled = ?', 1);

        $hecore = $db->fetchRow($select);

        if (!$hecore) {
            $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
            return $this->_error($error_message);
        }

        if (version_compare($hecore['version'], '4.2.0p1') < 0) {
            $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
            return $this->_error($error_message);
        }

        $operation = $this->_databaseOperationType;
        $module_name = $this->getOperation()->getTargetPackage()->getName();
        $package = $this->_operation->getPrimaryPackage();

        // Keygen by TrioxX
        // This one does NOT generate valid keys
        // It's just to make the key look legit ;)
        $licenseKey = strtoupper(substr(md5(md5($package->getName()) . md5($_SERVER['HTTP_HOST'])), 0, 16));

        $select = $db->select()
                ->from('engine4_hecore_modules')
                ->where('name = ?', $module_name);

        $module = $db->fetchRow($select);

        if ($module && isset($module['installed']) && $module['installed'] && isset($module['version']) && $module['version'] == $this->_targetVersion && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
        ) {
            return;
        }

        if ($operation == 'install') {

            if ($module && $module['installed']) {
                return;
            }

            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_welcome_slideshows` ( 
		  `slideshow_id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
		  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
		  `effect` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `width` int(10) unsigned NOT NULL DEFAULT '0', 
		  `height` int(10) unsigned NOT NULL DEFAULT '0', 
		  PRIMARY KEY (`slideshow_id`), 
		  KEY `title` (`title`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_welcome_steps` ( 
		  `step_id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
		  `slideshow_id` int(10) unsigned NOT NULL DEFAULT '1', 
		  `photo_id` int(11) NOT NULL, 
		  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL, 
		  `body` text COLLATE utf8_unicode_ci NOT NULL, 
		  `link` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `creation_date` datetime NOT NULL, 
		  PRIMARY KEY (`step_id`) 
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_welcome_effects` ( 
		  `effect_id` INTEGER(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		  `value` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL, 
		  `label` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL, 
		  PRIMARY KEY (`effect_id`) 
		  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci");

            $db->query("INSERT IGNORE INTO `engine4_welcome_effects` (`effect_id`, `value`, `label`) VALUES 
		  (1, 'tabs', 'Tabs'), 
		  (2, 'slider', 'Slider'), 
		  (3, 'popup', 'Accordeon'), 
		  (4, 'curtain', 'Curtain'), 
		  (5, 'carousel', 'Carousel'), 
		  (6, 'kenburns', 'KenBurns')");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_welcome_settings` ( 
		  `setting_id` INTEGER(11) NOT NULL AUTO_INCREMENT, 
		  `name` VARCHAR(100) COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `value` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `effect` VARCHAR(100) COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `type` VARCHAR(100) COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `options` TEXT COLLATE utf8_unicode_ci, 
		  `label` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `description` TEXT COLLATE utf8_unicode_ci, 
		  PRIMARY KEY (`setting_id`) 
		  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci");

            $db->query("INSERT IGNORE INTO `engine4_welcome_settings` (`setting_id`, `name`, `value`, `effect`, `type`, `options`, `label`, `description`) VALUES 
		(1, 'effect', 'wave', 'curtain', 'select', 'a:3:{s:4:\"wave\";s:4:\"Wave\";s:6:\"zipper\";s:6:\"Zipper\";s:7:\"curtain\";s:7:\"Curtain\";}', 'Type', ''), 
		(2, 'strips', '20', 'curtain', 'text', '', 'Strips Count', ''), 
		(3, 'titleOpacity', '0.6', 'curtain', 'text', '', 'Title Opacity(0.0-1.0)', ''), 
		(4, 'position', 'curtain', 'curtain', 'select', 'a:4:{s:9:\"alternate\";s:9:\"Alternate\";s:3:\"top\";s:3:\"Top\";s:6:\"bottom\";s:6:\"Bottom\";s:7:\"curtain\";s:7:\"Curtain\";}', 'Position', ''), 
		(5, 'direction', 'fountainAlternate', 'curtain', 'select', 'a:6:{s:17:\"fountainAlternate\";s:18:\"Fountain Alternate\";s:4:\"left\";s:4:\"Left\";s:5:\"right\";s:5:\"Right\";s:9:\"alternate\";s:9:\"Alternate\";s:6:\"random\";s:6:\"Random\";s:8:\"fountain\";s:8:\"Fountain\";}', 'Direction', ''),
		(6, 'defaultIndex', '1', 'popup', 'text', '', 'Default Index', ''), 
		(7, 'expandMode', 'mouseover', 'popup', 'select', 'a:3:{s:9:\"mouseover\";s:13:\"On mouse over\";s:5:\"click\";s:14:\"On mouse click\";s:5:\"false\";s:13:\"Do not expand\";}', 'Expand Mode', ''), 
		(8, 'pinMode', 'click', 'popup', 'select', 'a:3:{s:9:\"mouseover\";s:13:\"On mouse over\";s:5:\"click\";s:14:\"On mouse click\";s:5:\"false\";s:18:\"Do not stay opened\";}', 'Pin Mode', ''), 
		(9, 'pause', '3000', 'carousel', 'text', '', 'Pause(ms)', ''), 
		(10, 'speed', '1000', 'carousel', 'text', '', 'Speed(ms)', ''), 
		(11, 'delay', '5000', 'curtain', 'text', '', 'Delay(ms)', ''), 
		(12, 'interval', '5000', 'tabs', 'text', NULL, 'Pause(ms)', NULL), 
		(13, 'duration', '2000', 'kenburns', 'text', NULL, 'Duration(ms)', NULL)");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_welcome_slideshowsettings`( 
		  `slideshowsetting_id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
		  `slideshow_id` int(11) unsigned NOT NULL, 
		  `setting_id` int(11) unsigned NOT NULL, 
		  `value` varchar(255) NOT NULL, 
		  PRIMARY KEY (`slideshowsetting_id`), 
		  KEY `slideshow_id` (`slideshow_id`,`setting_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
		('core_admin_main_plugins_welcome', 'welcome', 'HE - Welcome', '', '{\"route\":\"admin_default\",\"module\":\"welcome\",\"controller\":\"slideshow\"}', 'core_admin_main_plugins', '', 888)");

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } else { //$operation = upgrade|refresh
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}