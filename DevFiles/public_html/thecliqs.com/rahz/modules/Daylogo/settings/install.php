<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2012-08-16 16:37 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */
 
// 02.03.2013 - TrioxX

class Daylogo_Installer extends Engine_Package_Installer_Module
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
    $module_name = 'daylogo';
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

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_daylogo_logos` ( 
			`logo_id` int(11) NOT NULL auto_increment, 
			`title` VARCHAR(100) NOT NULL, 
			`photo_id` int(11) NOT NULL, 
			`creation_date` datetime NOT NULL, 
			`modified_date` datetime NOT NULL, 
			`start_date` datetime NOT NULL, 
			`end_date` datetime NOT NULL, 
			`enabled` int(1) NOT NULL default '1', 
			`active` int(1) NOT NULL default '0', 
			PRIMARY KEY  (`logo_id`) 
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;"); 

		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `params`, `menu`, `order`) VALUES 
		  ('daylogo_admin_main_settings', 'daylogo', 'Settings', '{\"route\":\"daylogo_admin_index\",\"module\":\"daylogo\",\"controller\":\"settings\"}', 'daylogo_admin_main', 2), 

		  ('core_admin_main_plugins_daylogo', 'daylogo', 'HE - Daylogo', '{\"route\":\"daylogo_admin_index\",\"module\":\"daylogo\",\"controller\":\"index\"}', 'core_admin_main_plugins', 888),

		  ('daylogo_admin_main_index', 'daylogo', 'Manage Logo', '{\"route\":\"daylogo_admin_index\",\"module\":\"daylogo\",\"controller\":\"index\"}', 'daylogo_admin_main', 1) 
		  "); 

		$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
		  ('daylogo.logosperpage', 33), 

		  ('daylogo.maxheight', 150), 

		  ('daylogo.maxwidth', 350) 
		"); 

		$header_page = $db->query("SELECT * FROM `engine4_core_pages` WHERE `name` = 'header' LIMIT 1")->fetch(); 
		if($header_page){ 
			$main_container = $db->query("SELECT * FROM `engine4_core_content` WHERE `page_id` = {$header_page['page_id']} AND `type` = 'container' AND `name` = 'main' LIMIT 1")->fetch();     
			$core_logo = $db->query("SELECT * FROM `engine4_core_content` WHERE `name` = 'core.menu-logo' AND `name` != 'daylogo.day-logo'")->fetch(); 
			if($core_logo){ 
				$params = Zend_Json::decode($core_logo['params']); 
				$params['name'] = 'daylogo.day-logo'; 
				if( !array_key_exists('logo', $params) ) { 
				  $params['logo'] = ''; 
				} 
				if( !array_key_exists('nomobile', $params) ) { 
				  $params['nomobile'] = '0'; 
				} 
				$params['logo_id'] = 0; 
				$params['default'] = $params['logo']; 
				$params = str_replace('"', '\"',Zend_Json::encode($params)); 
				 
				$db->query("UPDATE `engine4_core_content` SET `type` = 'widget', `name` = 'daylogo.day-logo', `order` = 2, `params` = '{$params}' WHERE `content_id` = {$core_logo['content_id']}"); 
			} 
			if($main_container and !$core_logo){ 
				$daylogo = $db->query("SELECT * FROM `engine4_core_content` WHERE `name` = 'daylogo.day-logo'")->fetch(); 
				if(!$daylogo){ 
					$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES({$header_page['page_id']}, 'widget', 'daylogo.day-logo', {$main_container['content_id']}, 2, '{\"title\":\"\",\"name\":\"daylogo.day-logo\",\"logo\":\"\",\"nomobile\":0,\"logo_id\":0,\"default\":\"\"}')"); 
				} 
			} 
		}
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
    } else {
		$db = Engine_Db_Table::getDefaultAdapter();
		
		$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }
}
