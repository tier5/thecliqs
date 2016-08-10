<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-12-21 18:51 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
// 02.03.2013 - TrioxX

class Weather_Installer extends Engine_Package_Installer_Module
{
  function onPreInstall()
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

		// 02.03.2013 - TrioxX
		$db = Engine_Db_Table::getDefaultAdapter(); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_weather_locations` ( 
		  `location_id` int(11) NOT NULL AUTO_INCREMENT, 
		  `object_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL, 
		  `object_id` int(11) NOT NULL, 
		  `user_id` int(11) NOT NULL, 
		  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL, 
		  PRIMARY KEY (`location_id`), 
		  KEY `user_id` (`user_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"); 

		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
		  ('core_admin_main_plugins_weather', 'weather', 'HE - Weather', '', '{\"route\":\"admin_default\",\"module\":\"weather\",\"controller\":\"settings\"}', 'core_admin_main_plugins', '', 888),
		  ('weather_admin_main_settings', 'weather', 'Global Settings', '', '{\"route\":\"admin_default\",\"module\":\"weather\",\"controller\":\"settings\"}', 'weather_admin_main', '', 1),
		  ('weather_admin_main_faq', 'weather', 'FAQ', '', '{\"route\":\"admin_default\",\"module\":\"weather\",\"controller\":\"faq\"}', 'weather_admin_main', '', 2);");
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
	  
    }
    else { //$operation = upgrade|refresh
		$db = Engine_Db_Table::getDefaultAdapter();
		
		$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }

  function onInstall()
  {
    parent::onInstall();

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'page')
      ->limit(1);

    $core_module = $select->query()->fetchObject();

    if ($core_module && $core_module->name) {
      $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`) "
        . "VALUES ('weather', 'weather.weather', 21, '{\"title\":\"Weather\", \"titleCount\":false}');");
    }
  }
}