<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: install.php 18.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvmessages_Installer extends Engine_Package_Installer_Module
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
		
	// Keygen by MisterWizard
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

		$db->query("INSERT INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
                  (1, 'headvmessages', 'use', 1, NULL),
                  (2, 'headvmessages', 'use', 1, NULL),
                  (3, 'headvmessages', 'use', 1, NULL),
                  (4, 'headvmessages', 'use', 1, NULL),
                  (5, 'headvmessages', 'use', 0, NULL);");

		$db->query("INSERT INTO `engine4_core_settings` (`name`, `value`) VALUES
                  ('headvmessages.enabled', '1'),
                  ('headvmessages.enter.send.enabled', '1');");

		$db->query("INSERT INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('', 'core_admin_main_plugins_headvmessages', 'headvmessages', 'HE - Advanced Messages', NULL, '{"route":"admin_default","module":"headvmessages","controller":"index"}', 'core_admin_main_plugins', NULL, 1, 0, 777),
('', 'headvmessages_admin_main_index', 'headvmessages', 'Global Settings', NULL, '{"route":"admin_default","module":"headvmessages","controller":"index","action":"index"}', 'headvmessages_admin_main', NULL, 1, 0, 1),
('', 'headvmessages_admin_main_levels', 'headvmessages', 'Levels Settings', NULL, '{"route":"admin_default","module":"headvmessages","controller":"index","action":"levels"}', 'headvmessages_admin_main', NULL, 1, 0, 2);");
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
    }
    else { //$operation = upgrade|refresh
		$db = Engine_Db_Table::getDefaultAdapter();
		
		$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }
}
