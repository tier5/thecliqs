<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
// 02.03.2013 - TrioxX

class Like_Installer extends Engine_Package_Installer_Module
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
    $module_name = 'likes';
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

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_like_likes` ( 
			`like_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			`resource_type` VARCHAR(32) NOT NULL, 
			`resource_title` VARCHAR(128) NOT NULL, 
			`poster_type` VARCHAR(32) NOT NULL, 
			`poster_id` INT(11) UNSIGNED NOT NULL, 
			PRIMARY KEY (`like_id`), 
			INDEX `poster_type` (`poster_type`, `poster_id`) 
		)ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';"); 

		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
		('user_edit_interests', 'like', 'like_Profile Interests', 'Like_Plugin_Menus', '{\"route\":\"like_interests\",\"action\":\"index\"}', 'user_edit', '', 4), 

		('store_product_profile_promote', 'like', 'LIKE_PromoteProduct', 'Like_Plugin_Menus', '', 'store_product_profile', '', 2), 

		('core_admin_main_plugins_like', 'like', 'HE - Like', '', '{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"settings\"}', 'core_admin_main_plugins', '', 888),

		('like_admin_main_level', 'like', 'Level Settings', '', '{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"level\"}', 'like_admin_main', '', 2), 
		('like_admin_main_settings', 'like', 'Settings', '', '{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"settings\"}', 'like_admin_main', '', 3), 
		('like_admin_main_faq', 'like', 'FAQ', '', '{\"route\":\"admin_default\",\"module\":\"like\",\"controller\":\"faq\"}', 'like_admin_main', '', 4);"); 

		$db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES 
		('like_suggest', 'like', '{item:\$subject} suggest you to check this out {item:\$object}.', 0, ''), 
		('like_send_update', 'like', '{item:\$subject} send you to an update about {item:\$object}.', 0, '');"); 

		$db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
		('like_item', 'like', '{var:\$content}', 1, 6, 0, 1, 1, 0);"); 

		$db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
		('like_item_private', 'like', '{var:\$content}', 1, 1, 0, 1, 1, 0);"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'like_user' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'like_group' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'like_event' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'like_page' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 
		  
		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'like_donation' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");  

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'like_offer' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 
		   
		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'like_product' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'interest' as `name`, 
			1 as `value`, 
			null as `params` 
		  FROM `engine4_authorization_levels`;"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'user' as `type`, 
			'auth_interest' as `name`, 
			5 as `value`, 
			'[\"everyone\", \"registered\", \"owner_network\", \"owner_member_member\", \"owner_member\", \"owner\"]' as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`) 
		  SELECT 
			'user' AS `resource_type`, 
			`user_id` AS `resource_id`, 
			'interest' AS `action`, 
			'everyone' AS `role`, 
			0 AS `role_id`, 
			1 AS `value`, 
			null AS `params` 
		  FROM `engine4_users`;"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`) 
		  SELECT 
			'user' AS `resource_type`, 
			`user_id` AS `resource_id`, 
			'interest' AS `action`, 
			'registered' AS `role`, 
			0 AS `role_id`, 
			1 AS `value`, 
			null AS `params` 
		  FROM `engine4_users`;"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`) 
		  SELECT 
			'user' AS `resource_type`, 
			`user_id` AS `resource_id`, 
			'interest' AS `action`, 
			'owner_member' AS `role`, 
			0 AS `role_id`, 
			1 AS `value`, 
			null AS `params` 
		  FROM `engine4_users`;"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`) 
		  SELECT 
			'user' AS `resource_type`, 
			`user_id` AS `resource_id`, 
			'interest' AS `action`, 
			'owner_network' AS `role`, 
			0 AS `role_id`, 
			1 AS `value`, 
			null AS `params` 
		  FROM `engine4_users`;"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_allow` (`resource_type`, `resource_id`, `action`, `role`, `role_id`, `value`, `params`) 
		  SELECT 
			'user' AS `resource_type`, 
			`user_id` AS `resource_id`, 
			'interest' AS `action`, 
			'owner_member_member' AS `role`, 
			0 AS `role_id`, 
			1 AS `value`, 
			null AS `params` 
		  FROM `engine4_users`;"); 

		$db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES 
		('like_suggest_page', 'like', '[user],[link]'), 
		('like_suggest_user', 'like', '[user],[link]');"); 


		$creation_date = $db->query("SHOW COLUMNS FROM `engine4_core_likes` LIKE 'creation_date';"); 

		if (!$creation_date->rowCount()){ 
		  $db->query("ALTER TABLE `engine4_core_likes` ADD COLUMN `creation_date` DATETIME NOT NULL"); 
		} 

		$store = $db->fetchOne("SELECT TRUE FROM `engine4_hecore_modules` WHERE `name`='store' && `installed`=1 LIMIT 1"); 

		if ( $store ) { 
			$page_id = $db->fetchOne("SELECT `page_id` FROM `engine4_core_pages` WHERE `name` = 'store_index_index'"); 

			$main_content_id = $db->fetchOne("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` = $page_id AND `name` = 'main'"); 
			 
			if ($main_content_id) { 
				$content_id = $db->fetchOne("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` = $page_id AND `name` = 'left' AND `parent_content_id` = $main_content_id"); 
			 
				if ($content_id) { 
					$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
					($page_id, 'widget', 'like.most-liked-products', $content_id, 8, '{\"title\":\"like_Most Liked Products\",\"titleCount\":false}'), 
					($page_id, 'widget', 'like.most-liked-stores', $content_id, 9, '{\"title\":\"like_Most Liked Stores\",\"titleCount\":false}');"); 
				} 
			} 
			 
			$page_id = $db->fetchOne("SELECT `page_id` FROM `engine4_core_pages` WHERE `name` = 'store_index_products'"); 
			 
			$main_content_id = $db->fetchOne("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` = $page_id AND `name` = 'main'"); 
			 
			if ($main_content_id) { 
				$content_id = $db->fetchOne("SELECT `content_id` FROM `engine4_core_content` WHERE `page_id` = $page_id AND `name` = 'right' AND `parent_content_id` = $main_content_id"); 
			 
				if ($content_id) { 
					$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
					($page_id, 'widget', 'like.most-liked-products', $content_id, 8, '{\"title\":\"like_Most Liked Products\",\"titleCount\":false}', NULL);"); 
				} 
			} 
		}
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
    }
    else { //$operation = upgrade|refresh
		$db = Engine_Db_Table::getDefaultAdapter();
		
		$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }
}