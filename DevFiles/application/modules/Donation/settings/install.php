<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       06.09.12
 * @time       10:26
 */
class Donation_Installer extends Engine_Package_Installer_Module {

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

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'page')
                ->where('enabled = ?', 1);

        $page = $db->fetchRow($select);

        if ($page) {
            if (version_compare($page['version'], '4.2.5') < 0) {
                $error_message = $translate->_('You should first update your Page module.');
                return $this->_error($error_message);
            }
        }

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'like')
                ->where('enabled = ?', 1);

        $like = $db->fetchRow($select);

        if (!$like) {
            $error_message = $translate->_('Error! This plugin requires Hire-Experts Like module. It is free module and can be downloaded from Hire-Experts.com');
            return $this->_error($error_message);
        }

        if (version_compare($like['version'], '4.2.1') < 0) {
            $error_message = $translate->_('You should first update your Like module.');
            return $this->_error($error_message);
        }

        $operation = $this->_databaseOperationType;
        $module_name = 'donation';
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

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_donation_donations` ( 
			`donation_id` INT(10) NOT NULL AUTO_INCREMENT, 
			`title` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`short_desc` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`description` TEXT NULL COLLATE 'utf8_unicode_ci', 
			`view_count` INT(11) NOT NULL DEFAULT '0', 
			`owner_type` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`owner_id` INT(11) NOT NULL DEFAULT '0', 
			`page_id` INT(11) NOT NULL DEFAULT '0', 
			`photo_id` INT(11) NOT NULL DEFAULT '0', 
			`min_amount` DOUBLE(62,6) NOT NULL DEFAULT '0.000000', 
			`type` ENUM('project','charity','fundraise') NULL DEFAULT 'project' COLLATE 'utf8_unicode_ci', 
			`target_sum` DOUBLE(62,6) NOT NULL DEFAULT '0.000000', 
			`raised_sum` DOUBLE(62,6) NOT NULL, 
			`can_choose_amount` TINYINT(1) NOT NULL DEFAULT '1', 
			`allow_anonymous` TINYINT(1) NOT NULL DEFAULT '1', 
			`expiry_date` DATETIME NOT NULL, 
			`creation_date` DATETIME NOT NULL, 
			`modified_date` DATETIME NOT NULL, 
			`country` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`state` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`city` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`street` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`phone` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`enabled` TINYINT(1) NOT NULL DEFAULT '0', 
			`predefine_list` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`category_id` INT(11) NOT NULL, 
			`parent_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`status` ENUM('active','expired','initial','cancelled') NOT NULL DEFAULT 'initial', 
			`approved` TINYINT(1) NOT NULL DEFAULT '0', 
			PRIMARY KEY (`donation_id`), 
			INDEX `owner_type` (`owner_type`, `owner_id`)) 
		COLLATE='utf8_unicode_ci' ENGINE=InnoDB;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_donation_categories` ( 
			`category_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
			`user_id` INT(10) UNSIGNED NOT NULL, 
			`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
			PRIMARY KEY (`category_id`) 
		) 
		COLLATE='utf8_unicode_ci' 
		ENGINE=InnoDB");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_donation_albums` ( 
			`album_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
			`donation_id` INT(11) UNSIGNED NOT NULL, 
			`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
			`description` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`creation_date` DATETIME NOT NULL, 
			`modified_date` DATETIME NOT NULL, 
			`search` TINYINT(1) NOT NULL DEFAULT '1', 
			`photo_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`view_count` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`comment_count` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`collectible_count` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			PRIMARY KEY (`album_id`), 
			INDEX `donation_id` (`donation_id`) 
		) 
		COLLATE='utf8_unicode_ci' 
		ENGINE=InnoDB;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_donation_fin_infos` ( 
			`fininfo_id` INT(11) NOT NULL AUTO_INCREMENT, 
			`donation_id` INT(11) NOT NULL DEFAULT '0', 
			`pemail` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`2email` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			PRIMARY KEY (`fininfo_id`) 
		) 
		COLLATE='utf8_unicode_ci' 
		ENGINE=InnoDB;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_donation_markers` ( 
			`marker_id` INT(11) NOT NULL AUTO_INCREMENT, 
			`donation_id` INT(11) NOT NULL DEFAULT '0', 
			`latitude` FLOAT(10,6) NOT NULL, 
			`longitude` FLOAT(10,6) NOT NULL, 
			PRIMARY KEY (`marker_id`) 
		) 
		COLLATE='utf8_unicode_ci' 
		ENGINE=InnoDB;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_donation_photos` ( 
			`photo_id` INT(11) NOT NULL AUTO_INCREMENT, 
			`album_id` INT(11) NOT NULL, 
			`donation_id` INT(11) NOT NULL, 
			`user_id` INT(11) NOT NULL, 
			`collection_id` INT(11) NOT NULL, 
			`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci', 
			`description` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
			`file_id` INT(11) NOT NULL, 
			`creation_date` DATETIME NOT NULL, 
			`modified_date` DATETIME NOT NULL, 
			PRIMARY KEY (`photo_id`) 
		) 
		COLLATE='utf8_unicode_ci' 
		ENGINE=InnoDB;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_donation_transactions` ( 
			`transaction_id` INT(11) NOT NULL AUTO_INCREMENT, 
			`order_id` INT(10) UNSIGNED NOT NULL DEFAULT '0', 
			`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0', 
			`name` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`email` VARCHAR(128) NULL DEFAULT NULL COLLATE 'ucs2_unicode_ci', 
			`item_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`item_type` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`state` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
			`gateway_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`gateway_transaction_id` VARCHAR(128) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci', 
			`amount` DOUBLE(62,6) NOT NULL DEFAULT '0.000000', 
			`currency` CHAR(3) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci', 
			`description` TEXT NULL COLLATE 'utf8_unicode_ci', 
			`creation_date` DATETIME NOT NULL, 
			PRIMARY KEY (`transaction_id`) 
		) 
		COLLATE='utf8_unicode_ci' 
		ENGINE=InnoDB;");

            $db->query("INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES 
			('donation_main', 'standard', 'Donation Main Navigation Menu'), 
			('donation_quick', 'standard', 'Donation Quick Navigation Menu');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
			('donation_main_browse_charity', 'donation', 'Charities', 'Donation_Plugin_Menus', '{\"route\":\"donation_charity_browse\"}', 'donation_main', '', 1), 
			('donation_main_browse_project', 'donation', 'Projects', 'Donation_Plugin_Menus', '{\"route\":\"donation_project_browse\"}', 'donation_main', '', 2), 
			('donation_main_browse_fundraise', 'donation', 'Fundraisings', 'Donation_Plugin_Menus', '{\"route\":\"donation_fundraise_browse\"}', 'donation_main', '', 3), 
			('donation_main_manage_donations', 'donation', 'My Donations', 'Donation_Plugin_Menus', '{\"route\":\"donation_manage_donations\"}', 'donation_main', '', 4), 
			('core_admin_main_plugins_donation', 'donation', 'HE - Donation', '', '{\"route\":\"admin_default\",\"module\":\"donation\", \"controller\":\"donations\"}', 'core_admin_main_plugins', '', 888), 
			('donation_admin_main_donations', 'donation', 'DONATION_Donations', '', '{\"route\":\"admin_default\",\"module\":\"donation\", \"controller\":\"donations\"}', 'donation_admin_main', '', 1), 
			('donation_profile_edit', 'donation', 'Donation Edit', 'Donation_Plugin_Menus', '', 'donation_profile', NULL, 1), 
			('donation_profile_delete', 'donation', 'Donation Delete', 'Donation_Plugin_Menus', '', 'donation_profile', '', 4), 
			('donation_profile_share', 'donation', 'Donation Share', 'Donation_Plugin_Menus', '', 'donation_profile', '', 2), 
			('donation_profile_promote', 'donation', 'Donation Promote', 'Donation_Plugin_Menus', '', 'donation_profile', NULL, 3), 
			('donation_profile_statistics', 'donation', 'DONATION_Profile_statistic', 'Donation_Plugin_Menus', '', 'donation_profile', '', 5), 
			('donation_profile_donation', 'donation', 'Back to Donations', 'Donation_Plugin_Menus', '', 'donation_profile', '', 999), 
			('donation_profile_fundraise', 'donation', 'Raise Money for Us', 'Donation_Plugin_Menus', '', 'donation_profile', '', 0), 
			('donation_profile_suggest', 'donation', 'Suggest To Friends', 'Donation_Plugin_Menus', '', 'donation_profile', NULL, 999), 
			('donation_quick_create_charity', 'donation', 'Create New Charity', 'Donation_Plugin_Menus', '{\"route\":\"donation_extended\",\"controller\":\"charity\",\"action\":\"create\",\"class\":\"buttonlink icon_donation_new\"}', 'donation_quick', NULL, 999),
			('donation_quick_create_project', 'donation', 'Create New Project', 'Donation_Plugin_Menus', '{\"route\":\"donation_extended\",\"controller\":\"project\",\"action\":\"create\",\"class\":\"buttonlink icon_donation_new\"}', 'donation_quick', NULL, 999),
			('donation_page_browse_charity', 'donation', 'Charity', 'Donation_Plugin_Menus', '', 'donation_page', NULL, 1), 
			('donation_page_browse_project', 'donation', 'Projects', 'Donation_Plugin_Menus', '', 'donation_page', NULL, 2), 
			('donation_admin_main_categories', 'donation', 'Categories', NULL, '{\"route\":\"admin_default\",\"module\":\"donation\",\"controller\":\"category\"}', 'donation_admin_main', NULL, 2), 
			('donation_admin_main_global', 'donation', 'DONATION_Global Settings', NULL, '{\"route\":\"admin_default\",\"module\":\"donation\",\"controller\":\"global\"}', 'donation_admin_main', NULL, 3), 
			('donation_admin_main_level', 'donation', 'DONATION_Level Settings', NULL, '{\"route\":\"admin_default\",\"module\":\"donation\",\"controller\":\"level\"}', 'donation_admin_main', NULL, 4), 
			('donation_profile_fininfo', 'donation', 'Edit Financial Information', 'Donation_Plugin_Menus', '', 'donation_profile', NULL, 6), 
			('donation_admin_main_statistics', 'donation', 'Statistics', NULL, '{\"route\":\"admin_default\",\"module\":\"donation\",\"controller\":\"statistics\"}', 'donation_admin_main', NULL, 3), 
			('core_main_donation', 'donation', 'Donations', '', '{\"route\":\"donation_charity_browse\"}', 'core_main', '', 999);");

            $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES 
			('donation_expired', 'donation', '[donation],[owner_name]'), 
			('donation_fundraise_expired', 'donation', '[fundraise],[owner_name]'), 
			('donation_child_fundraise_expired', 'donation', '[child_fundraise],[owner_name]'), 
			('donation_target', 'donation', '[donation],[owner_name]'), 
			('donation_fundraise_target', 'donation', '[fundraise],[owner_name]'), 
			('donation_child_fundraise_target', 'donation', '[child_fundraise],[owner_name]');");

            $db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('donation_index_browse', 'Browse Charities', NULL, 'Charities', 'This page displays a charity donations entry', '', 0, 0, '', '(NULL)', '(NULL)', 0);");
            $page_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'top', NULL, 1, '[\"[]\"]', NULL);");
            $top_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', NULL);");
            $main_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $top_id, 6, '[\"[]\"]', NULL);");
            $top_middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'right', $main_id, 5, '[\"[]\"]', NULL);");
            $main_right_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $main_id, 6, '[\"[]\"]', NULL);");
            $main_middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
			($page_id, 'widget', 'donation.browse-menu', $top_middle_id, 3, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'core.content', $main_middle_id, 6, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.donation-search', $main_right_id, 8, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.browse-menu-charity-quick', $main_right_id, 9, '[]', NULL);");

            $db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('donation_index_view', 'Donation Profile Page', NULL, 'Donation Profile', 'This is view pafe for donation.', '', 0, 0, '', NULL, NULL, 0);");
            $page_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', NULL);");
            $main_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $main_id, 6, '[\"[]\"]', NULL);");
            $middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'right', $main_id, 5, '[\"[]\"]', NULL);");
            $right_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'widget', 'core.container-tabs', $middle_id, 6, '{\"max\":6}', NULL);");
            $tab_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'widget', 'donation.profile-status', $right_id, 12, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.profile-options', $right_id, 13, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.profile-map', $right_id, 14, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.profile-supporters', $right_id, 15, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.profile-photo', $middle_id, 3, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'like.donation-status', $middle_id, 4, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.profile-description', $middle_id, 5, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.profile-photos', $tab_id, 8, '{\"title\":\"Photos\",\"titleCount\":true}', NULL), 
			($page_id, 'widget', 'core.comments', $tab_id, 7, '{\"title\":\"Comments\"}', NULL), 
			($page_id, 'widget', 'donation.profile-fundraisers', $middle_id, 9, '{\"titleCount\":true}', NULL), 
			($page_id, 'widget', 'donation.profile-donations', $middle_id, 10, '{\"titleCount\":true}', NULL);");

            $db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) 
			VALUES ('donation_donation_donate', 'Making Donation', '(NULL)', 'Making Donation', 'Making donation page', '', 0, 0, '', '(NULL)', '(NULL)', 0);");
            $page_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', NULL);");
            $main_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $main_id, 6, '[\"[]\"]', NULL);");
            $middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'widget', 'core.content', $middle_id, 3, '[]', NULL), 
			($page_id, 'container', 'right', $main_id, 5, '[\"[]\"]', NULL);");

            $db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) 
			VALUES ('donation_project_browse', 'Browse Projects', NULL, 'Projects', 'This page displays a project donations entry', '', 0, 0, '', '(NULL)', '(NULL)', 0);");
            $page_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', NULL);");
            $main_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'top', NULL, 1, '[\"[]\"]', NULL);");
            $top_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $main_id, 6, '[\"[]\"]', NULL);");
            $middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'right', $main_id, 5, '[\"[]\"]', NULL);");
            $right_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $top_id, 6, '[\"[]\"]', NULL);");
            $top_middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'widget', 'core.content', $middle_id, 6, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.browse-menu', $top_middle_id, 3, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.donation-search', $right_id, 8, '[\"[]\"]', NULL), 
			($page_id, 'widget', 'donation.browse-menu-project-quick', $right_id, 9, '[]', NULL);");

            $db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) 
			VALUES ('donation_fundraise_browse', 'Browse Fundraisers', NULL, 'Fundraising', 'This page displays a fundraise donations entry', '', 0, 0, '', '(NULL)', '(NULL)', 0);");
            $page_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'top', NULL, 1, '[]', NULL);");
            $top_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', NULL);");
            $main_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'right', $main_id, 5, '[]', NULL);");
            $right_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $main_id, 6, '[\"[]\"]', NULL);");
            $middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $top_id, 6, '[]', NULL);");
            $top_middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'widget', 'donation.browse-menu', $top_middle_id, 3, '[]', NULL), 
			($page_id, 'widget', 'core.content', $middle_id, 6, '[]', NULL), 
			($page_id, 'widget', 'donation.donation-search', $right_id, 8, '[]', NULL);");

            $db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) 
			VALUES ('donation_fundraise_view', 'Fundraising Profile Page', 'NULL', 'Fundraising Profile', 'This is view pafe for fundraising.', '', 1, 0, '', NULL, NULL, 0);");
            $page_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', '');");
            $main_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'right', $main_id, 5, '[\"[]\"]', NULL);");
            $right_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'container', 'middle', $main_id, 6, '[\"[]\"]', '');");
            $middle_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ($page_id, 'widget', 'core.container-tabs', $middle_id, 7, '{\"max\":6}', NULL);");
            $container_id = $db->lastInsertId();

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
		($page_id, 'widget', 'donation.profile-photo', $middle_id, 3, '[\"[]\"]', NULL), 
		($page_id, 'widget', 'like.donation-status', $middle_id, 4, '[\"[]\"]', NULL), 
		($page_id, 'widget', 'donation.parent-donation', $middle_id, 5, '[\"[]\"]', NULL), 
		($page_id, 'widget', 'donation.profile-description', $middle_id, 6, '[\"[]\"]', NULL), 
		($page_id, 'widget', 'core.comments', $container_id, 8, '{\"title\":\"Comments\"}', NULL), 
		($page_id, 'widget', 'donation.profile-photos', $container_id, 9, '{\"title\":\"Photos\",\"titleCount\":true}', NULL), 
		($page_id, 'widget', 'donation.profile-donations', $middle_id, 10, '{\"titleCount\":true}', NULL), 
		($page_id, 'widget', 'donation.profile-status', $right_id, 12, '[\"[]\"]', NULL), 
		($page_id, 'widget', 'donation.profile-options', $right_id, 13, '[\"[]\"]', NULL), 
		($page_id, 'widget', 'donation.profile-supporters', $right_id, 14, '[\"[]\"]', NULL);");

            $db->query("INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) 
			VALUES ('Donation Cleanup', 'donation', 'Donation_Plugin_Task_Cleanup', 60);");

            $sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'page' LIMIT 1 
CONTENT;

            if ($db->fetchOne($sql)) {
                $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `params`, `informed`) 
				VALUES ('donation', 'donation.page-profile-donations', '{\"title\":\"Donations\", \"titleCount\":true}', 1);");
            };

            $sql = <<<CONTENT
SELECT engine4_core_pages.page_id 
FROM engine4_core_pages 
WHERE engine4_core_pages.name = 'user_index_home' 
LIMIT 1;"); 
CONTENT;

            $db->query("INSERT IGNORE INTO `engine4_core_content` (`type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) 
			VALUES ('widget', 'donation.top-donors', 4, 17, '{\"title\":\"Top Donors\"}', NULL);");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			  level_id as `level_id`, 
			  'donation' as `type`, 
			  'create_charity' as `name`, 
			  1 as `value`, 
			  null as `params` 
			FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			  level_id as `level_id`, 
			  'donation' as `type`, 
			  'create_project' as `name`, 
			  1 as `value`, 
			  null as `params` 
			FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			  level_id as `level_id`, 
			  'donation' as `type`, 
			  'raise_money' as `name`, 
			  1 as `value`, 
			  null as `params` 
			FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			  level_id as `level_id`, 
			  'donation' as `type`, 
			  'comment' as `name`, 
			  1 as `value`, 
			  null as `params` 
			FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			  level_id as `level_id`, 
			  'donation' as `type`, 
			  'auth_view' as `name`, 
			  1 as `value`, 
			  null as `params` 
			FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			  level_id as `level_id`, 
			  'donation' as `type`, 
			  'auth_comment' as `name`, 
			  1 as `value`, 
			  null as `params` 
			FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			  level_id as `level_id`, 
			  'donation' as `type`, 
			  'delete' as `name`, 
			  1 as `value`, 
			  null as `params` 
			FROM `engine4_authorization_levels` WHERE `type` IN('admin');");

            $db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `displayable`) VALUES 
		('donation_project_new', 'donation', '{item:\$subject} posted a new project:', 7), 
		('donation_charity_new', 'donation', '{item:\$subject} posted a new charity:', 7), 
		('donation_fundraise_new', 'donation', '{item:\$subject}  is raising funds for {itemParent:\$object}:', 7), 
		('page_project_new', 'donation', '{actors:\$subject:\$object} posted a new project:', 7), 
		('page_charity_new', 'donation', '{actors:\$subject:\$object} posted a new charity:', 7);");

            $db->query("INSERT IGNORE INTO `engine4_donation_categories` (`user_id`, `title`) VALUES  
		(1, 'Animals'), 
		(1, 'Art'), 
		(1, 'Children'), 
		(1, 'Disability'), 
		(1, 'Education'), 
		(1, 'Environmental'), 
		(1, 'Healthcare'), 
		(1, 'Hospices'), 
		(1, 'Religion'), 
		(1, 'Sport'), 
		(1, 'Others');");

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } else { //$operation = upgrade|refresh
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}
