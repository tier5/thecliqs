<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2012-04-06 15:11:13 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
// 02.03.2013 - TrioxX

class Hetips_Installer extends Engine_Package_Installer_Module
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

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_hetips_maps` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `tip_id` int(10) unsigned DEFAULT '0',
		  `tip_type` varchar(100) NOT NULL,
		  `option_id` int(10) unsigned DEFAULT '0',
		  `order` int(10) unsigned DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=0");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_hetips_types` (
		  `type_id` int(10) unsigned NOT NULL ,
		  `type` varchar(50) DEFAULT NULL,
		  `label` varchar(150) DEFAULT NULL,
		  PRIMARY KEY (`type_id`)
		) ENGINE=InnoDB");

		$db->query("INSERT IGNORE INTO `engine4_hetips_types` (`type_id`, `type`, `label`) VALUES
		  (1, 'user', 'USER_PROFILE_TIPS'),
		  (2, 'page', 'PAGE_PROFILE_TIPS'),
		  (3, 'group', 'GROUP_PROFILE_TIPS')");
		  
		$db->query("CREATE TABLE IF NOT EXISTS `engine4_hetips_meta` (
		  `tip_id` int(5) unsigned NOT NULL ,
		  `tip_type` varchar(50) DEFAULT NULL,
		  `type` varchar(50) DEFAULT NULL,
		  `label` varchar(150) DEFAULT NULL,
		  `order` int(10) unsigned DEFAULT '999',
		  PRIMARY KEY (`tip_id`)
		) ENGINE=InnoDB");

		$db->query("INSERT IGNORE INTO `engine4_hetips_meta` (`tip_id`, `tip_type`, `type`, `label`, `order`) VALUES
		  (1, 'page', 'url', 'Url', 999),
		  (2, 'page', 'displayname', 'Display Name', 999),
		  (3, 'page', 'title', 'Title', 999),
		  (4, 'page', 'country', 'Country', 999),
		  (5, 'page', 'street', 'Street', 999),
		  (6, 'page', 'phone', 'Phone', 999),
		  (7, 'page', 'website', 'Web Site', 999),
		  (8, 'group', 'title', 'Title', 999),
		 (9, 'group', 'member_count', 'Member count', 999)");
		 
		$db->query("CREATE TABLE IF NOT EXISTS `engine4_hetips_settings` (
		  `type_id` int(5) DEFAULT NULL,
		  `name` varchar(100) NOT NULL DEFAULT '',
		  `value` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`name`)
		) ENGINE=InnoDB");

		$db->query("INSERT IGNORE INTO `engine4_hetips_settings` (`type_id`, `name`, `value`) VALUES
		(1, 'user_display_friends', '1'),
		(1, 'user_how_display', 'hetips_own_row'),
		(1, 'user_likes_count', '1'),
		(1, 'user_show_labels', '1'),
		(2, 'page_how_display', 'hetips_own_row'),
		(2, 'page_likes_count', '1'),
		(2, 'page_members_like', '1'),
		(2, 'page_show_labels', '1'),
		(3, 'group_display_friends', '1'),
		(3, 'group_how_display', 'hetips_own_row'),
		(3, 'group_likes_count', '1'),
		(3, 'group_members_count', '1'),
		(3, 'group_show_labels', '1')");

		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `params`, `menu`, `order`) VALUES 
		('hetips_admin_main_settings', 'hetips', 'HETIPS_Hetips', '{\"route\":\"admin_default\",\"module\":\"hetips\"}', 'core_admin_main_plugins', 888)");
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
    }
    else { //$operation = upgrade|refresh
		$db = Engine_Db_Table::getDefaultAdapter();
		
		$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }
}