INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynmobile', 'Mobile SocialEngine', '', '4.06', 1, 'extra') ;

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_tokens` (
	`token_id` VARCHAR(64) NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`created_at` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`token_id`)
);

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_userdevices` (
  `userdevice_id` varchar(45) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(64) DEFAULT NULL,
  `timestamp` int(10) NOT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `device_id` tinytext NOT NULL,
  PRIMARY KEY (`userdevice_id`),
  KEY `user_id` (`user_id`),
  KEY `token` (`token`)
);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynmobile', 'ynmobile', ' YouNet Mobile', '', '{"route":"admin_default","module":"ynmobile","controller":"menus"}', 'core_admin_main_plugins', '', 999),
('ynmobile_admin_main_menus', 'ynmobile', 'Manage Menus','', '{"route":"admin_default","module":"ynmobile","controller":"menus"}', 'ynmobile_admin_main', '', 1),
('ynmobile_admin_main_settings', 'ynmobile', 'Global Settings','', '{"route":"admin_default","module":"ynmobile","controller":"settings"}', 'ynmobile_admin_main', '', 2),
('ynmobile_admin_main_notifications', 'ynmobile', 'Manage Notifications','', '{"route":"admin_default","module":"ynmobile","controller":"notifications"}', 'ynmobile_admin_main', '', 3);


DROP TABLE IF EXISTS `engine4_ynmobile_menuitems`;
CREATE TABLE IF NOT EXISTS `engine4_ynmobile_menuitems` (
  `menuitem_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'Core',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `order` smallint(6) NOT NULL DEFAULT '999',
  `label` varchar(50) NOT NULL,
  `layout` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `uri` varchar(50) NOT NULL,
  `menu` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`menuitem_id`),
  UNIQUE KEY `name` (`name`),
  KEY `LOOKUP` (`name`,`order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `engine4_ynmobile_menuitems`
--

INSERT IGNORE INTO `engine4_ynmobile_menuitems` (`name`, `module`, `enabled`, `order`, `label`, `layout`, `icon`, `uri`, `menu`) VALUES
('new_feeds', 'core', 1, 1, 'News Feed', 'new_feed', 'icon-rss', 'home', 1),
('event', 'event', 1, 999, 'Events', 'event', 'icon-calendar', 'upcommingevent', 1),
('ynevent', 'ynevent', 1, 5, 'Events', 'event', 'icon-calendar', 'upcommingevent', 1),
('friends', 'core', 1, 2, 'Friends', 'friend', 'icon-group', 'friend', 1),
('album', 'album', 1, 999, 'Albums', 'photo', 'icon-picture', 'albumLanding', 1),
('advalbum', 'advalbum', 1, 6, 'Albums', 'photo', 'icon-picture', 'photoList', 1),
('music', 'music', 1, 3, 'Music', 'music', 'icon-music', 'music', 1),
('video', 'video', 1, 7, 'Videos', 'video', 'icon-facetime-video', 'video', 1),
('ynvideo', 'ynvideo', 1, 999, 'Videos', 'video', 'icon-facetime-video', 'video', 1),
('mail', 'core', 1, 4, 'Mails', 'mail', 'icon-envelope-alt', 'maillist', 1),
('forum', 'Core', 1, 999, 'Forums', 'forum', 'fa-comments', 'forums', 1),
('poll', 'poll', 1, 999, 'Polls', 'poll', 'icon-sidebar-poll', 'polls', 1),
('group', 'core', 1, 999, 'Groups', 'group', 'icon-sidebar-group', 'groups', 1);

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('SE Mobile Video Encode', 'ynmobile_encode', 'ynmobile', 'Ynmobile_Plugin_Job_Encode', NULL, 1, 1, 1);

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_maps` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci,
  `latitude` varchar(64),
  `longitude` varchar(64),
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('ynmobile_checkin', 'ynmobile', '{item:$subject} - {var:$status} at {var:$location}', 1, 5, 1, 1, 1, 1);

-- ALL
-- auth_view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
 
-- USER
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');


-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_storekitpurchases` (
    `storekitpurchase_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`storekitpurchase_key` VARCHAR(75) COMMENT 'which has been created manually on Apple Dev' ,
	`storekitpurchase_module_id` VARCHAR(75),
	`storekitpurchase_type` VARCHAR(255) DEFAULT 'purchase_product' COMMENT 'purchase product/sponsor/feature/...',
	`storekitpurchase_item_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'some modules need item id' ,
	PRIMARY KEY (`storekitpurchase_id`) 
);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobile_admin_main_subscription', 'ynmobile', 'Subscription Products','', '{"route":"admin_default","module":"ynmobile","controller":"subscription"}', 'ynmobile_admin_main', '', 4);

INSERT IGNORE INTO `engine4_ynmobile_menuitems` (`name`, `module`, `enabled`, `order`, `label`, `layout`, `icon`, `uri`, `menu`) VALUES
('blog', 'Core', 1, 999, 'Blogs', 'blog', 'icon-blog', 'blogs', 1),
('classified', 'Core', 1, 999, 'Classifieds', 'classified', 'icon-sidebar-classified', 'classifieds', 1),
('subscribe', 'Core', 1, 999, 'Memberships', 'subscribe', '', 'subscribe', 1);

--
-- profile cover table
--
CREATE TABLE IF NOT EXISTS `engine4_ynmobile_profilecovers` (
  `profilecover_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) DEFAULT 'iphone',
  `owner_type` varchar(64) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `photo_id` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`profilecover_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
