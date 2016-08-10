<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Installer extends Engine_Package_Installer_Module
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

    $select = $db->select()
      ->from('engine4_core_modules')
      ->where('name = ?', 'inviter')
      ->where('enabled = ?', 1);

    $inviter = $db->fetchRow($select);

    if($inviter) {
      if (version_compare($inviter['version'], '4.1.8') < 0) {
        $error_message = $translate->_('You should first update your Inviter module.');
        return $this->_error($error_message);
      }
    }

    if (!$this->checkModule('like')) {
      return $this->_error('You should first install Like Module.');
    }

    $operation = $this->_databaseOperationType;
    $module_name = 'pages';
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

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_fields_maps` ( 
		  `field_id` int(11) unsigned NOT NULL, 
		  `option_id` int(11) unsigned NOT NULL, 
		  `child_id` int(11) unsigned NOT NULL, 
		  `order` smallint(6) NOT NULL, 
		  PRIMARY KEY  (`field_id`,`option_id`,`child_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_fields_meta` ( 
		  `field_id` int(11) unsigned NOT NULL auto_increment, 
		  `type` varchar(24) character set latin1 collate latin1_general_ci NOT NULL, 
		  `label` varchar(64) collate utf8_unicode_ci NOT NULL, 
		  `description` varchar(255) collate utf8_unicode_ci NOT NULL default '', 
		  `alias` varchar(32) character set latin1 collate latin1_general_ci NOT NULL default '', 
		  `required` tinyint(1) NOT NULL default '0', 
		  `display` tinyint(1) unsigned NOT NULL, 
		  `publish` tinyint(1) unsigned NOT NULL default '0', 
		  `search` tinyint(1) unsigned NOT NULL default '0', 
		  `order` smallint(3) unsigned NOT NULL default '999', 
		  `show` tinyint(1) unsigned NOT NULL default '1', 
		  `config` text collate utf8_unicode_ci, 
		  `validators` text collate utf8_unicode_ci, 
		  `filters` text collate utf8_unicode_ci, 
		  `style` text collate utf8_unicode_ci, 
		  `error` text collate utf8_unicode_ci, 
		  PRIMARY KEY  (`field_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_terms` ( 
			`term_id` INT(11) NOT NULL AUTO_INCREMENT, 
			`terms` LONGTEXT NOT NULL COLLATE 'utf8_unicode_ci', 
			`option_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', 
			PRIMARY KEY (`term_id`) 
		);"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_fields_options` ( 
		  `option_id` int(11) unsigned NOT NULL auto_increment, 
		  `field_id` int(11) unsigned NOT NULL, 
		  `label` varchar(255) collate utf8_unicode_ci NOT NULL, 
		  `order` smallint(6) NOT NULL default '999', 
		  PRIMARY KEY  (`option_id`), 
		  KEY `field_id` (`field_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_fields_search` ( 
		  `item_id` int(11) unsigned NOT NULL, 
		  `profile_type` smallint(11) unsigned default NULL, 
		  PRIMARY KEY  (`item_id`), 
		  KEY `profile_type` (`profile_type`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_fields_values` ( 
		  `item_id` int(11) unsigned NOT NULL, 
		  `field_id` int(11) unsigned NOT NULL, 
		  `index` smallint(3) unsigned NOT NULL default '0', 
		  `value` text collate utf8_unicode_ci NOT NULL, 
		  PRIMARY KEY  (`item_id`,`field_id`,`index`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_locations` ( 
		  `id` INTEGER(11) NOT NULL AUTO_INCREMENT, 
		  `begin_num` BIGINT(15) NOT NULL DEFAULT '0', 
		  `end_num` BIGINT(15) NOT NULL DEFAULT '0', 
		  `country` VARCHAR(50) COLLATE utf8_general_ci DEFAULT NULL, 
		  `name` VARCHAR(255) COLLATE utf8_general_ci DEFAULT NULL, 
		  PRIMARY KEY (`id`), 
		  INDEX `name` (`name`) 
		)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_listitems` ( 
		  `listitem_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		  `list_id` INT(11) UNSIGNED NOT NULL, 
		  `child_id` INT(11) UNSIGNED NOT NULL, 
		  PRIMARY KEY (`listitem_id`), 
		  INDEX `list_id` (`list_id`), 
		  INDEX `child_id` (`child_id`) 
		)COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_lists` ( 
		  `list_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		  `title` VARCHAR(64) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci', 
		  `owner_id` INT(11) UNSIGNED NOT NULL, 
		  `child_count` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
		  PRIMARY KEY (`list_id`), 
		  INDEX `owner_id` (`owner_id`) 
		)COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_membership` ( 
		  `resource_id` INT(11) UNSIGNED NOT NULL, 
		  `user_id` INT(11) UNSIGNED NOT NULL, 
		  `active` TINYINT(1) NOT NULL DEFAULT '0', 
		  `resource_approved` TINYINT(1) NOT NULL DEFAULT '0', 
		  `user_approved` TINYINT(1) NOT NULL DEFAULT '0', 
		  `message` TEXT NULL COLLATE 'utf8_unicode_ci', 
		  `title` TEXT NULL COLLATE 'utf8_unicode_ci', 
		  `type` TEXT NOT NULL, 
		  PRIMARY KEY (`resource_id`, `user_id`), 
		  INDEX `REVERSE` (`user_id`) 
		)COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_markers` ( 
		  `marker_id` int(11) NOT NULL auto_increment, 
		  `page_id` int(11) NOT NULL default '0', 
		  `latitude` float(10,6) NOT NULL, 
		  `longitude` float(10,6) NOT NULL, 
		  PRIMARY KEY  (`marker_id`), 
		  KEY `page_id` (`page_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_views` ( 
		  `view_id` int(11) NOT NULL auto_increment, 
		  `user_id` bigint(15) NOT NULL default '0', 
		  `ip` bigint(15) NOT NULL default '0', 
		  `view_date` datetime default NULL, 
		  `page_id` int(11) NOT NULL default '0', 
		  `country` varchar(255) NULL default NULL, 
		  PRIMARY KEY  (`view_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_content` ( 
		  `content_id` int(11) NOT NULL auto_increment, 
		  `page_id` int(11) NOT NULL default '0', 
		  `name` varchar(128) NOT NULL, 
		  `type` varchar(32) NOT NULL, 
		  `parent_content_id` int(11) NOT NULL default '0', 
		  `params` text NOT NULL, 
		  `attribs` text NOT NULL, 
		  `order` int(11) NOT NULL default '1', 
		  `is_timeline` TINYINT NOT NULL default '0', 
		  PRIMARY KEY  (`content_id`), 
		  KEY `page_id` (`page_id`,`order`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_pages` ( 
		  `page_id` INT(11) NOT NULL AUTO_INCREMENT, 
		  `name` VARCHAR(128) NOT NULL COLLATE 'latin1_general_ci', 
		  `displayname` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
		  `url` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
		  `title` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
		  `description` TEXT NULL COLLATE 'utf8_unicode_ci', 
		  `keywords` TEXT NULL COLLATE 'utf8_unicode_ci', 
		  `layout` VARCHAR(128) NULL DEFAULT NULL, 
		  `package_id` INT(11) NOT NULL DEFAULT '0', 
		  `view_count` INT(11) NOT NULL DEFAULT '0', 
		  `user_id` INT(11) NOT NULL DEFAULT '0', 
		  `fragment` TINYINT(1) NOT NULL DEFAULT '0', 
		  `photo_id` INT(11) NOT NULL DEFAULT '0', 
		  `country` VARCHAR(255) NULL DEFAULT NULL, 
		  `city` VARCHAR(255) NULL DEFAULT NULL, 
		  `street` VARCHAR(255) NULL DEFAULT NULL, 
		  `website` VARCHAR(255) NULL DEFAULT NULL, 
		  `phone` VARCHAR(255) NULL DEFAULT NULL, 
		  `comment_count` INT(11) NOT NULL DEFAULT '0', 
		  `creation_date` DATETIME NULL DEFAULT NULL, 
		  `modified_date` DATETIME NULL DEFAULT NULL, 
		  `featured` TINYINT(1) NOT NULL DEFAULT '0', 
		  `approved` TINYINT(1) NOT NULL DEFAULT '0', 
		  `auto_set` TINYINT(4) NOT NULL DEFAULT '0', 
		  `state` VARCHAR(255) NULL DEFAULT NULL, 
		  `search` TINYINT(1) NOT NULL DEFAULT '1', 
		  `parent_type` VARCHAR(100) NULL DEFAULT NULL, 
		  `parent_id` INT(11) NOT NULL DEFAULT '0', 
		  `note` TEXT NULL, 
		  `unique_views` INT(11) NOT NULL DEFAULT '0', 
		  `default` TINYINT(1) NOT NULL DEFAULT '0', 
		  `enabled` TINYINT(1) NOT NULL DEFAULT '0', 
		  `sponsored` TINYINT(1) NOT NULL DEFAULT '0', 
		  `is_timeline` TINYINT NOT NULL default '0', 
		  `timeline_converted` TINYINT NOT NULL default '0', 
		  `cover_id` INT(11) unsigned NULL default '0', 
		  `set_id` int(11) DEFAULT '0', 
			PRIMARY KEY (`page_id`), 
			UNIQUE KEY `name` (`name`), 
			INDEX `user_id` (`user_id`), 
			INDEX `set_id` (`set_id`), 
			INDEX `approved` (`approved`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_modules` ( 
		  `module_id` int(11) NOT NULL auto_increment, 
		  `name` varchar(50) NOT NULL default '', 
		  `widget` varchar(255) default NULL, 
		  `order` int(11) NOT NULL default '0', 
		  `params` text, 
		  `informed` TINYINT UNSIGNED NOT NULL DEFAULT '0', 
		  PRIMARY KEY  (`name`), 
		  KEY `module_id` (`module_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_favorites` ( 
		  `page_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
		  `page_fav_id` int(11) UNSIGNED NOT NULL DEFAULT '0', 
		  PRIMARY KEY (`page_id`,`page_fav_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_packages` ( 
			`package_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
			`name` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
			`description` TEXT NULL COLLATE 'utf8_unicode_ci', 
			`price` FLOAT NOT NULL DEFAULT '0', 
			`recurrence` INT(11) NOT NULL, 
			`recurrence_type` ENUM('day','week','month','year','forever') NOT NULL COLLATE 'utf8_unicode_ci', 
			`duration` INT(11) NOT NULL, 
			`duration_type` ENUM('day','week','month','year','forever') NOT NULL COLLATE 'utf8_unicode_ci', 
			`enabled` TINYINT(1) NOT NULL DEFAULT '1', 
			`featured` TINYINT(1) NOT NULL DEFAULT '0', 
			`sponsored` TINYINT(1) NOT NULL DEFAULT '0', 
			`autoapprove` TINYINT(1) NOT NULL DEFAULT '0', 
			`tell_friend` TINYINT(1) NOT NULL DEFAULT '0', 
			`print` TINYINT(1) NOT NULL DEFAULT '0', 
			`edit_columns` TINYINT(1) NOT NULL DEFAULT '0', 
			`edit_layout` TINYINT(1) NOT NULL DEFAULT '0', 
			`modules` TEXT NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`auth_view` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`auth_comment` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`auth_posting` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`style` TINYINT(1) NOT NULL DEFAULT '1', 
			PRIMARY KEY (`package_id`) 
		) COLLATE='utf8_unicode_ci' ENGINE=InnoDB;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_subscriptions` ( 
			`subscription_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			`page_id` INT(11) UNSIGNED NOT NULL, 
			`package_id` INT(11) UNSIGNED NOT NULL, 
			`status` ENUM('initial','trial','pending','active','cancelled','expired','overdue','refunded') NOT NULL DEFAULT 'initial' COLLATE 'utf8_unicode_ci', 
			`active` TINYINT(1) NOT NULL DEFAULT '0', 
			`creation_date` DATETIME NOT NULL, 
			`modified_date` DATETIME NULL DEFAULT NULL, 
			`payment_date` DATETIME NULL DEFAULT NULL, 
			`expiration_date` DATETIME NULL DEFAULT NULL, 
			`gateway_id` INT(11) UNSIGNED NOT NULL, 
			`gateway_profile_id` INT(11) UNSIGNED NOT NULL, 
			PRIMARY KEY (`subscription_id`) 
		) COLLATE='utf8_unicode_ci' ENGINE=InnoDB;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_claims` ( 
			`claim_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			`claimer_name` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`claimer_email` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`claimer_phone` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`description` TEXT NULL COLLATE 'utf8_unicode_ci', 
			`creation_date` DATETIME NULL DEFAULT NULL, 
			`user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`page_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`status` ENUM('pending','declined','approved') NOT NULL DEFAULT 'pending' COLLATE 'utf8_unicode_ci', 
			PRIMARY KEY (`claim_id`), 
			KEY `page_id` (`page_id`) 
		) COLLATE='utf8_unicode_ci' ENGINE=InnoDB;"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_imports` ( 
			`import_id` INT(11) NOT NULL AUTO_INCREMENT, 
			`file_name` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
			`file_id` INT(11) NOT NULL DEFAULT '0', 
			`seek` INT(11) UNSIGNED NOT NULL DEFAULT '1', 
			`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', 
			`options` TEXT NOT NULL COLLATE 'utf8_unicode_ci', 
			`creation_date` DATETIME NULL DEFAULT NULL, 
			`import_count` INT(11) NULL DEFAULT '0', 
			PRIMARY KEY (`import_id`) 
		) 
		ENGINE=InnoDB DEFAULT CHARSET=latin1;"); 

		$db->query("INSERT IGNORE INTO `engine4_page_content` (`content_id`, `page_id`, `name`, `type`, `parent_content_id`, `params`, `attribs`, `order`) VALUES (110, 1, 'core.menu-mini', 'widget', 100, '', '', 1);"); 
		$db->query("INSERT IGNORE INTO `engine4_page_content` (`content_id`, `page_id`, `name`, `type`, `parent_content_id`, `params`, `attribs`, `order`) VALUES (100, 1, 'main', 'container', 0, '', '', 1);"); 
		$db->query("INSERT IGNORE INTO `engine4_page_content` (`content_id`, `page_id`, `name`, `type`, `parent_content_id`, `params`, `attribs`, `order`) VALUES (111, 1, 'core.menu-logo', 'widget', 100, '', '', 2);"); 
		$db->query("INSERT IGNORE INTO `engine4_page_content` (`content_id`, `page_id`, `name`, `type`, `parent_content_id`, `params`, `attribs`, `order`) VALUES (112, 1, 'core.menu-main', 'widget', 100, '', '', 3);"); 
		$db->query("INSERT IGNORE INTO `engine4_page_content` (`content_id`, `page_id`, `name`, `type`, `parent_content_id`, `params`, `attribs`, `order`) VALUES (200, 2, 'main', 'container', 0, '', '', 1);"); 
		$db->query("INSERT IGNORE INTO `engine4_page_content` (`content_id`, `page_id`, `name`, `type`, `parent_content_id`, `params`, `attribs`, `order`) VALUES (210, 2, 'core.menu-footer', 'widget', 200, '', '', 2);"); 

		$db->query("INSERT IGNORE INTO `engine4_page_pages` (`page_id`, `name`, `displayname`, `url`, `title`, `description`, `keywords`, `fragment`, `layout`, `view_count`) VALUES (1, 'header', 'Site Header', 'header', '', '', '', 1, '', 0);");
		$db->query("INSERT IGNORE INTO `engine4_page_pages` (`page_id`, `name`, `displayname`, `url`, `title`, `description`, `keywords`, `fragment`, `layout`, `view_count`) VALUES (2, 'footer', 'Site Footer', 'footer', '', '', '', 1, '', 0);");

		$db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
		('page_create', 'page', '{actors:\$subject:\$object} created a new page: ', 1, 7, 1, 1, 1, 1), 
		('page_cover_photo_update', 'page', '{item:\$subject} has added a new cover photo.', 1, 3, 1, 1, 1, 1);"); 


		$db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES  
		('post_page', 'page', '{item:\$subject} has posted on your {item:\$object:\$label:\$action}.', 0, '', 1), 
		('page_add_admin', 'page', '{item:\$subject} has added you on page {item:\$object:\$label} as Admin.', 0, '', 1), 
		('page_add_employer', 'page', '{item:\$subject} has added you on page {item:\$object:\$label} as Employer.', 0, '', 1), 
		('page_delete_admin', 'page', '{item:\$subject} has deleted you from page {item:\$object:\$label}.', 0, '', 1);"); 

		$db->query("INSERT IGNORE INTO `engine4_page_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES 
		(1, 'profile_type', 'Page Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);"); 

		$db->query("INSERT IGNORE INTO `engine4_page_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES  
		(0, 0, 1, 1);"); 

		$db->query("INSERT IGNORE INTO `engine4_page_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES  
		(1, 1, 'Default', 1), 
		(2, 1, 'Local Business', 2), 
		(3, 1, 'Automotive', 3), 
		(4, 1, 'Banking and Finance', 4), 
		(5, 1, 'Bar', 5), 
		(6, 1, 'Cafe', 6), 
		(7, 1, 'Club', 7), 
		(8, 1, 'Education', 8), 
		(9, 1, 'Event Planning Service', 9), 
		(10, 1, 'Health and Beauty', 10), 
		(11, 1, 'Hotel', 11), 
		(12, 1, 'Museum', 12), 
		(13, 1, 'Real Estate', 13), 
		(14, 1, 'Restaurent', 14), 
		(15, 1, 'Store', 15), 
		(16, 1, 'Travel', 16), 
		(17, 1, 'Public Figure', 17), 
		(18, 1, 'Actor', 18), 
		(19, 1, 'Band', 19), 
		(20, 1, 'Model', 20), 
		(21, 1, 'Musician', 21), 
		(22, 1, 'Sports Team', 22), 
		(23, 1, 'Writer', 23);"); 


		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
		('core_main_page', 'page', 'Pages', '', '{\"route\":\"page_browse\"}', 'core_main', '', 6), 
		('core_sitemap_page', 'page', 'Pages', '', '{\"route\":\"page_browse\"}', 'core_sitemap', '', 6), 
		('core_admin_main_plugins_page', 'page', 'page_menu_Page', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"settings\"}', 'core_admin_main_plugins', '', 888), 

		('page_profile_timeline', 'page', 'Switch View Mode', 'Page_Plugin_Menus', '', 'page_profile', '', '2'), 

		('page_profile_edit', 'page', 'Edit Page', 'Page_Plugin_Menus', '', 'page_profile', '', 1), 
		('page_profile_share', 'page', 'Share Page', 'Page_Plugin_Menus', '', 'page_profile', '', 5), 
		('page_profile_favorite', 'page', 'Add Page To Favorites', 'Page_Plugin_Menus', '', 'page_profile', '', 6), 

		('page_admin_main_manage', 'page', 'View Pages', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"manage\"}', 'page_admin_main', '', 1), 
		('page_admin_main_settings', 'page', 'Global Settings', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"settings\"}', 'page_admin_main', '', 2), 
		('page_admin_main_fields', 'page', 'Fields', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"fields\"}', 'page_admin_main', '', 4), 
		('page_admin_settings_global', 'page', 'Global Settings', NULL, '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"settings\"}', 'page_admin_settings', '', 1), 
		('page_admin_main_editor', 'page', 'Default Layout Editor', NULL , '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"editor\"}', 'page_admin_main', '', 6), 
		('page_admin_main_permission', 'page', 'Permission Settings', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"permission\"}', 'page_admin_main', '', 3), 
		('page_admin_main_subscription', 'page', 'Page Subscriptions', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"subscription\"}', 'page_admin_main', '', 10), 
		('page_profile_store', 'page', 'Manage Products', 'Page_Plugin_Menus', '{\"route\":\"store_settings\", \"action\":\"products\"}', 'page_profile', '', 7), 
		('page_profile_donation', 'page', 'Manage Donations', 'Page_Plugin_Menus', '{\"route\":\"donation_extended\",\"controller\":\"page\",\"action\":\"index\"}', 'page_profile', '', 8), 
		('page_admin_main_transactions', 'page', 'Page Transactions', NULL, '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"transactions\"}', 'page_admin_main', '', 11), 
		('page_main_pages', 'page', 'Browse Pages', 'Page_Plugin_Menus', '{\"route\":\"page_browse\"}', 'page_main', '', 1), 
		('page_main_manage', 'page', 'HE_My Pages', 'Page_Plugin_Menus', '{\"route\":\"page_manage\",\"action\":\"manage\"}', 'page_main', '', 2), 
		('page_main_create', 'page', 'Create New Page', 'Page_Plugin_Menus', '{\"route\":\"page_create\",\"action\":\"create\"}', 'page_main', '', 3), 
		('page_profile_print', 'page', 'Print Page', 'Page_Plugin_Menus', '', 'page_profile', '', 6), 
		('page_admin_main_claim', 'page', 'Manage Claims', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"claim\"}', 'page_admin_main', '', 5), 
		('page_main_claim', 'page', 'Claim a Page', 'Page_Plugin_Menus', '{\"route\":\"page_claim\",\"action\":\"claim\"}', 'page_main', '', 4), 
		('page_quick_create', 'page', 'Create New Page', '', '{\"route\":\"page_create\",\"action\":\"create\",\"class\":\"buttonlink icon_page_new\"}', 'page_quick', NULL, 1), 
		('page_admin_import_sitepage', 'page', 'Import From Site Page', NULL, '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"import\",\"action\":\"sitepage\"}', 'page_admin_import', NULL, 3), 
		('page_admin_import_file', 'page', 'Import From CSV File', NULL, '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"import\"}', 'page_admin_import', NULL, 1), 
		('page_admin_main_import', 'page', 'Import Pages', '', '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"import\"}', 'page_admin_main', '', 12), 
		('page_admin_main_categories', 'page', 'Categories', NULL, '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"categories\"}', 'page_admin_main', NULL, 5);"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES 
		(1, 'page', 'create', 1, NULL), 
		(1, 'page', 'view', 1, NULL), 
		(1, 'page', 'comment', 1, NULL), 
		(1, 'page', 'posting', 1, NULL), 
		(1, 'page', 'auth_posting', 5,'[\"registered\", \"team\", \"likes\"]'), 
		(1, 'page', 'auth_view', 5,'[\"everyone\", \"registered\", \"likes\", \"team\"]'), 
		(1, 'page', 'auth_comment', 5,'[\"registered\", \"team\", \"likes\"]'), 

		(2, 'page', 'create', 1, NULL), 
		(2, 'page', 'view', 1, NULL), 
		(2, 'page', 'comment', 1, NULL), 
		(2, 'page', 'posting', 1, NULL), 
		(2, 'page', 'auth_posting', 5,'[\"registered\", \"team\", \"likes\"]'), 
		(2, 'page', 'auth_view', 5,'[\"everyone\", \"registered\", \"likes\", \"team\"]'), 
		(2, 'page', 'auth_comment', 5,'[\"registered\", \"team\", \"likes\"]'), 

		(3, 'page', 'create', 1, NULL), 
		(3, 'page', 'view', 1, NULL), 
		(3, 'page', 'comment', 1, NULL), 
		(3, 'page', 'posting', 1, NULL), 
		(3, 'page', 'auth_posting', 5,'[\"registered\", \"team\", \"likes\"]'), 
		(3, 'page', 'auth_view', 5,'[\"everyone\", \"registered\", \"likes\", \"team\"]'), 
		(3, 'page', 'auth_comment', 5,'[\"registered\", \"team\", \"likes\"]'), 

		(4, 'page', 'create', 1, NULL), 
		(4, 'page', 'view', 1, NULL), 
		(4, 'page', 'comment', 1, NULL), 
		(4, 'page', 'posting', 1, NULL), 
		(4, 'page', 'auth_posting', 5,'[\"registered\", \"team\", \"likes\"]'), 
		(4, 'page', 'auth_view', 5,'[\"everyone\", \"registered\", \"likes\", \"team\"]'), 
		(4, 'page', 'auth_comment', 5,'[\"registered\", \"team\", \"likes\"]'), 

		(5, 'page', 'view', 1, NULL);"); 

		$db->query("INSERT IGNORE INTO `engine4_page_pages` 
		(`page_id`, `name`, `displayname`, `url`, `title`, `description`, `keywords`, `layout`, `view_count`, `user_id`, `fragment`, `photo_id`, `country`, `city`, `street`, `website`, `phone`, `comment_count`, `creation_date`, `modified_date`, `featured`, `approved`, `state`, `search`, `parent_type`, `parent_id`, `note`, `unique_views`, `default`)
		VALUES (NULL, 'default', '', 'default', '', NULL, NULL, NULL, '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, '0', '0', NULL, '0', NULL, '0', NULL, '0', '');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'page' as `type`, 
			'auth_features' as `name`, 
			5 as `value`, 
			'[\"pagealbum\",\"pagedocument\",\"pageblog\",\"pagediscussion\",\"pageevent\",\"pagemusic\",\"pagevideo\",\"rate\",\"store\",\"pagecontact\",\"pagefaq\",\"donation\",\"offers\"]' as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'page' as `type`, 
			'allowed_pages' as `name`, 
			3 as `value`, 
			16 as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		SELECT 
		  level_id as `level_id`, 
		  'page' as `type`, 
		  'layout_editor' as `name`, 
		  1 as `value`, 
		  '[\"\"]' as `params` 
		FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		SELECT 
		  level_id as `level_id`, 
		  'page' as `type`, 
		  'edit_cols' as `name`, 
		  1 as `value`, 
		  '[\"\"]' as `params` 
		FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'page' as `type`, 
			'style' as `name`, 
			1 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');"); 

		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'page' as `type`, 
			'style' as `name`, 
			1 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('user');"); 


		$db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES 
			('page_subscription_refunded', 'payment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_subscription_title],[page_subscription_description],[object_link]'), 
			('page_subscription_recurrence', 'payment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_subscription_title],[page_subscription_description],[object_link]'), 
			('page_subscription_pending', 'payment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_subscription_title],[page_subscription_description],[object_link]'), 
			('page_subscription_overdue', 'payment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_subscription_title],[page_subscription_description],[object_link]'), 
			('page_subscription_expired', 'payment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_subscription_title],[page_subscription_description],[object_link]'), 
			('page_subscription_cancelled', 'payment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_subscription_title],[page_subscription_description],[object_link]'), 
			('page_subscription_active', 'payment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[page_subscription_title],[page_subscription_description],[object_link]'), 
			('page_claim', 'page', '[page],[claimer_name],[message]'), 
		  ('page_expired', 'page', '[page],[owner_name],[message]');"); 



		$db->query("INSERT IGNORE INTO `engine4_page_packages` (`package_id`, `title`, `name`, `description`, `price`, `recurrence`, `recurrence_type`, `duration`, `duration_type`, `enabled`, `featured`, `sponsored`, `autoapprove`, `tell_friend`, `print`, `edit_columns`, `edit_layout`, `modules`, `auth_view`, `auth_comment`, `auth_posting`) VALUES
		(1, 'Default', 'Default', 'Default Package', 0, 0, 'forever', 0, 'forever', 1, 0, 0, 1, 0, 0, 0, 1, 'a:11:{i:0;s:9:\"pagealbum\";i:1;s:8:\"pageblog\";i:2;s:14:\"pagediscussion\";i:3;s:12:\"pagedocument\";i:4;s:9:\"pageevent\";i:5;s:9:\"pagemusic\";i:6;s:9:\"pagevideo\";i:7;s:4:\"rate\";i:8;s:11:\"pagecontact\";i:9;s:5:\"store\";i:10;s:7:\"pagefaq\";i:10;s:8:\"donation\";}', 'a:4:{i:0;s:8:\"everyone\";i:1;s:10:\"registered\";i:2;s:5:\"likes\";i:3;s:4:\"team\";}', 'a:3:{i:0;s:10:\"registered\";i:1;s:5:\"likes\";i:2;s:4:\"team\";}', 'a:3:{i:0;s:10:\"registered\";i:1;s:5:\"likes\";i:2;s:4:\"team\";}');"); 
			 
		$db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'page' as `type`, 
			'auto_approve' as `name`, 
			1 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');"); 
			 
		$sql= "SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'store' LIMIT 1"; 

		if ($db->fetchOne($sql)){ 
			$db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`, `informed`) VALUES 
			('store', 'store.page-profile-products', 15, '{\"title\":\"Store\", \"titleCount\":true}', 1);"); 
		}; 

		$sql= "SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'donation' LIMIT 1"; 

		if ($db->fetchOne($sql)){ 
			$db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`, `informed`) VALUES 
			('donation', 'donation.page-profile-donations', 16, '{\"title\":\"Donation\", \"titleCount\":true}', 1);"); 
		}; 

		$sql = "SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'offers' LIMIT 1"; 

		if ($db->fetchOne($sql)) { 
		$db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`, `informed`) VALUES 
		('offers', 'offers.profile-offers', 5, '{\"title\":\"Offers\", \"titleCount\":true}', 1);"); 
		} 

		$db->query("INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES 
		('page_main', 'standard', 'Page Main Navigation Menu', 999), 
		('page_profile', 'standard', 'Page Profile Options Menu', 999), 
		('page_quick', 'standard', 'Page Quick Navigation Menu', 999);"); 


		$sql = "SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'inviter' LIMIT 1"; 

		if (null != $db->fetchRow($sql)) { 
		$db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`, `informed`) VALUES 
		('inviter', 'inviter.page-inviter', '0','{\"title\":\"Inviter\", \"titleCount\":true}' ,'1');"); 
		} 

		$db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES  
		('page_like', 'page', '{item:\$subject} likes your page {item:\$object:\$label}.', 0, '', 1);"); 

		$sql = "INSERT IGNORE INTO  `engine4_core_pages` (`name`,     `displayname`,     `title`,     `description`, `provides`, `view_count`) VALUES 
		('page_index_index', 'Browse Pages', 'Browse Pages', 'This is the browse page for pages', 'no-subject', 0);"; 

		$db->query($sql); 
		$page_id = $db->lastInsertId(); 

		$sql = "INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
		($page_id, 'container', 'top', NULL, 1);"; 
		$db->query($sql); 
		$top_content_id = $db->lastInsertId(); 

		$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'middle', $top_content_id, 6); 
CONTENT;
		$db->query($sql); 
		$middle_content_id = $db->lastInsertId(); 

		$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'widget', 'page.navigation-tabs', $middle_content_id, 3); 
CONTENT;
		$db->query($sql); 

		$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'main', NULL, 2); 
CONTENT;
		$db->query($sql); 
		$main_content_id = $db->lastInsertId(); 

		$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'right', $main_content_id, 5); 
CONTENT;
		$db->query($sql); 
		$right_content_id = $db->lastInsertId(); 

		$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'page.page-search', $right_content_id, 10, '{\"title\":\"Search Page\"}'), 
($page_id, 'widget', 'page.page-categories', $right_content_id, 11, '{\"title\":\"Page Categories\"}'), 
($page_id, 'widget', 'page.page-locations', $right_content_id, 12, '{\"title\":\"Page Locations\"}'), 
($page_id, 'widget', 'page.page-tags', $right_content_id, 13, '{\"title\":\"Page Tags\"}'), 
($page_id, 'widget', 'page.recent-pages', $right_content_id, 14, '{\"title\":\"Recent Pages\",\"titleCount\":false}'), 
($page_id, 'widget', 'page.popular-pages', $right_content_id, 15, '{\"title\":\"Most Popular Pages\",\"titleCount\":false}'), 
($page_id, 'widget', 'page.sponsored-pages', $right_content_id, 16, '{\"title\":\"Sponsored Pages\",\"titleCount\":false}'), 
($page_id, 'widget', 'page.featured-pages', $right_content_id, 17, '{\"title\":\"Featured Pages\",\"titleCount\":false}'); 
CONTENT;
		$db->query($sql); 

		$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'middle', $main_content_id, 6); 
CONTENT;
		$db->query($sql); 
		$middle_content_id = $db->lastInsertId(); 

		$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'page.sponsored-carousel', $middle_content_id, 6, '{\"title\":\"Sponsored Pages Carousel\",\"titleCount\":true}'), 
($page_id, 'widget', 'page.page-abc', $middle_content_id, 7, '[\"[]\"]'), 
($page_id, 'widget', 'page.browse-pages', $middle_content_id, 8, '[\"[]\"]'); 
CONTENT;
		$db->query($sql); 


		$he_module = $db->query("SELECT * FROM engine4_core_modules WHERE name = 'hebadge' AND enabled = 1")->fetch(); 

		if ($he_module){  

			$db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`, `informed`) VALUES  
			('hebadge', 'hebadge.page-badges', 999, '{\"title\":\"HEBADGE_WIDGET_TITLE_PAGE_BADGES\",\"titleCount\":\"true\"}', 1);"); 
			 
			/* Browse Pages Widget  */ 
			$he_content = $db->query(" 
				SELECT  
				NULL AS content_id, c.page_id AS page_id, 'widget' AS type, 'hebadge.pages-badges' AS name, c.parent_content_id AS parent_content_id, c.`order`+1 AS `order`, '[]' AS params, '' AS attribs  
				FROM engine4_core_pages AS p  
				JOIN engine4_core_content AS c_main ON c_main.page_id = p.page_id AND c_main.name = 'main' 
				JOIN engine4_core_content AS c_block ON c_block.page_id = p.page_id AND c_block.parent_content_id = c_main.content_id AND c_block.name = 'middle' 
				JOIN engine4_core_content AS c ON c.page_id = p.page_id AND c.parent_content_id = c_block.content_id AND c.name = 'page.page-abc' 
				WHERE p.name = 'page_index_index' 
			")->fetch(); 
			 

			if ($he_content){ 
			  $he_table = new Zend_Db_Table(array('name' => 'engine4_core_content')); 
			  $he_table->insert($he_content); 
			  $he_content_id = $db->lastInsertId(); 
			  $db->query("UPDATE engine4_core_content SET `order`=`order`+1 WHERE parent_content_id = {$he_content['parent_content_id']} AND `order` >= {$he_content['order']} AND content_id != {$he_content_id}");        
			} 
			 
			 
			/* Default Badges Icons  */ 
			$he_content = $db->query(" 
				SELECT  
				NULL AS content_id, c.page_id AS page_id, 'hebadge.page-badgeicons' AS name, 'widget' AS type, c_block.content_id AS parent_content_id, c.`order`+1 AS `order`, '[]' AS params, '' AS attribs  
				FROM engine4_page_pages AS p  
				JOIN engine4_page_content AS c_main ON c_main.page_id = p.page_id AND c_main.name = 'main' 
				JOIN engine4_page_content AS c_block ON c_block.page_id = p.page_id AND c_block.parent_content_id = c_main.content_id AND c_block.name = 'left' 
				JOIN engine4_page_content AS c ON c.page_id = p.page_id AND c.parent_content_id = c_block.content_id AND c.name = 'page.profile-photo' 
				WHERE p.name = 'default' 
			")->fetch();     

			if ($he_content){ 
			  $he_table = new Zend_Db_Table(array('name' => 'engine4_page_content')); 
			  $he_table->insert($he_content); 
			  $he_content_id = $db->lastInsertId(); 
			  $db->query("UPDATE engine4_page_content SET `order`=`order`+1 WHERE parent_content_id = {$he_content['parent_content_id']} AND `order` >= {$he_content['order']} AND content_id != {$he_content_id}"); 
			}     
			 
		} 

		$db->query("INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`) VALUES ('Page Cleanup', 'page', 'Page_Plugin_Task_Cleanup');"); 

		$db->query("UPDATE `engine4_page_pages` SET `set_id`=1 WHERE `name` NOT IN ('header', 'footer', 'default')"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_category_set` ( 
		  `id` int(11) NOT NULL AUTO_INCREMENT, 
		  `caption` varchar(255) DEFAULT NULL, 
		  PRIMARY KEY (`id`), 
		  UNIQUE KEY `id` (`id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 

		$db->query("INSERT IGNORE INTO `engine4_page_category_set` SET `caption`='Others'"); 

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_page_category_set_category` ( 
		  `set_id` int(11) NOT NULL, 
		  `cat_id` int(11) NOT NULL, 
		  `order` int(11) NOT NULL 
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;"); 

		$db->query("INSERT IGNORE INTO `engine4_page_category_set_category` (`set_id`, `cat_id`, `order`) SELECT 1 as `set_id`, `option_id` as `cat_id`, `option_id` as `order`  FROM `engine4_page_fields_options` WHERE `field_id` = 1");

		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('page_admin_import_csvfiles', 'page', 'Imported Files', NULL, '{\"route\":\"admin_default\",\"module\":\"page\",\"controller\":\"import\",\"action\":\"files\"}', 'page_admin_import', NULL, 1, 0, 2);");

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
    $db->beginTransaction();

    try{

      $select = new Zend_Db_Select($db);

      // profile page
      $select
        ->from('engine4_core_pages')
        ->where('name = ?', 'user_profile_index')
        ->limit(1);

      $page_id = $select->query()->fetchObject()->page_id;

      // page.profile-pages

      // Check if it's already been placed
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'page.profile-pages')
      ;
      $info = $select->query()->fetch();

      if( empty($info) ) {

        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_core_content')
          ->where('page_id = ?', $page_id)
          ->where('type = ?', 'container')
          ->limit(1);
        $container_id = $select->query()->fetchObject()->content_id;

        // middle_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
          ->from('engine4_core_content')
          ->where('parent_content_id = ?', $container_id)
          ->where('type = ?', 'container')
          ->where('name = ?', 'middle')
          ->limit(1);
        $middle_id = $select->query()->fetchObject()->content_id;

        // tab_id (tab container) may not always be there
        $select
          ->reset('where')
          ->where('type = ?', 'widget')
          ->where('name = ?', 'core.container-tabs')
          ->where('page_id = ?', $page_id)
          ->limit(1);
        $tab_id = $select->query()->fetchObject();

        if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
        } else {
          $tab_id = null;
        }

        // tab on profile
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type'    => 'widget',
          'name'    => 'page.profile-pages',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order'   => 131,
          'params'  => '{"title":"Pages","titleCount":true}',
        ));
      }

      $db->commit();
    }
    catch (Exception $e){
      $db->rollBack();
      throw $e;
    }

    // rate integration
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'rate')
      ->where('version >= ?', '4.0.1')
      ->limit(1);

    $rate_module = $select->query()->fetchObject();

    if ($rate_module && $rate_module->name) {
      $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`) "
        . "VALUES ('rate', 'rate.page-review', 16, '{\"title\":\"RATE_REVIEW_TABITEM\", \"titleCount\":true}');");

      $db->query("INSERT IGNORE INTO `engine4_rate_types` (`category_id`, `label`, `order`) "
        . "SELECT `option_id` AS `category_id`, 'Rate' AS `label`, 1 AS `order` "
        . "FROM `engine4_page_fields_options` "
        . "WHERE `field_id` = 1;");
    }

    // weather integration
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    $select
      ->from('engine4_core_modules')
      ->where('name = ?', 'weather')
      ->limit(1);

    $weather_module = $select->query()->fetchObject();

    if ($weather_module && $weather_module->name) {
      $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`) VALUES "
        . "('weather', 'weather.weather', 21, '{\"title\":\"Weather\", \"titleCount\":false}');");
    }
  }

  protected function checkModule($module)
  {
    $db = $this->getDb();

    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_modules')
      ->where('name = ?', $module)
      ->where('enabled = 1')
      ->limit(1);

    $info = $select->query()->fetch();

    return (!empty($info));
  }
}