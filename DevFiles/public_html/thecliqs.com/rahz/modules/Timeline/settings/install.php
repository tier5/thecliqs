<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

// 02.03.2013 - TrioxX

class Timeline_Installer extends Engine_Package_Installer_Module
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
            ->where('name = ?', 'wall')
            ->where('enabled = ?', 1);

        $wall = $db->fetchRow($select);

        if (version_compare($wall['version'], '4.2.5p5') < 0) {
            $error_message = $translate->_('You should first update your Wall module.');
            return $this->_error($error_message);
        }

        $select = $db->select()
            ->from('engine4_core_modules')
            ->where('name = ?', 'page')
            ->where('enabled = ?', 1);

        $page = $db->fetchRow($select);

        if ($page) {
            if (version_compare($page['version'], '4.2.3p4') < 0) {
                $error_message = $translate->_('You should first update your Page module.');
                return $this->_error($error_message);
            }
        }

        $select = $db->select()
            ->from('engine4_core_modules')
            ->where('name = ?', 'pagealbum')
            ->where('enabled = ?', 1);

        $page_album = $db->fetchRow($select);

        if ($page_album) {
            if (version_compare($page_album['version'], '4.1.7') < 0) {
                $error_message = $translate->_('You should first update your Page Albums module.');
                return $this->_error($error_message);
            }
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

			$db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
			('cover_photo_update', 'timeline', '{item:\$subject} has added a new cover photo.', 1, 3, 1, 1, 1, 1), 
			('birth_photo_update', 'timeline', '{item:\$subject} has added a new birth photo.', 1, 3, 1, 1, 1, 1);"); 

			$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
			('core_admin_main_plugins_timeline', 'timeline', 'HE - Timeline', NULL, '{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\"}', 'core_admin_main_plugins', NULL, 1, 0, 888),
			('timeline_profile_edit', 'timeline', 'Update Info', 'Timeline_Plugin_Menus', '', 'timeline_profile', NULL, 1, 0, 1), 
			('timeline_profile_friend', 'timeline', 'Friends', 'Timeline_Plugin_Menus', '', 'timeline_profile', NULL, 1, 0, 3), 
			('timeline_profile_block', 'timeline', 'Block', 'Timeline_Plugin_Menus', '', 'timeline_profile', NULL, 1, 0, 4), 
			('timeline_profile_report', 'timeline', 'Report User', 'Timeline_Plugin_Menus', '', 'timeline_profile', NULL, 1, 0, 5), 
			('user_settings_timeline', 'timeline', 'Timeline', 'Timeline_Plugin_Menus', '{\"route\":\"timeline_user_settings\"}', 'user_settings', NULL, 1, 0, 3), 
			('user_edit_timeline', 'timeline', 'Timeline', 'Timeline_Plugin_Menus', '{\"route\":\"timeline_user_edit\"}', 'user_edit', NULL, 1, 0, 3), 
			('timeline_admin_main_settings', 'timeline', 'Global Settings', NULL, '{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\"}', 'timeline_admin_main', NULL, 1, 0, 999);");

			$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
			('timeline.usage', 'choice'), 
			('timeline.menuitems', '20');"); 

			$res = $db->query("SHOW COLUMNS FROM `engine4_users` LIKE 'cover_id';"); 
			if (!$res->rowCount()){ 
			  $db->query("ALTER TABLE `engine4_users` ADD COLUMN `cover_id` INT(11) unsigned NULL default '0' AFTER `photo_id`;"); 
			} 
			$res = $db->query("SHOW COLUMNS FROM `engine4_users` LIKE 'mini_cover_id';"); 
			if (!$res->rowCount()){ 
			  $db->query("ALTER TABLE `engine4_users` ADD COLUMN `mini_cover_id` INT(11) unsigned NULL default '0' AFTER `cover_id`;"); 
			} 
			$res = $db->query("SHOW COLUMNS FROM `engine4_users` LIKE 'born_id';"); 
			if (!$res->rowCount()){ 
			  $db->query("ALTER TABLE `engine4_users` ADD COLUMN `born_id` INT(11) unsigned NULL default '0' AFTER `mini_cover_id`;"); 
			} 

			$sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'album' LIMIT 1 
CONTENT;

			if ($db->fetchOne($sql)){ 
			  $db->query("ALTER TABLE `engine4_album_albums` MODIFY COLUMN `type` ENUM('wall','profile','message','blog','cover','birth','page_cover');"); 
			}; 

			$sql = <<<CONTENT
SELECT TRUE FROM `engine4_core_modules` WHERE `name` = 'pagealbum' LIMIT 1 
CONTENT;

			if ($db->fetchOne($sql)){ 
				$res = $db->query("SHOW COLUMNS FROM `engine4_page_albums` LIKE 'type';"); 
				if (!$res->rowCount()){ 
				  $db->query("ALTER TABLE `engine4_page_albums` ADD COLUMN `type` ENUM('page_cover') NULL AFTER `view_count`;"); 
				} 
			}; 

			$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `title`, `description`, `provides`) VALUES 
('timeline_profile_index', 'Timeline', 'Member Profile', 'This is a member\'s  timeline profile', 'subject=user'); 
CONTENT;

			$db->query($sql); 
			$page_id = $db->lastInsertId(); 

			$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'main', NULL, 2); 
CONTENT;

			$db->query($sql); 
			$main_content_id = $db->lastInsertId(); 

			$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'middle', $main_content_id, 6); 
CONTENT;

			$db->query($sql); 
			$middle_content_id = $db->lastInsertId(); 

			$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'widget', 'timeline.header', $middle_content_id, 4), 
($page_id, 'widget', 'timeline.content', $middle_content_id, 5); 
CONTENT;
			
			$db->query($sql); 

			$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'core.container-tabs', $middle_content_id, 6, '{\"max\":6}'); 
CONTENT;

			$db->query($sql); 
			$tab_container_id = $db->lastInsertId(); 

			$sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'album.profile-albums', $tab_container_id, 7, '{\"title\":\"Albums\",\"titleCount\":true}'), 
($page_id, 'widget', 'video.profile-videos', $tab_container_id, 8, '{\"title\":\"Videos\",\"titleCount\":true}'), 
($page_id, 'widget', 'event.profile-events', $tab_container_id, 9, '{\"title\":\"Events\",\"titleCount\":true}'), 
($page_id, 'widget', 'group.profile-groups', $tab_container_id, 10, '{\"title\":\"Groups\",\"titleCount\":true}'), 
($page_id, 'widget', 'blog.profile-blogs', $tab_container_id, 11, '{\"title\":\"Blogs\",\"titleCount\":true}'), 
($page_id, 'widget', 'classified.profile-classifieds', $tab_container_id, 12, '{\"title\":\"Classifieds\",\"titleCount\":true}'), 
($page_id, 'widget', 'forum.profile-forum-posts', $tab_container_id, 13, '{\"title\":\"Forum Posts\",\"titleCount\":true}'), 
($page_id, 'widget', 'forum.profile-forum-topics', $tab_container_id, 14, '{\"title\":\"Forum Topics\",\"titleCount\":true}'), 
($page_id, 'widget', 'poll.profile-polls', $tab_container_id, 16, '{\"title\":\"Polls\",\"titleCount\":true}'), 
($page_id, 'widget', 'user.profile-friends-followers', $tab_container_id, 17, '{\"title\":\"Followers\",\"titleCount\":true}'), 
($page_id, 'widget', 'user.profile-friends-following', $tab_container_id, 18, '{\"title\":\"Following\",\"titleCount\":true}'), 
($page_id, 'widget', 'user.profile-friends', $tab_container_id, 7, '{\"title\":\"Friends\",\"titleCount\":true}'); 
CONTENT;

			$db->query($sql); 

			$db->query("CREATE TABLE IF NOT EXISTS `engine4_timeline_thumbs` ( 
				`thumb_id` INT(10) NOT NULL AUTO_INCREMENT, 
				`photo_id` INT(10) NULL DEFAULT '0', 
				`type` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci', 
				`title` VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci', 
				`creation_date` DATETIME NOT NULL, 
				`modified_date` DATETIME NOT NULL, 
				`page` TINYINT(1) NULL DEFAULT '0', 
				PRIMARY KEY (`thumb_id`), 
				UNIQUE INDEX `type` (`type`) 
			) 
			COLLATE='utf8_unicode_ci' 
			ENGINE=InnoDB 
			AUTO_INCREMENT=1;"); 

			$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `params`, `menu`, `order`) VALUES 
			('timeline_admin_main_thumbIcons', 'timeline', 'Thumb Settings', '{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\",\"action\":\"thumb-icons\"}', 'timeline_admin_main', 1000), 
			('timeline_admin_main_pageIcons', 'timeline', 'Page Thumb Settings', '{\"route\":\"admin_default\",\"module\":\"timeline\",\"controller\":\"settings\",\"action\":\"page-icons\"}', 'timeline_admin_main', 1001);");
			
			$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        }
        else { //$operation = upgrade|refresh
			$db = Engine_Db_Table::getDefaultAdapter();
		
			$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }
}