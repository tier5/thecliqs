<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Installer extends Engine_Package_Installer_Module
{
  public function onPreInstall()
  {
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

    if ($module && isset($module['installed']) && $module['installed']
      && isset($module['version']) && $module['version'] == $this->_targetVersion
      && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
    ) {
      return;
    }

    if ($operation == 'install') {

      if ($module && $module['installed']) {
        return;
      }

		$db = Engine_Db_Table::getDefaultAdapter();

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_checkin_checks` (
		  `check_id` int(10) NOT NULL AUTO_INCREMENT,
		  `action_id` int(11) DEFAULT NULL,
		  `user_id` int(11) DEFAULT NULL,
		  `place_id` int(10) DEFAULT '0',
		  `creation_date` datetime DEFAULT NULL,
		  PRIMARY KEY (`check_id`),
		  KEY `action_id` (`action_id`),
		  KEY `user_id` (`user_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("
		CREATE TABLE IF NOT EXISTS `engine4_checkin_places` (
		  `place_id` INT(10) NOT NULL AUTO_INCREMENT,
		  `google_id` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
		  `object_type` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
		  `object_id` INT(10) NULL DEFAULT '0',
		  `name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
		  `vicinity` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
		  `types` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
		  `icon` TEXT NULL COLLATE 'utf8_unicode_ci',
		  `latitude` FLOAT(10,6) NULL DEFAULT NULL,
		  `longitude` FLOAT(10,6) NULL DEFAULT NULL,
		  `creation_date` DATETIME NULL DEFAULT NULL,
		  PRIMARY KEY (`place_id`),
		  INDEX `object_id` (`object_id`)
		)
		ENGINE=MyISAM;
		");


		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
		('core_admin_main_plugins_checkin', 'checkin', 'HE - Checkin', '', '{\"route\":\"admin_default\",\"module\":\"checkin\",\"controller\":\"index\"}', 'core_admin_main_plugins', '', 888);");
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
    }
    else { //$operation = upgrade|refresh
		$db = Engine_Db_Table::getDefaultAdapter();
		
		$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }
}