<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Installer extends Engine_Package_Installer_Module {

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

        $select = $db->select()
                ->from('engine4_hecore_modules')
                ->where('name = ?', 'rate');

        $rate = $db->fetchRow($select);

        if ($rate && version_compare($rate['version'], '4.1.5') < 0) {
            $error_message = $translate->_('We found that you have old version of Rate plugin, please download latest version of Rate Plugin and install.');
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

            $db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
		('store_product_new', 'store', '{item:\$subject} posted a new product:', 1, 5, 1, 3, 1, 1),
		('page_product_new', 'store', '{actors:\$subject:\$object} posted a new product:', 1, 7, 1, 3, 1 , 1),
		('comment_store_product', 'store', '{item:\$subject} commented on {item:\$owner}\'s {item:\$object:product}: {body:\$body}', 1, 1, 1, 1, 1, 0);");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`) VALUES
		(1, 'store_product', 'comment', 1),
		(2, 'store_product', 'comment', 1),
		(3, 'store_product', 'comment', 1),
		(4, 'store_product', 'comment', 1),
		(1, 'store_product', 'order', 1),
		(2, 'store_product', 'order', 1),
		(3, 'store_product', 'order', 1),
		(4, 'store_product', 'order', 1),
		(1, 'store', 'use', 1),
		(2, 'store', 'use', 1),
		(3, 'store', 'use', 1),
		(4, 'store', 'use', 1),
		(5, 'store', 'use', 1);");

            $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
		('store_transaction_pending', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[order_details],[transaction_details],[order_link]'),
		('store_transaction_overdue', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[order_details],[transaction_details],[order_link]'),
		('store_transaction_refunded', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[order_details],[transaction_details],[order_link]'),
		('store_transaction_success', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[order_details],[transaction_details],[order_link]'),
		('store_cart_complete', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[order_details],[order_link]'),
		('store_cart_pending', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[order_details],[order_link]'),
		('store_request_complete', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[request_details],[request_link]'),
		('store_request_pending', 'store', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[request_details],[request_link]');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
		('core_main_store', 'store', 'STORE_Stores', 'Store_Plugin_Menus', '{\"route\":\"store_general\"}', 'core_main', '', 1, 0, 999),
		('core_admin_main_plugins_store', 'store', 'HE - Store', '', '{\"route\":\"admin_default\",\"module\":\"store\", \"controller\":\"products\"}', 'core_admin_main_plugins', '', 1, 0, 888),
		('store_main_home', 'store', 'Store Home', 'Store_Plugin_Menus', '{\"route\":\"store_extended\"}', 'store_main', '', 1, 0, 1),
		('store_main_products', 'store', 'STORE_Browse Products', '', '{\"route\":\"store_general\",\"action\":\"products\"}', 'store_main', '', 1, 0, 2),
		('store_main_stores', 'store', 'STORE_Browse Stores', 'Store_Plugin_Menus', '{\"route\":\"store_general\",\"action\":\"stores\"}', 'store_main', '', 1, 0, 3),
		('store_main_faq', 'store', 'FAQ', '', '{\"route\":\"store_general\",\"action\":\"faq\"}', 'store_main', '', 1, 0, 4),
		('store_main_panel', 'store', 'STORE_My Panel', 'Store_Plugin_Menus', '{\"route\":\"store_panel\"}', 'store_main', '', 1, 0, 10),
		('store_main_cart', 'store', 'Cart', 'Store_Plugin_Menus', '{\"route\":\"store_extended\",\"controller\":\"cart\"}', 'store_main', '', 1, 0, 11),
		('store_admin_main_products', 'store', 'STORE_Products', '', '{\"route\":\"admin_default\",\"module\":\"store\", \"controller\":\"products\"}', 'store_admin_main', '', 1, 0, 1),
		('store_admin_main_stores', 'store', 'STORE_Stores', 'Store_Plugin_Menus', '{\"route\":\"admin_default\",\"module\":\"store\", \"controller\":\"stores\"}', 'store_admin_main', '', 1, 0, 2),
		('store_admin_main_orders', 'store', 'Orders', NULL, '{\"route\":\"admin_default\",\"module\":\"store\", \"controller\":\"orders\"}', 'store_admin_main', NULL, 1, 0, 3),
		('store_admin_main_transactions', 'store', 'Transactions', '', '{\"route\":\"admin_default\",\"module\":\"store\", \"controller\":\"transactions\"}', 'store_admin_main', '', 1, 0, 4),
		('store_admin_main_requests', 'store', 'Requests', 'Store_Plugin_Menus', '{\"route\":\"admin_default\",\"module\":\"store\", \"controller\":\"requests\"}', 'store_admin_main', NULL, 1, 0, 5),
		('store_admin_main_faq', 'store', 'FAQ', NULL, '{\"route\":\"admin_default\",\"module\":\"store\",\"controller\":\"faq\"}', 'store_admin_main', NULL, 1, 0, 6),
		('store_admin_main_settings', 'store', 'Settings', NULL, '{\"route\":\"admin_default\",\"module\":\"store\",\"controller\":\"settings\"}', 'store_admin_main', NULL, 1, 0, 7),
		('store_admin_main_fields', 'store', 'Fields', '', '{\"route\":\"admin_default\",\"module\":\"store\", \"controller\":\"fields\"}', 'store_admin_main', '', 1, 0, 8),
		('store_admin_main_gateways', 'store', 'Gateways', NULL, '{\"route\":\"admin_default\",\"module\":\"store\",\"controller\":\"gateway\"}', 'store_admin_main', NULL, 1, 0, 9),
		('store_admin_main_locations', 'store', 'Locations', NULL, '{\"route\":\"admin_default\",\"module\":\"store\",\"controller\":\"locations\"}', 'store_admin_main', NULL, 1, 0, 10),
		('store_admin_main_taxes', 'store', 'Tax', NULL, '{\"route\":\"admin_default\",\"module\":\"store\",\"controller\":\"taxes\"}', 'store_admin_main', NULL, 1, 0, 11),
		('store_admin_main_credit', 'store', 'Credit Settings', 'Store_Plugin_Menus', '{\"route\":\"admin_default\",\"module\":\"store\",\"controller\":\"credits\"}', 'store_admin_main', NULL, 1, 0, 12),
		('store_admin_main_statistics', 'store', 'Statistics', NULL, '{\"route\":\"admin_default\", \"module\":\"store\",\"controller\":\"statistics\",\"action\":\"chart\"}', 'store_admin_main', NULL, 1, 0, 13),
		('store_product_profile_edit', 'store', 'STORE_Edit Product', 'Store_Plugin_Menus', '', 'store_product_profile', '', 1, 0, 2),
		('store_product_profile_share', 'store', 'Share', 'Store_Plugin_Menus', '', 'store_product_profile', '', 1, 0, 3),
		('store_product_profile_delete', 'store', 'Delete Product', 'Store_Plugin_Menus', '', 'store_product_profile', '', 1, 0, 4),
		('store_product_profile_store', 'store', 'STORE_Back to Store', 'Store_Plugin_Menus', '', 'store_product_profile', '', 1, 0, 5),
		('storepage_all', 'store', 'Browse products', 'Store_Plugin_Menus', '', 'storepage', '', 1, 0, 1),
		('storepage_mine', 'store', 'My Products', 'Store_Plugin_Menus', '', 'storepage', '', 1, 0, 2),
		('storepage_create', 'store', 'Add Product', 'Store_Plugin_Menus', '', 'storepage', '', 1, 0, 3),
		('core_mini_cart', 'store', 'cart', 'Store_Plugin_Menus', '{\"route\":\"store_general\"}', 'core_mini', NULL, 1, 0, 999);");

            $db->query("INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
		('store_main', 'standard', 'Store Main Navigation Menu', 999),
		('store_product_profile', 'standard', 'Store Product Profile', 999);");

            $db->query("INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`) VALUES ('Discount Expiration Date', 'store', 'Store_Plugin_Task_Expiry', 3600);");



            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_apis` (
			`api_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`page_id` INT(10) UNSIGNED NOT NULL,
			`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			`gateway_id` INT(10) UNSIGNED NULL DEFAULT NULL,
			`email` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
			`config` MEDIUMBLOB NULL,
			`test_mode` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`api_id`),
			UNIQUE INDEX `page_id_gateway_id` (`page_id`, `gateway_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_audios` (
			`audio_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`product_id` INT(11) UNSIGNED NOT NULL,
			`file_id` INT(11) UNSIGNED NOT NULL,
			`title` VARCHAR(60) NOT NULL COLLATE 'utf8_unicode_ci',
			`play_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`audio_id`),
			INDEX `product_id` (`product_id`, `file_id`),
			INDEX `play_count` (`play_count`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_balances` (
			`balance_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`page_id` INT(10) UNSIGNED NOT NULL,
			`current_amt` DECIMAL(16,2) UNSIGNED NOT NULL DEFAULT '0.00',
			`pending_amt` DECIMAL(16,2) UNSIGNED NOT NULL DEFAULT '0.00',
			`requested_amt` DECIMAL(16,2) UNSIGNED NOT NULL DEFAULT '0.00',
			`transferred_amt` DECIMAL(16,2) UNSIGNED NOT NULL DEFAULT '0.00',
			`transferred_date` DATETIME NULL DEFAULT NULL,
			`requested_date` DATETIME NULL DEFAULT NULL,
			PRIMARY KEY (`balance_id`),
			UNIQUE INDEX `page_id` (`page_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_cartitems` (
			`cartitem_id` INT(11) NOT NULL AUTO_INCREMENT,
			`cart_id` INT(11) NOT NULL,
			`product_id` INT(11) NOT NULL,
			`qty` INT(11) UNSIGNED NOT NULL DEFAULT '1',
			`params` TINYTEXT NULL COLLATE 'utf8_unicode_ci',
			`creation_date` DATETIME NOT NULL,
			`shipping` TINYINT(1) NOT NULL DEFAULT '1',
			`active` TINYINT(1) NOT NULL DEFAULT '1',
			PRIMARY KEY (`cartitem_id`),
			INDEX `product_id` (`product_id`),
			INDEX `basket_id` (`cart_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_carts` (
			`cart_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`user_id` INT(11) UNSIGNED NOT NULL,
			`order_id` INT(11) UNSIGNED NULL DEFAULT NULL,
			`active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			PRIMARY KEY (`cart_id`),
			INDEX `user_id` (`user_id`),
			INDEX `order_id` (`order_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_details` (
			`detail_id` INT(10) NOT NULL AUTO_INCREMENT,
			`user_id` INT(10) NOT NULL,
			`first_name` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
			`last_name` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
			`middle_name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`email` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
			`phone` VARCHAR(20) NOT NULL COLLATE 'utf8_unicode_ci',
			`phone_extension` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`location_id_1` INT(11) NOT NULL,
			`location_id_2` INT(11) NOT NULL DEFAULT '0',
			`zip` VARCHAR(11) NOT NULL COLLATE 'utf8_unicode_ci',
			`city` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
			`address_line_1` TINYTEXT NOT NULL COLLATE 'utf8_unicode_ci',
			`address_line_2` TINYTEXT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`detail_id`),
			UNIQUE INDEX `user_id` (`user_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_faq` (
			`faq_id` INT(10) NOT NULL AUTO_INCREMENT,
			`question` TEXT NULL COLLATE 'utf8_unicode_ci',
			`answer` TEXT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`faq_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_gateways` (
			`gateway_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
			`description` TEXT NULL COLLATE 'utf8_unicode_ci',
			`button_url` TINYTEXT NULL COLLATE 'utf8_unicode_ci',
			`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			`plugin` VARCHAR(128) NOT NULL COLLATE 'latin1_general_ci',
			`email` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
			`config` MEDIUMBLOB NULL,
			`test_mode` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`gateway_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_locations` (
			`location_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`location` TINYTEXT NOT NULL COLLATE 'utf8_unicode_ci',
			`location_code` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`shipping_amt` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL,
			`shipping_days` INT(10) UNSIGNED NOT NULL DEFAULT '7',
			PRIMARY KEY (`location_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_locationships` (
			`locationship_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`page_id` INT(10) UNSIGNED NOT NULL,
			`location_id` INT(10) UNSIGNED NOT NULL,
			`shipping_amt` DECIMAL(16,2) UNSIGNED NULL DEFAULT NULL,
			`shipping_days` INT(10) UNSIGNED NOT NULL DEFAULT '7',
			`creation_date` DATETIME NOT NULL,
			PRIMARY KEY (`locationship_id`),
			UNIQUE INDEX `page_id_location_id` (`page_id`, `location_id`),
			INDEX `location_id` (`location_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_orderitems` (
			`orderitem_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`page_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`order_id` INT(20) NOT NULL,
			`item_id` INT(11) UNSIGNED NOT NULL,
			`item_type` ENUM('store_product') NOT NULL,
			`name` VARCHAR(128) NOT NULL,
			`params` TINYTEXT NULL,
			`qty` INT(11) NOT NULL DEFAULT '1',
			`item_amt` DECIMAL(16,2) NOT NULL,
			`tax_amt` DECIMAL(16,2) NOT NULL DEFAULT '0.00',
			`shipping_amt` DECIMAL(16,2) NOT NULL DEFAULT '0.00',
			`commission_amt` DECIMAL(16,2) NOT NULL DEFAULT '0.00',
			`total_amt` DECIMAL(16,2) NOT NULL,
			`status` ENUM('initial','processing','shipping','delivered','cancelled','completed') NOT NULL DEFAULT 'initial',
			`currency` VARCHAR(3) NOT NULL,
			`refund_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			`via_credits` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			`download_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`update_date` DATETIME NOT NULL,
			`gateway_transaction_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`orderitem_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_orders` (
			`order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`user_id` INT(11) UNSIGNED NOT NULL,
			`status` ENUM('initial','processing','shipping','delivered','cancelled','completed') NOT NULL DEFAULT 'initial' COLLATE 'utf8_unicode_ci',
			`gateway_id` INT(11) UNSIGNED NOT NULL,
			`gateway_transaction_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`gateway_order_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`ukey` CHAR(10) NOT NULL COLLATE 'utf8_unicode_ci',
			`creation_date` DATETIME NOT NULL,
			`payment_date` DATETIME NOT NULL,
			`item_type` ENUM('store_cart','store_request') NOT NULL COLLATE 'utf8_unicode_ci',
			`item_id` INT(10) UNSIGNED NOT NULL,
			`item_amt` DECIMAL(16,2) UNSIGNED NOT NULL,
			`tax_amt` DECIMAL(16,2) UNSIGNED NOT NULL DEFAULT '0.00',
			`shipping_amt` DECIMAL(16,2) UNSIGNED NOT NULL DEFAULT '0.00',
			`commission_amt` DECIMAL(16,2) UNSIGNED NULL DEFAULT NULL,
			`total_amt` DECIMAL(16,2) UNSIGNED NOT NULL,
			`currency` CHAR(3) NOT NULL COLLATE 'utf8_unicode_ci',
			`shipping_details` TEXT NULL COLLATE 'utf8_unicode_ci',
			`offer_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`via_credits` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`order_id`),
			UNIQUE INDEX `ukey` (`ukey`),
			UNIQUE INDEX `item_type_item_id` (`item_type`, `item_id`),
			INDEX `user_id` (`user_id`),
			INDEX `gateway_id` (`gateway_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_photos` (
			`photo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`user_id` INT(11) UNSIGNED NOT NULL,
			`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
			`description` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
			`collection_id` INT(11) UNSIGNED NOT NULL,
			`file_id` INT(11) UNSIGNED NOT NULL,
			`creation_date` DATETIME NOT NULL,
			`modified_date` DATETIME NOT NULL,
			PRIMARY KEY (`photo_id`),
			INDEX `user_id` (`user_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_products` (
			`product_id` INT(11) NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(128) NOT NULL COLLATE 'utf8_unicode_ci',
			`description` TEXT NULL COLLATE 'utf8_unicode_ci',
			`params` TINYTEXT NULL COLLATE 'utf8_unicode_ci',
			`type` ENUM('simple','digital') NULL DEFAULT 'simple' COLLATE 'utf8_unicode_ci',
			`price_type` ENUM('simple','discount') NULL DEFAULT 'simple' COLLATE 'utf8_unicode_ci',
			`discount_expiry_date` DATETIME NULL DEFAULT NULL,
			`price` DECIMAL(16,2) NOT NULL DEFAULT '0.00',
			`list_price` DECIMAL(16,2) NOT NULL DEFAULT '0.00',
			`tax_id` INT(11) NOT NULL DEFAULT '0',
			`owner_id` INT(11) NOT NULL,
			`page_id` INT(11) NOT NULL,
			`creation_date` DATETIME NOT NULL,
			`modified_date` DATETIME NOT NULL,
			`photo_id` INT(11) NOT NULL DEFAULT '0',
			`quantity` INT(11) UNSIGNED NOT NULL DEFAULT '1',
			`sell_count` INT(11) NOT NULL DEFAULT '0',
			`view_count` INT(11) NOT NULL DEFAULT '0',
			`comment_count` INT(11) NOT NULL DEFAULT '0',
			`search` TINYINT(1) NOT NULL DEFAULT '1',
			`sponsored` TINYINT(1) NOT NULL DEFAULT '0',
			`featured` TINYINT(1) NOT NULL DEFAULT '0',
			`via_credits` TINYINT(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`product_id`),
			INDEX `tax_id` (`tax_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_productships` (
			`productship_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`product_id` INT(10) UNSIGNED NOT NULL,
			`location_id` INT(10) UNSIGNED NOT NULL,
			`shipping_amt` DECIMAL(16,2) UNSIGNED NULL DEFAULT NULL,
			`shipping_days` INT(10) UNSIGNED NOT NULL DEFAULT '7',
			`creation_date` DATETIME NOT NULL,
			PRIMARY KEY (`productship_id`),
			UNIQUE INDEX `product_id_location_id` (`product_id`, `location_id`),
			INDEX `location_id` (`location_id`),
			INDEX `product_id` (`product_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_product_fields_maps` (
			`field_id` INT(11) UNSIGNED NOT NULL,
			`option_id` INT(11) UNSIGNED NOT NULL,
			`child_id` INT(11) UNSIGNED NOT NULL,
			`order` SMALLINT(6) NOT NULL,
			PRIMARY KEY (`field_id`, `option_id`, `child_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_product_fields_meta` (
			`field_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`type` VARCHAR(24) NOT NULL COLLATE 'utf8_unicode_ci',
			`label` VARCHAR(64) NOT NULL COLLATE 'utf8_unicode_ci',
			`description` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
			`alias` VARCHAR(32) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
			`required` TINYINT(1) NOT NULL DEFAULT '0',
			`display` TINYINT(1) UNSIGNED NOT NULL,
			`publish` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			`search` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			`order` SMALLINT(3) UNSIGNED NOT NULL DEFAULT '999',
			`config` TEXT NULL COLLATE 'utf8_unicode_ci',
			`validators` TEXT NULL COLLATE 'utf8_unicode_ci',
			`filters` TEXT NULL COLLATE 'utf8_unicode_ci',
			`style` TEXT NULL COLLATE 'utf8_unicode_ci',
			`error` TEXT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`field_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_product_fields_options` (
			`option_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`field_id` INT(11) UNSIGNED NOT NULL,
			`label` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
			`order` SMALLINT(6) NOT NULL DEFAULT '999',
			PRIMARY KEY (`option_id`),
			INDEX `field_id` (`field_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_product_fields_search` (
			`item_id` INT(11) UNSIGNED NOT NULL,
			`profile_type` ENUM('1','6') NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`field_10` ENUM('12','16','10','11','17','15','21') NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`field_2` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`field_4` ENUM('7','8','16') NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`item_id`),
			INDEX `field_10` (`field_10`),
			INDEX `field_2` (`field_2`),
			INDEX `profile_type` (`profile_type`),
			INDEX `field_4` (`field_4`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_product_fields_values` (
			`item_id` INT(11) UNSIGNED NOT NULL,
			`field_id` INT(11) UNSIGNED NOT NULL,
			`index` SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
			`value` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`item_id`, `field_id`, `index`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_requests` (
			`request_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`page_id` INT(10) UNSIGNED NOT NULL,
			`amt` DECIMAL(16,2) UNSIGNED NOT NULL,
			`status` ENUM('cancelled','completed','denied','pending','waiting') NOT NULL DEFAULT 'waiting',
			`request_message` TINYTEXT NULL,
			`response_message` TINYTEXT NULL,
			`request_date` DATETIME NOT NULL,
			`response_date` DATETIME NULL DEFAULT NULL,
			PRIMARY KEY (`request_id`),
			INDEX `page_id` (`page_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_taxes` (
			`tax_id` INT(10) NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`percent` DECIMAL(5,2) UNSIGNED ZEROFILL NOT NULL DEFAULT '000.00',
			PRIMARY KEY (`tax_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_transactions` (
			`transaction_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`item_id` INT(10) UNSIGNED NOT NULL,
			`item_type` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
			`order_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`timestamp` DATETIME NOT NULL,
			`state` VARCHAR(64) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
			`gateway_id` INT(10) UNSIGNED NOT NULL,
			`gateway_transaction_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'latin1_general_ci',
			`gateway_order_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`type` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
			`gateway_fee` DECIMAL(16,2) NULL DEFAULT NULL,
			`amt` DECIMAL(16,2) NOT NULL,
			`currency` VARCHAR(3) NOT NULL DEFAULT '' COLLATE 'latin1_general_ci',
			`via_credits` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`transaction_id`),
			UNIQUE INDEX `gateway_transaction_id` (`gateway_transaction_id`),
			INDEX `item_id` (`item_id`),
			INDEX `order_id` (`order_id`),
			INDEX `user_id` (`user_id`),
			INDEX `gateway_id` (`gateway_id`),
			INDEX `state` (`state`(1))
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_transactiontypes` (
			`type` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',
			`plugin` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`type`),
			UNIQUE INDEX `type` (`type`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_videos` (
			`video_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(100) NOT NULL COLLATE 'utf8_unicode_ci',
			`description` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
			`owner_id` INT(11) NOT NULL,
			`product_id` INT(11) UNSIGNED NULL DEFAULT NULL,
			`creation_date` DATETIME NOT NULL,
			`modified_date` DATETIME NOT NULL,
			`status` TINYINT(1) NOT NULL,
			`type` TINYINT(1) NOT NULL,
			`code` VARCHAR(150) NOT NULL COLLATE 'utf8_unicode_ci',
			`url` VARCHAR(200) NOT NULL COLLATE 'utf8_unicode_ci',
			`photo_id` INT(11) UNSIGNED NULL DEFAULT NULL,
			`duration` INT(9) UNSIGNED NOT NULL,
			PRIMARY KEY (`video_id`),
			INDEX `owner_id` (`owner_id`),
			INDEX `creation_date` (`creation_date`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_store_wishes` (
			`wish_id` INT(10) NOT NULL AUTO_INCREMENT,
			`user_id` INT(11) NOT NULL,
			`product_id` INT(11) NOT NULL,
			PRIMARY KEY (`wish_id`),
			INDEX `user_id` (`user_id`),
			INDEX `product_id` (`product_id`)
		) ENGINE=InnoDB CHARSET=utf8 COLLATE='utf8_unicode_ci';");



            $db->query("INSERT IGNORE INTO `engine4_store_faq` (`faq_id`, `question`, `answer`) VALUES
		(1, 'How much will it cost to ship my order?', '<p>Shipping rates will be calculated separately for each address. This rate is shown on your cart and added to the total order price.</p>'),
		(2, 'Can orders be shipped internationally?', '<p>Yes, we provide shippment to international addresses. Please just go head and add products to your cart, if your location is not supported then you will be notified before ordering.</p> 
		<p>If you location is not supported then you can contact the store owner for more options or leave your feedback which will be certainly considered.</p>'),
		(3, 'What forms of payment are accepted for online purchase?', '<p>We accept PayPal, credit and debit cards like: Visa, MasterCard, and American Express.</p>'),
		(4, 'Is it safe for me to pay by credit cards?', '<p>Yes, all payments are handled on a secure server of payment gateways and your finance information is not filled and stored on this site.</p>'),
		(5, 'I\'d like to sell my products on your site. How do I open my store and sell products?', '<p>Yes, it is possible. Please go ahead and create your official page on our site and just start adding your products. Your products will be automatically displayed under Stores section.</p>'),
		(6, 'How do payments go from customers to my store?', '<p>Your sales amount are sent to site and reflected on your account balance. You can control your account balance and withdraw funds to your finance account(PayPal and 2Checkout are supported) from your store panel.</p>'),
		(7, 'How I can withdraw funds from my store?', '<p>You can sent withdrawal request from your store panel if all the following requirements are met:</p> 
		<p>- you have exceeded the minimal withdrawal fund</p> 
		<p>- you have provided the finance account information to which we can send your funds. Right now we support PayPal and 2Checkout finance accounts</p> 
		<p>&nbsp;</p> 
		<p>You are notified as soon as your withdrawal request is accepted and completed.</p>');");

            $db->query("INSERT IGNORE INTO `engine4_store_gateways` (`gateway_id`, `title`, `description`, `button_url`, `enabled`, `plugin`) VALUES
		(1, '2Checkout', '2Checkout', '', 0, 'Store_Plugin_Gateway_2Checkout'),
		(2, 'PayPal', 'PayPal', '', 0, 'Store_Plugin_Gateway_PayPal'),
		(3, 'Credit', 'HE Credit Plugin', '', 1, 'Store_Plugin_Gateway_Credit');");

            $db->query("INSERT IGNORE INTO `engine4_store_locations` (`location_id`, `location`, `location_code`, `parent_id`, `shipping_amt`, `shipping_days`) VALUES
		(1, 'United States', 'US', 0, 5.00, 1),
		(2, 'Alabama', 'AL', 1, 5.00, 1),
		(3, 'Alaska', 'AK', 1, 5.00, 1),
		(4, 'American', 'AS', 1, 5.00, 1),
		(5, 'Arizona', 'AZ', 1, 5.00, 1),
		(6, 'Arkansas', 'AR', 1, 5.00, 1),
		(7, 'California', 'CA', 1, 5.00, 1),
		(8, 'Colorado', 'CO', 1, 5.00, 1),
		(9, 'Connecticut', 'CT', 1, 5.00, 1),
		(10, 'Delaware', 'DE', 1, 5.00, 1),
		(11, 'Dist. of Columbia', 'DC', 1, 5.00, 1),
		(12, 'Florida', 'FL', 1, 5.00, 1),
		(13, 'Georgia', 'GA', 1, 5.00, 1),
		(14, 'Guam', 'GU', 1, 5.00, 1),
		(15, 'Hawaii', 'HI', 1, 5.00, 1),
		(16, 'Idaho', 'ID', 1, 5.00, 1),
		(17, 'Illinois', 'IL', 1, 5.00, 1),
		(18, 'Indiana', 'IN', 1, 5.00, 1),
		(19, 'Iowa', 'IA', 1, 5.00, 1),
		(20, 'Kansas', 'KS', 1, 5.00, 1),
		(21, 'Kentucky', 'KY', 1, 5.00, 1),
		(22, 'Louisiana', 'LA', 1, 5.00, 1),
		(23, 'Maine', 'ME', 1, 5.00, 1),
		(24, 'Maryland', 'MD', 1, 5.00, 1),
		(25, 'Marshall', 'MH', 1, 5.00, 1),
		(26, 'Massachusetts', 'MA', 1, 5.00, 1),
		(27, 'Michigan', 'MI', 1, 5.00, 1),
		(28, 'Micronesia', 'FM', 1, 5.00, 1),
		(29, 'Minnesota', 'MN', 1, 5.00, 1),
		(30, 'Mississippi', 'MS', 1, 5.00, 1),
		(31, 'Missouri', 'MO', 1, 5.00, 1),
		(32, 'Montana', 'MT', 1, 5.00, 1),
		(33, 'Nebraska', 'NE', 1, 5.00, 1),
		(34, 'Nevada', 'NV', 1, 5.00, 1),
		(35, 'New Hampshire', 'NH', 1, 5.00, 1),
		(36, 'New Jersey', 'NJ', 1, 5.00, 1),
		(37, 'New Mexico', 'NM', 1, 5.00, 1),
		(38, 'New York', 'NY', 1, 5.00, 1),
		(39, 'North Carolina', 'NC', 1, 5.00, 1),
		(40, 'North Dakota', 'ND', 1, 5.00, 1),
		(41, 'Northern', 'MP', 1, 5.00, 1),
		(42, 'Ohio', 'OH', 1, 5.00, 1),
		(43, 'Oklahoma', 'OK', 1, 5.00, 1),
		(44, 'Oregon', 'OR', 1, 5.00, 1),
		(45, 'Palau', 'PW', 1, 5.00, 1),
		(46, 'Pennsylvania', 'PA', 1, 5.00, 1),
		(47, 'Puerto Rico', 'PR', 1, 5.00, 1),
		(48, 'Rhode Island', 'RI', 1, 5.00, 1),
		(49, 'South Carolina', 'SC', 1, 5.00, 1),
		(50, 'South Dakota', 'SD', 1, 5.00, 1),
		(51, 'Tennessee', 'TN', 1, 5.00, 1),
		(52, 'Texas', 'TX', 1, 5.00, 1),
		(53, 'Utah', 'UT', 1, 5.00, 1),
		(54, 'Vermont', 'VT', 1, 5.00, 1),
		(55, 'Virginia', 'VA', 1, 5.00, 1),
		(56, 'Virgin Islands', 'VI', 1, 5.00, 1),
		(57, 'Washington', 'WA', 1, 5.00, 1),
		(58, 'West Virginia', 'WV', 1, 5.00, 1),
		(59, 'Wisconsin', 'WI', 1, 5.00, 1),
		(60, 'Wyoming', 'WY', 1, 5.00, 1);");

            $db->query("INSERT IGNORE INTO `engine4_store_locations` (`location`, `location_code`) VALUES 
		('Aland Islands ', 'AX'),
		('Albania', 'AL'),
		('Algeria', 'DZ'),
		('American Samoa', 'AS'),
		('Andorra', 'AD'),
		('Anguilla', 'AI'),
		('Antarctica ', 'AQ'),
		('Antigua And Barbuda ', 'AG'),
		('Argentina', 'AR'),
		('Armenia', 'AM'),
		('Aruba', 'AW'),
		('Australia ', 'AU'),
		('Austria', 'AT'),
		('Azerbaijan', 'AZ'),
		('Bahamas', 'BS'),
		('Bahrain', 'BH'),
		('Bangladesh', 'BD'),
		('Barbados', 'BB'),
		('Belgium', 'BE'),
		('Belize', 'BZ'),
		('Benin', 'BJ'),
		('Bermuda', 'BM'),
		('Bhutan', 'BT'),
		('Bosnia-Herzegovina', 'BA'),
		('Botswana', 'BW'),
		('Bouvet Island', 'BV'),
		('Brazil', 'BR'),
		('British Indian Ocean Territory', 'IO'),
		('Brunei Darussalam ', 'BN'),
		('Bulgaria', 'BG'),
		('Burkina Faso', 'BF'),
		('Canada', 'CA'),
		('Cape Verde ', 'CV'),
		('Cayman Islands ', 'KY'),
		('Central African Republic', 'CF'),
		('Chile', 'CL'),
		('China', 'CN'),
		('Christmas Island', 'CX '),
		('Cocos (Keeling) Islands ', 'CC'),
		('Colombia', 'CO'),
		('Cook Islands ', 'CK'),
		('Costa Rica ', 'CR'),
		('Cyprus', 'CY'),
		('Czech Republic ', 'CZ'),
		('Denmark', 'DK'),
		('Djibouti', 'DJ'),
		('Dominica', 'DM'),
		('Dominican Republic ', 'DO'),
		('Ecuador', 'EC'),
		('Egypt', 'EG'),
		('El Salvador', 'SV'),
		('Estonia', 'EE'),
		('Falkland Islands (Malvinas)', 'FK'),
		('Faroe Islands', 'FO'),
		('Fiji', 'FJ'),
		('Finland', 'FI'),
		('France', 'FR'),
		('French Guiana ', 'GF'),
		('French Polynesia', 'PF'),
		('French Southern Territories', 'TF'),
		('Gabon', 'GA'),
		('Gambia', 'GM'),
		('Georgia', 'GE'),
		('Germany', 'DE'),
		('Ghana', 'GH'),
		('Gibraltar', 'GI'),
		('Greece', 'GR'),
		('Greenland', 'GL'),
		('Grenada', 'GD'),
		('Guadeloupe', 'GP'),
		('Guam', 'GU'),
		('Guernsey', 'GG'),
		('Guyana', 'GY'),
		('Heard Island And Mcdonald Islands', 'HM'),
		('Holy See (Vatican City State)', 'VA'),
		('Honduras', 'HN'),
		('Hong Kong', 'HK'),
		('Hungary', 'HU'),
		('Iceland', 'IS'),
		('India', 'IN'),
		('Indonesia', 'ID'),
		('Ireland', 'IE'),
		('Isle Of Man', 'IM'),
		('Israel', 'IL'),
		('Italy', 'IT'),
		('Jamaica', 'JM'),
		('Japan', 'JP'),
		('Jersey', 'JE'),
		('Jordan', 'JO'),
		('Kazakhstan', 'KZ'),
		('Kiribati', 'KI'),
		('Korea, Republic Of', 'KR'),
		('Kuwait', 'KW'),
		('Kyrgyzstan', 'KG'),
		('Latvia', 'LV'),
		('Lesotho', 'LS'),
		('Liechtenstein', 'LI'),
		('Lithuania', 'LT'),
		('Luxembourg', 'LU'),
		('Macao', 'MO'),
		('Macedonia', 'MK'),
		('Madagascar', 'MG'),
		('Malawi', 'MW'),
		('Malaysia', 'MY'),
		('Malta', 'MT'),
		('Marshall Islands ', 'MH'),
		('Martinique', 'MQ'),
		('Mauritania', 'MR'),
		('Mauritius', 'MU'),
		('Mayotte', 'YT'),
		('Mexico', 'MX'),
		('Micronesia, Federated States Of', 'FM'),
		('Moldova, Republic Of ', 'MD'),
		('Monaco', 'MC'),
		('Mongolia', 'MN'),
		('Montenegro', 'ME'),
		('Montserrat', 'MS'),
		('Morocco', 'MA'),
		('Mozambique', 'MZ'),
		('Namibia', 'NA'),
		('Nauru', 'NR'),
		('Nepal', 'NP'),
		('Netherlands', 'NL'),
		('Netherlands Antilles', 'AN'),
		('New Caledonia', 'NC'),
		('New Zealand', 'NZ'),
		('Nicaragua', 'NI'),
		('Niger', 'NE'),
		('Niue', 'NU'),
		('Norfolk Island', 'NF'),
		('Northern Mariana Islands ', 'MP'),
		('Norway', 'NO'),
		('Oman', 'OM'),
		('Palau', 'PW'),
		('Palestine', 'PS'),
		('Panama', 'PA'),
		('Paraguay', 'PY'),
		('Peru', 'PE'),
		('Philippines', 'PH'),
		('Pitcairn', 'PN'),
		('Poland', 'PL'),
		('Portugal', 'PT'),
		('Puerto Rico ', 'PR'),
		('Qatar', 'QA'),
		('Reunion', 'RE'),
		('Romania', 'RO'),
		('Russian Federation', 'RU'),
		('Rwanda', 'RW'),
		('Saint Helena', 'SH'),
		('Saint Kitts And Nevis', 'KN'),
		('Saint Lucia', 'LC'),
		('Saint Pierre And Miquelon ', 'PM'),
		('Saint Vincent And The Grenadines', 'VC'),
		('Samoa', 'WS'),
		('San Marino', 'SM'),
		('Sao Tome And Principe', 'ST'),
		('Saudi Arabia', 'SA'),
		('Senegal', 'SN'),
		('Serbia', 'RS'),
		('Seychelles', 'SC'),
		('Singapore', 'SG'),
		('Slovakia', 'SK'),
		('Slovenia', 'SI'),
		('Solomon Islands', 'SB'),
		('South Africa', 'ZA'),
		('South Georgia And The South Sandwich Islands', 'GS'),
		('Spain', 'ES'),
		('Suriname', 'SR'),
		('Svalbard And Jan Mayen ', 'SJ'),
		('Swaziland', 'SZ'),
		('Sweden', 'SE'),
		('Switzerland', 'CH'),
		('Taiwan, Province Of China', 'TW'),
		('Tanzania, United Republic Of', 'TZ'),
		('Thailand', 'TH'),
		('Timor-Leste', 'TL'),
		('Togo', 'TG'),
		('Tokelau', 'TK'),
		('Tonga', 'TO'),
		('Trinidad And Tobago', 'TT'),
		('Tunisia', 'TN'),
		('Turkey', 'TR'),
		('Turkmenistan', 'TM'),
		('Turks And Caicos Islands', 'TC'),
		('Tuvalu', 'TV'),
		('Uganda', 'UG'),
		('Ukraine', 'UA'),
		('United Arab Emirates', 'AE'),
		('United Kingdom ', 'GB'),
		('United States Minor Outlying Islands', 'UM'),
		('Uruguay', 'UY'),
		('Uzbekistan', 'UZ'),
		('Vanuatu', 'VU'),
		('Venezuela', 'VE'),
		('Viet Nam', 'VN'),
		('Virgin Islands, British', 'VG'),
		('Virgin Islands, U.S. ', 'VI'),
		('Wallis And Futuna', 'WF'),
		('Western Sahara', 'EH'),
		('Zambia', 'ZM');");

            $db->query("INSERT IGNORE INTO `engine4_store_locationships` (`locationship_id`, `page_id`, `location_id`, `shipping_amt`, `shipping_days`, `creation_date`) VALUES
		(1, 0, 1, 5.00, 1, NOW()),
		(2, 0, 2, 5.00, 1, NOW()),
		(3, 0, 3, 5.00, 1, NOW()),
		(4, 0, 4, 5.00, 1, NOW()),
		(5, 0, 5, 5.00, 1, NOW()),
		(6, 0, 6, 5.00, 1, NOW()),
		(7, 0, 7, 5.00, 1, NOW()),
		(8, 0, 8, 5.00, 1, NOW()),
		(9, 0, 9, 5.00, 1, NOW()),
		(10, 0, 10, 5.00, 1, NOW()),
		(11, 0, 11, 5.00, 1, NOW()),
		(12, 0, 12, 5.00, 1, NOW()),
		(13, 0, 13, 5.00, 1, NOW()),
		(14, 0, 14, 5.00, 1, NOW()),
		(15, 0, 15, 5.00, 1, NOW()),
		(16, 0, 16, 5.00, 1, NOW()),
		(17, 0, 17, 5.00, 1, NOW()),
		(18, 0, 18, 5.00, 1, NOW()),
		(19, 0, 19, 5.00, 1, NOW()),
		(20, 0, 20, 5.00, 1, NOW()),
		(21, 0, 21, 5.00, 1, NOW()),
		(22, 0, 22, 5.00, 1, NOW()),
		(23, 0, 23, 5.00, 1, NOW()),
		(24, 0, 24, 5.00, 1, NOW()),
		(25, 0, 25, 5.00, 1, NOW()),
		(26, 0, 26, 5.00, 1, NOW()),
		(27, 0, 27, 5.00, 1, NOW()),
		(28, 0, 28, 5.00, 1, NOW()),
		(29, 0, 29, 5.00, 1, NOW()),
		(30, 0, 30, 5.00, 1, NOW()),
		(31, 0, 31, 5.00, 1, NOW()),
		(32, 0, 32, 5.00, 1, NOW()),
		(33, 0, 33, 5.00, 1, NOW()),
		(34, 0, 34, 5.00, 1, NOW()),
		(35, 0, 35, 5.00, 1, NOW()),
		(36, 0, 36, 5.00, 1, NOW()),
		(37, 0, 37, 5.00, 1, NOW()),
		(38, 0, 38, 5.00, 1, NOW()),
		(39, 0, 39, 5.00, 1, NOW()),
		(40, 0, 40, 5.00, 1, NOW()),
		(41, 0, 41, 5.00, 1, NOW()),
		(42, 0, 42, 5.00, 1, NOW()),
		(43, 0, 43, 5.00, 1, NOW()),
		(44, 0, 44, 5.00, 1, NOW()),
		(45, 0, 45, 5.00, 1, NOW()),
		(46, 0, 46, 5.00, 1, NOW()),
		(47, 0, 47, 5.00, 1, NOW()),
		(48, 0, 48, 5.00, 1, NOW()),
		(49, 0, 49, 5.00, 1, NOW()),
		(50, 0, 50, 5.00, 1, NOW()),
		(51, 0, 51, 5.00, 1, NOW()),
		(52, 0, 52, 5.00, 1, NOW()),
		(53, 0, 53, 5.00, 1, NOW()),
		(54, 0, 54, 5.00, 1, NOW()),
		(55, 0, 55, 5.00, 1, NOW()),
		(56, 0, 56, 5.00, 1, NOW()),
		(57, 0, 57, 5.00, 1, NOW()),
		(58, 0, 58, 5.00, 1, NOW()),
		(59, 0, 59, 5.00, 1, NOW()),
		(60, 0, 60, 5.00, 1, NOW());");

            $db->query("INSERT IGNORE INTO `engine4_store_product_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
		(0, 0, 1, 1),
		(1, 2, 2, 1);");

            $db->query("INSERT IGNORE INTO `engine4_store_product_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
		(1, 'profile_type', 'Category', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL),
		(2, 'select', 'Type', '', '', 1, 0, 0, 1, 999, '{\"show\":\"1\"}', NULL, NULL, '', '');");

            $db->query("INSERT IGNORE INTO `engine4_store_product_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
		(1, 1, 'Other', 999),
		(2, 1, 'Electronics', 999),
		(3, 2, 'Computers', 1),
		(4, 2, 'Cameras', 2);");

            $db->query("INSERT IGNORE INTO `engine4_store_taxes` (`tax_id`, `title`, `percent`) VALUES
		(1, 'Tax', 010.00);");

            $db->query("INSERT IGNORE INTO `engine4_store_transactiontypes` (`type`, `plugin`) VALUES
		('store_cart', 'Store_Plugin_Transaction_Cart'),
		('store_request', 'Store_Plugin_Transaction_Request');");

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_pages`(`name`,    `displayname`, `title`, `description`, `provides`, `view_count`) VALUES
('store_index_index', 'Store Home', 'Store Home', 'This is the home page for stores', 'no-subject', 0)
CONTENT;

            $db->query($sql);
            $page_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'container', 'top', NULL, 1)
CONTENT;

            $db->query($sql);
            $top_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'container', 'middle', $top_content_id, 6)
CONTENT;

            $db->query($sql);
            $content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'widget', 'store.navigation-tabs', $content_id, 3)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'container', 'main', NULL, 2)
CONTENT;

            $db->query($sql);
            $main_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'container', 'middle', $main_content_id, 6)
CONTENT;

            $db->query($sql);
            $content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES
($page_id, 'widget', 'store.product-slider-sponsored', $content_id, 14, '[\"[]\"]'),
($page_id, 'widget', 'store.product-sponsored-carousel', $content_id, 15, '{\"title\":\"STORE_Sponsored Products\"}'),
($page_id, 'widget', 'store.product-featured-carousel', $content_id, 16, '{\"title\":\"STORE_Featured Products\"}'),
($page_id, 'widget', 'store.product-browse', $content_id, 17, '[\"[]\"]')
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'container', 'left', $main_content_id, 6)
CONTENT;

            $db->query($sql);
            $content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES
($page_id, 'widget', 'store.product-of-the-day', $content_id, 6, '{\"title\":\"STORE_Product Of The Day\"}'),
($page_id, 'widget', 'store.store-of-the-day', $content_id, 7, '{\"title\":\"STORE_Store Of The Day\"}'),
($page_id, 'widget', 'store.store-popular-stores', $content_id, 12, '{\"title\":\"STORE_Popular Stores\"}'),
($page_id, 'widget', 'store.product-populars', $content_id, 13, '{\"title\":\"Popular Products\"}')
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name`='rate' && `enabled`=1 LIMIT 1
CONTENT;

            if ($db->fetchOne($sql)) {
                $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES
			($page_id, 'widget', 'rate.product-rate', $content_id, 10, '{\"title\":\"Most Rated Products\",\"titleCount\":true}'),
			($page_id, 'widget', 'rate.store-reviewed', $content_id, 11, '{\"title\":\"Most Reviewed Stores\",\"titleCount\":true}');");
            }

            $sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name`='like' && `enabled`=1 LIMIT 1
CONTENT;
            if ($db->fetchOne($sql)) {
                $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES
($page_id, 'widget', 'like.most-liked-products', $content_id, 8, '{\"title\":\"like_Most Liked Products\",\"titleCount\":false}'),
($page_id, 'widget', 'like.most-liked-stores', $content_id, 9, '{\"title\":\"like_Most Liked Stores\",\"titleCount\":false}');");
            }


            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'container', 'right', $main_content_id, 6)
CONTENT;

            $db->query($sql);
            $content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES
($page_id, 'widget', 'store.product-search', $content_id, 19, '{\"title\":\"STORE_Search Product\"}'),
($page_id, 'widget', 'store.product-categories', $content_id, 20, '{\"title\":\"STORE_Product Categories\"}'),
($page_id, 'widget', 'store.product-tags', $content_id, 21, '{\"title\":\"STORE_Product Tags\"}'),
($page_id, 'widget', 'store.store-tags', $content_id, 22, '{\"title\":\"STORE_Store Tags\"}'),
($page_id, 'widget', 'store.product-randoms', $content_id, 23, '{\"title\":\"Random Products\"}')
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `levels`, `provides`, `view_count`) VALUES
('store_index_products', 'Browse Products', NULL, 'STORE_Browse Products', 'This is the browse page for products', '', NULL, 'no-subject', 0);
CONTENT;

            $db->query($sql);
            $page_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $main_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'middle', $main_content_id, 6, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-browse', $middle_content_id, 6, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'right', $main_content_id, 5, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $right_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-categories', $right_content_id, 10, '{\"title\":\"STORE_Product Categories\"}', NULL),
($page_id, 'widget', 'store.product-search', $right_content_id, 9, '{\"title\":\"STORE_Search Product\"}', NULL),
($page_id, 'widget', 'store.product-tags', $right_content_id, 11, '{\"title\":\"STORE_Popular Product Tags\"}', NULL);
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name`='like' && `enabled`=1 LIMIT 1
CONTENT;
            if ($db->fetchOne($sql)) {
                $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'like.most-liked-products', $right_content_id, 8, '{\"title\":\"like_Most Liked Products\",\"titleCount\":false}', NULL);
");
            }

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'top', NULL, 1, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $top_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'middle', $top_content_id, 6, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.navigation-tabs', $middle_content_id, 3, '[]', NULL),
($page_id, 'widget', 'store.product-slider-sponsored', $middle_content_id, 4, '[]', NULL);
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `levels`, `provides`, `view_count`) VALUES
('store_index_stores', 'Browse Stores', NULL, 'STORE_Browse Stores', 'This is the browse page for stores', '', NULL, 'no-subject', 0);
CONTENT;

            $db->query($sql);
            $page_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'top', NULL, 1, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $top_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'middle', $top_content_id, 6, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.navigation-tabs', $middle_content_id, 3, '[\"[]\"]', NULL),
($page_id, 'widget', 'store.store-slider-sponsored', $middle_content_id, 4, '[]', NULL);
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'main', NULL, 2, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $main_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'right', $main_content_id, 5, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $right_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.store-categories', $right_content_id, 11, '{\"title\":\"STORE_Store Categories\"}', NULL),
($page_id, 'widget', 'store.store-locations', $right_content_id, 12, '{\"title\":\"STORE_Store Locations\"}', NULL),
($page_id, 'widget', 'store.store-search', $right_content_id, 10, '[\"[]\"]', NULL),
($page_id, 'widget', 'store.store-tags', $right_content_id, 13, '{\"title\":\"STORE_Store Tags\"}', NULL),
($page_id, 'widget', 'store.store-of-the-day', $right_content_id, 9, '{\"title\":\"STORE_Store Of The Day\"}', NULL);
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'middle', $main_content_id, 6, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);
            $middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.store-browse', $middle_content_id, 7, '[\"[]\"]', NULL);
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_pages` (`name`,     `displayname`,     `url`,     `title`,     `description`,     `keywords`,     `levels`,     `provides`,     `view_count`) VALUES
('store_product_index', 'Product Profile', NULL, 'Product Profile', 'This is a store product\'s profile.', '', NULL, 'subject=store_product', 0)
CONTENT;

            $db->query($sql);
            $page_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'main', null , 2, '[\"[]\"]', NULL)
CONTENT;

            $db->query($sql);
            $main_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'middle', $main_content_id, 6, '[\"[]\"]', NULL)
CONTENT;

            $db->query($sql);
            $middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-status', $middle_content_id, 3, '[\"[]\"]', NULL)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-info', $middle_content_id, 4, '[\"[]\"]', NULL)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'core.container-tabs', $middle_content_id, 5, '{\"max\":6}', NULL)
CONTENT;

            $db->query($sql);
            $tabs_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-photos', $tabs_content_id, 1, '{\"title\":\"Photos\"}', NULL)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-video', $tabs_content_id, 2, '{\"title\":\"Video\"}', NULL)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-audios', $tabs_content_id, 3, '{\"title\":\"Audios\"}', NULL)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'container', 'right', $main_content_id, 5, '[\"[]\"]', NULL)
CONTENT;

            $db->query($sql);
            $right_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'store.product-options', $right_content_id, 7, '[\"[]\"]', NULL)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name`='rate' && `enabled`=1 LIMIT 1
CONTENT;
            if ($db->fetchOne($sql)) {
                $db->query("
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
($page_id, 'widget', 'rate.widget-rate', $right_content_id, 8, '{\"title\":\"Rate This\",\"titleCount\":true}', NULL);");
            }

            $sql = <<<CONTENT
SELECT `c`.`content_id`, `p`.`page_id` FROM `engine4_core_pages` AS `p`
LEFT JOIN `engine4_core_content` AS `c` ON (`c`.`page_id` = `p`.`page_id` && `c`.`type`='container' && `c`.`name` = 'main')
WHERE `p`.`name`='footer' LIMIT 1
CONTENT;
            if (null != ($row = $db->fetchRow($sql))) {
                $page_id = $row['page_id'];
                $content_id = $row['content_id'];
                $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'widget', 'store.product-footer-products', $content_id, 1);");
            }

            $sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'page' LIMIT 1
CONTENT;

            if (null != $db->fetchRow($sql)) {
                $db->query("INSERT IGNORE INTO `engine4_page_modules` (`name`, `widget`, `order`, `params`, `informed`) VALUES
		('store', 'store.page-profile-products', 4, '{\"title\":\"Store\", \"titleCount\":true}', 1);");

                $db->query("UPDATE `engine4_authorization_permissions`
		SET `params` = '[\"pagealbum\",\"pageblog\",\"pagediscussion\",\"pageevent\",\"pagemusic\",\"pagevideo\",\"rate\",\"pagecontact\",\"store\"]'
		WHERE `type` = 'page' AND `name` = 'auth_features' AND `value` = 5 AND `params` = '[\"pagealbum\",\"pageblog\",\"pagediscussion\",\"pageevent\",\"pagemusic\",\"pagevideo\",\"rate\",\"pagecontact\"]';");

                $db->query("UPDATE `engine4_authorization_permissions`
		SET `params` = '[\"pagealbum\",\"pageblog\",\"pagediscussion\",\"pageevent\",\"pagemusic\",\"pagevideo\",\"rate\",\"store\"]'
		WHERE `type` = 'page' AND `name` = 'auth_features' AND `value` = 5 AND `params` = '[\"pagealbum\",\"pageblog\",\"pagediscussion\",\"pageevent\",\"pagemusic\",\"pagevideo\",\"rate\"]';");
            };

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } else { //$operation = upgrade|refresh
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}