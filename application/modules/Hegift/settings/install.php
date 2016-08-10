<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 22.03.12 11:50 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hegift_Installer extends Engine_Package_Installer_Module {

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

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_hegift_categories` (
			`category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
			PRIMARY KEY (`category_id`)
		) COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_hegift_gifts` (
			`gift_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
			`photo_id` INT(11) NOT NULL DEFAULT '0',
			`file_id` INT(11) NOT NULL DEFAULT '0',
			`owner_id` INT(11) NOT NULL DEFAULT '0',
			`type` TINYINT(1) NOT NULL DEFAULT '1',
			`credits` INT(11) NOT NULL DEFAULT '0',
			`amount` INT(11) NULL DEFAULT NULL,
			`sent_count` INT(11) NOT NULL DEFAULT '0',
			`category_id` INT(11) NOT NULL DEFAULT '1',
			`creation_date` DATETIME NULL DEFAULT NULL,
			`starttime` DATETIME NULL DEFAULT NULL,
			`endtime` DATETIME NULL DEFAULT NULL,
			`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
			`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			PRIMARY KEY (`gift_id`),
			INDEX `category_id` (`category_id`),
			INDEX `owner_id` (`owner_id`),
			INDEX `file_id` (`file_id`),
			INDEX `photo_id` (`photo_id`)
		) COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_hegift_recipients` (
			`recipient_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			`gift_id` INT(11) NOT NULL DEFAULT '0',
			`subject_id` INT(11) NOT NULL DEFAULT '0',
			`object_id` INT(11) NOT NULL DEFAULT '0',
			`message` TEXT NOT NULL COLLATE 'utf8_unicode_ci',
			`privacy` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			`approved` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
			`send_date` DATETIME NULL DEFAULT NULL,
			PRIMARY KEY (`recipient_id`),
			INDEX `gift_id` (`gift_id`),
			INDEX `subject_id` (`subject_id`),
			INDEX `object_id` (`object_id`)
		) COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;");

            $db->query("INSERT IGNORE INTO `engine4_hegift_categories` (`category_id`, `title`) VALUES 
		(1, 'Default'),
		(2, 'Birthday'),
		(3, 'Holidays'),
		(4, 'Love');");

            $db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
		('sent_gift', 'hegift', '{item:\$subject} sent a gift to {item:\$object}:', 1, 5, 1, 1, 1, 1);");

            $db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
		('send_gift', 'hegift', 'Surprise! {item:\$subject} sent you a gift. Check it {item:\$object:\$label:\$action}.', 0, '', 1);");

            $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
		('notify_send_gift', 'hegift', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");

            $db->query("INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES 
		('hegift_main', 'standard', 'Virtual Gifts Main Navigation Menu', 999);");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
		('core_main_hegift', 'hegift', 'HEGIFT_Gifts', NULL, '{\"route\":\"hegift_general\"}', 'core_main', NULL, 1, 0, 11),
		('core_admin_main_plugins_hegift', 'hegift', 'vGift', NULL, '{\"route\":\"admin_default\",\"module\":\"hegift\",\"controller\":\"index\",\"action\":\"index\"}', 'core_admin_main_plugins', NULL, 1, 0, 888),
		('hegift_admin_main_index', 'hegift', 'View Gifts', NULL, '{\"route\":\"admin_default\",\"module\":\"hegift\",\"controller\":\"index\",\"action\":\"index\"}', 'hegift_admin_main', NULL, 1, 0, 1),
		('hegift_admin_main_category', 'hegift', 'Categories', NULL, '{\"route\":\"admin_default\",\"module\":\"hegift\",\"controller\":\"category\",\"action\":\"index\"}', 'hegift_admin_main', NULL, 1, 0, 2),
		('hegift_main_index', 'hegift', 'Browse Gifts', NULL, '{\"route\":\"hegift_general\"}', 'hegift_main', NULL, 1, 0, 1),
		('hegift_main_manage', 'hegift', 'Inbox/Sent', 'Hegift_Plugin_Menus', '{\"route\":\"hegift_general\",\"action\":\"manage\"}', 'hegift_main', NULL, 1, 0, 2),
		('hegift_main_own', 'hegift', 'Send Own Gift', 'Hegift_Plugin_Menus', '{\"route\":\"hegift_own\"}', 'hegift_main', NULL, 1, 0, 3),
		('hegift_admin_main_settings', 'hegift', 'Global Settings', NULL, '{\"route\":\"admin_default\",\"module\":\"hegift\",\"controller\":\"settings\",\"action\":\"index\"}', 'hegift_admin_main', NULL, 1, 0, 3),
		('hegift_main_temp', 'hegift', 'Temporary', 'Hegift_Plugin_Menus', '{\"route\":\"hegift_temp\"}', 'hegift_main', NULL, 1, 0, 4),
		('hegift_profile_gift', 'hegift', 'HEGIFT_Send Gift', 'Hegift_Plugin_Menus', '', 'user_profile', '', 1, 0, 6);");

            $db->query("INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES 
		('Video Gift Encode', 'gift_video_encode', 'hegift', 'Hegift_Plugin_Job_Encode', NULL, 1, 102, 1);");

            $db->query("INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`) VALUES 
		('Gift Cleanup', 'hegift', 'Hegift_Plugin_Task_Cleanup')");

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `title`, `description`, `provides`, `view_count`) VALUES
('hegift_index_index', 'Browse Gifts', 'Browse Gifts', 'This is the browse page for Virtual Gifts', 'no-subject', 0)
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
            $top_middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'widget', 'hegift.navigation-tabs', $top_middle_content_id, 3)
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
($page_id, 'container', 'right', $main_content_id, 5)
CONTENT;

            $db->query($sql);
            $main_right_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES
($page_id, 'widget', 'hegift.gift-categories', $main_right_content_id, 8, '{\"title\":\"Categories\"}'),
($page_id, 'widget', 'hegift.birthdays', $main_right_content_id, 9, '{\"title\":\"Birthdays\"}')
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES
($page_id, 'container', 'middle', $main_content_id, 6)
CONTENT;

            $db->query($sql);
            $main_middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES
($page_id, 'widget', 'hegift.browse-gifts', $main_middle_content_id, 6, '[\"[]\"]')
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
SELECT `page_id` FROM `engine4_core_pages` WHERE `name` = 'user_profile_index' LIMIT 1;
CONTENT;
            $page_id = $db->fetchOne($sql);

            if ($page_id) {
                $sql = <<<CONTENT
SELECT `name` FROM `engine4_core_content` WHERE `name` like '%profile-photo%' AND `page_id` = $page_id LIMIT 1;
CONTENT;
                $widget_name = $db->fetchOne($sql);

                if ($widget_name != null && $widget_name != 'hegift.profile-photo') {
                    $sql = <<<CONTENT
UPDATE `engine4_core_content` SET `name` = 'hegift.profile-photo' WHERE `name` like '%profile-photo%' AND `page_id` = $page_id;
CONTENT;

                    $db->query($sql);
                }
            }

            $sql = <<<CONTENT
SELECT `user_id` FROM `engine4_users` WHERE `level_id` = 1 ORDER BY `user_id` ASC LIMIT 1;
CONTENT;
            $user_id = $db->fetchOne($sql);

            $sql = <<<CONTENT
SELECT `service_id` FROM `engine4_storage_services` WHERE `enabled`=1 AND `default`=1 LIMIT 1;
CONTENT;
            $service_id = $db->fetchOne($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 1, $user_id, $service_id, 'public/gift/2a/002a_46fa.png', 'png', '0LkZa6bjc_m_m.png', 'image', 'png', 10368, '0e5046fa5154cea3cbdfbaaed085bba2')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(1, 'Happy B\'Day!', $file_id, 0, 0, 1, 20, NULL, 0, 2, NOW(), NULL, NULL, 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 1, $user_id, $service_id, 'public/gift/2b/002b_46fa.png', 'png', '0LkZa6bjc_m_in.png', 'image', 'png', 10368, '0e5046fa5154cea3cbdfbaaed085bba2'),
($file_id, 'thumb.icon', 'gift', 1, $user_id, $service_id, 'public/gift/2c/002c_95f5.png', 'png', '0LkZa6bjc_m_is.png', 'image', 'png', 5171, 'b2b895f5f1f1e74625709f88877808f2');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 2, $user_id, $service_id, 'public/gift/2e/002e_5b1e.png', 'png', '0EiuPggsvc_m_m.png', 'image', 'png', 13867, 'a1935b1e230aa33d75fc3c56195273e7')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(2, 'Cake', $file_id, 0, 0, 1, 20, NULL, 0, 2, NOW(), NULL, NULL, 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 2, $user_id, $service_id, 'public/gift/2f/002f_5b1e.png', 'png', '0EiuPggsvc_m_in.png', 'image', 'png', 13867, 'a1935b1e230aa33d75fc3c56195273e7'),
($file_id, 'thumb.icon', 'gift', 2, $user_id, $service_id, 'public/gift/30/0030_9b33.png', 'png', '0EiuPggsvc_m_is.png', 'image', 'png', 6003, '8bf49b337b88664683704bcd1f1b509f');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 3, $user_id, $service_id, 'public/gift/32/0032_061e.png', 'png', '0of3FGjGGl_m_m.png', 'image', 'png', 13225, '52f2061ef6387b2edefd1d68064a2f69')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(3, 'Gifts', $file_id, 0, 0, 1, 20, NULL, 0, 2, NOW(), NULL, NULL, 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 3, $user_id, $service_id, 'public/gift/33/0033_061e.png', 'png', '0of3FGjGGl_m_in.png', 'image', 'png', 13225, '52f2061ef6387b2edefd1d68064a2f69'),
($file_id, 'thumb.icon', 'gift', 3, $user_id, $service_id, 'public/gift/34/0034_c534.png', 'png', '0of3FGjGGl_m_is.png', 'image', 'png', 5581, '162bc534250751d2937f8554f5ca205f');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 4, $user_id, $service_id, 'public/gift/36/0036_d358.png', 'png', '0QARAwBs99_m_m.png', 'image', 'png', 7948, '2412d358039a35db33fc45ca851bfca9')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(4, 'Balloons', $file_id, 0, 0, 1, 20, NULL, 0, 2, NOW(), NULL, NULL, 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 4, $user_id, $service_id, 'public/gift/37/0037_d358.png', 'png', '0QARAwBs99_m_in.png', 'image', 'png', 7948, '2412d358039a35db33fc45ca851bfca9'),
($file_id, 'thumb.icon', 'gift', 4, $user_id, $service_id, 'public/gift/38/0038_83dc.png', 'png', '0QARAwBs99_m_is.png', 'image', 'png', 4491, 'c35383dcd2d7fd5560943b1dfe908cee');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 5, $user_id, $service_id, 'public/gift/3a/003a_454f.png', 'png', '030KHzYEiV_m_m.png', 'image', 'png', 9372, '7ddc454fe62fde5bd5ba44bc02d8afd2')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(5, 'I\'ll be there', $file_id, 0, 0, 1, 20, NULL, 0, 4, NOW(), NULL, NULL, 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 5, $user_id, $service_id, 'public/gift/3b/003b_454f.png', 'png', '030KHzYEiV_m_in.png', 'image', 'png', 9372, '7ddc454fe62fde5bd5ba44bc02d8afd2'),
($file_id, 'thumb.icon', 'gift', 5, $user_id, $service_id, 'public/gift/3c/003c_cc62.png', 'png', '030KHzYEiV_m_is.png', 'image', 'png', 5058, '8e63cc6221cba786875d39bb74ccdbf1');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 6, $user_id, $service_id, 'public/gift/3e/003e_a673.png', 'png', '0uCQtwwk9v_m_m.png', 'image', 'png', 7319, '68f4a673a3c6c31dd718b19e94b256fe')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(6, 'Forever', $file_id, 0, 0, 1, 20, NULL, 0, 4, NOW(), NULL, NULL, 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 6, $user_id, $service_id, 'public/gift/3f/003f_a673.png', 'png', '0uCQtwwk9v_m_in.png', 'image', 'png', 7319, '68f4a673a3c6c31dd718b19e94b256fe'),
($file_id, 'thumb.icon', 'gift', 6, $user_id, $service_id, 'public/gift/40/0040_bff1.png', 'png', '0uCQtwwk9v_m_is.png', 'image', 'png', 4072, 'd263bff17f965b6f7de5163b4876e802');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 7, $user_id, $service_id, 'public/gift/42/0042_2de9.png', 'png', '0YfMKjKOXJ_m_m.png', 'image', 'png', 7896, 'c6c62de91f9d0f410c53be5e689368c9')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(7, 'Merry Christmas', $file_id, 0, 0, 1, 20, NULL, 0, 3, NOW(), '2012-12-24 18:00:00', '2013-01-18 18:00:00', 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 7, $user_id, $service_id, 'public/gift/43/0043_2de9.png', 'png', '0YfMKjKOXJ_m_in.png', 'image', 'png', 7896, 'c6c62de91f9d0f410c53be5e689368c9'),
($file_id, 'thumb.icon', 'gift', 7, $user_id, $service_id, 'public/gift/44/0044_2ed1.png', 'png', '0YfMKjKOXJ_m_is.png', 'image', 'png', 3928, 'e2ea2ed1ecd603c6487efaa55cd9ed31');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 8, $user_id, $service_id, 'public/gift/46/0046_2841.png', 'png', '02hl3DDNHN_m_m.png', 'image', 'png', 5042, '27c5284161e61d6d4188383f0326b733')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(8, 'Christmas Tree', $file_id, 0, 0, 1, 20, NULL, 0, 3, NOW(), '2012-12-24 18:00:00', '2013-01-18 18:00:00', 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 8, $user_id, $service_id, 'public/gift/47/0047_2841.png', 'png', '02hl3DDNHN_m_in.png', 'image', 'png', 5042, '27c5284161e61d6d4188383f0326b733'),
($file_id, 'thumb.icon', 'gift', 8, $user_id, $service_id, 'public/gift/48/0048_d15e.png', 'png', '02hl3DDNHN_m_is.png', 'image', 'png', 4492, 'e38dd15e5e8117342f58777ca4475ffc');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 9, $user_id, $service_id, 'public/gift/4a/004a_8468.png', 'png', '0F9DF61A6Z_m_m.png', 'image', 'png', 6729, 'd8f88468b7b682c80ef961c112dd3474')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(9, 'Halloween', $file_id, 0, 0, 1, 20, NULL, 0, 3, NOW(), '2012-10-29 18:00:00', '2012-10-31 18:00:00', 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 9, $user_id, $service_id, 'public/gift/4b/004b_8468.png', 'png', '0F9DF61A6Z_m_in.png', 'image', 'png', 6729, 'd8f88468b7b682c80ef961c112dd3474'),
($file_id, 'thumb.icon', 'gift', 9, $user_id, $service_id, 'public/gift/4c/004c_ff5a.png', 'png', '0F9DF61A6Z_m_is.png', 'image', 'png', 3576, 'e73cff5a98bd9e6f90c0af587710f5fe');
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES
('gift', 10, $user_id, $service_id, 'public/gift/4d/004d_ecf2.png', 'png', '0SXf9ZxYXq_m.png', 'image', 'png', 7407, '90b6ecf27d87cedaaec7089f79538601')
CONTENT;

            $db->query($sql);
            $file_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_hegift_gifts` (`gift_id`, `title`, `photo_id`, `file_id`, `owner_id`, `type`, `credits`, `amount`, `sent_count`, `category_id`, `creation_date`, `starttime`, `endtime`, `status`, `enabled`) VALUES 
(10, 'Halloween', $file_id, 0, 0, 1, 20, NULL, 0, 3, NOW(), '2012-10-29 18:00:00', '2012-10-31 18:00:00', 0, 1)
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_storage_files` (`parent_file_id`, `type`, `parent_type`, `parent_id`, `user_id`, `service_id`, `storage_path`, `extension`, `name`, `mime_major`, `mime_minor`, `size`, `hash`) VALUES 
($file_id, 'thumb.normal', 'gift', 10, $user_id, $service_id, 'public/gift/4e/004e_ecf2.png', 'png', '0SXf9ZxYXq_in.png', 'image', 'png', 7407, '90b6ecf27d87cedaaec7089f79538601'),
($file_id, 'thumb.icon', 'gift', 10, $user_id, $service_id, 'public/gift/4f/004f_9d70.png', 'png', '0SXf9ZxYXq_is.png', 'image', 'png', 3598, '145c9d7033ba18cf9920cf16d8ae947b');
CONTENT;

            $db->query($sql);

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } else { //$operation = upgrade|refresh
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}