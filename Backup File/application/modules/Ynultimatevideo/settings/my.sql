/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */

-- change table permissions (change length of column type)
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynultimatevideo', 'YN - Ultimate Video', 'YN - Ultimate Video', '4.01p1', 1, 'extra') ;

-- insert menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('ynultimatevideo_main', 'standard', 'YN - Ultimate Video Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_main_ynultimatevideo', 'ynultimatevideo', 'Ultimate Video', '', '{"route":"ynultimatevideo_general"}', 'core_main', '', 1, 0, 999),
('ynultimatevideo_main_browse', 'ynultimatevideo', 'Videos Home', '', '{"route":"ynultimatevideo_general"}', 'ynultimatevideo_main', '', 1, 0, 900),
('ynultimatevideo_main_listings', 'ynultimatevideo', 'Browse Videos', '', '{"route":"ynultimatevideo_general","action":"list"}', 'ynultimatevideo_main', '', 1, 0, 901),
('ynultimatevideo_main_playlist', 'ynultimatevideo', 'Browse Playlists', '', '{"route":"ynultimatevideo_playlist","action":"index"}', 'ynultimatevideo_main', '', 1, 0, 902),
('ynultimatevideo_main_manage', 'ynultimatevideo', 'My Items', 'Ynultimatevideo_Plugin_Menus', '{"route":"ynultimatevideo_general","action":"manage"}', 'ynultimatevideo_main', '', 1, 0, 903),
('ynultimatevideo_main_create', 'ynultimatevideo', 'Post New Video', 'Ynultimatevideo_Plugin_Menus', '{"route":"ynultimatevideo_general","action":"create"}', 'ynultimatevideo_main', '', 1, 0, 904),
('core_admin_main_plugins_ynultimatevideo', 'ynultimatevideo', 'YN - Ultimate Videos', '', '{"route":"admin_default","module":"ynultimatevideo","controller":"manage"}', 'core_admin_main_plugins', '', 1, 0, 999),
('ynultimatevideo_admin_main_level', 'ynultimatevideo', 'Member Level Settings', '', '{"route":"admin_default","module":"ynultimatevideo","controller":"settings","action":"level"}', 'ynultimatevideo_admin_main', '', 1, 0, 4),
('ynultimatevideo_admin_main_categories', 'ynultimatevideo', 'Categories', '', '{"route":"admin_default","module":"ynultimatevideo","controller":"category","action":"index"}', 'ynultimatevideo_admin_main', '', 1, 0, 6),
('ynultimatevideo_admin_main_manage', 'ynultimatevideo', 'Manage Videos', '', '{"route":"admin_default","module":"ynultimatevideo","controller":"manage"}', 'ynultimatevideo_admin_main', '', 1, 0, 1),
('ynultimatevideo_admin_main_utility', 'ynultimatevideo', 'Video Utilities', '', '{"route":"admin_default","module":"ynultimatevideo","controller":"settings","action":"utility"}', 'ynultimatevideo_admin_main', '', 1, 0, 2),
('ynultimatevideo_admin_main_settings', 'ynultimatevideo', 'Global Settings', '', '{"route":"admin_default","module":"ynultimatevideo","controller":"settings"}', 'ynultimatevideo_admin_main', '', 1, 0, 3),
('ynultimatevideo_admin_main_youtubesettings', 'ynultimatevideo', 'YouTube Settings', '', '{"route":"admin_default","module":"ynultimatevideo","controller":"settings", "action":"youtube"}', 'ynultimatevideo_admin_main', '', 1, 0, 5),
('ynultimatevideo_admin_main_migrationvideo', 'ynultimatevideo', 'Migrate Videos', 'Ynultimatevideo_Plugin_Menus::canMigrateVideo', '{"route":"admin_default","module":"ynultimatevideo","controller":"migration-video"}', 'ynultimatevideo_admin_main', '', 1, 0, 7);

-- create videos table
DROP TABLE IF EXISTS `engine4_ynultimatevideo_videos`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_videos` (
  `video_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `owner_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `parent_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci default NULL,
  `parent_id` int(11) unsigned default NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `favorite_count` int(11) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL,
  `code` varchar(150) NOT NULL,
  `photo_id` int(11) unsigned default NULL,
  `large_photo_id` int(11) unsigned default NULL,
  `rating` float NOT NULL,
  `category_id` int(11) unsigned NOT NULL default '0',
  `status` tinyint(1) NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `duration` int(9) unsigned NOT NULL DEFAULT '0',
  `rotation` smallint unsigned NOT NULL DEFAULT '0',
  `featured` TINYINT(1) DEFAULT '0',
  `import_id` int(11) NOT NULL DEFAULT '0',
  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL,
  `user_token` VARCHAR( 256 ) NULL DEFAULT NULL,
  `allow_upload_channel` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`video_id`),
  KEY `owner_id` (`owner_id`,`owner_type`),
  KEY `search` (`search`),
  KEY `creation_date` (`creation_date`),
  KEY `view_count` (`view_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- create video rating
DROP TABLE IF EXISTS `engine4_ynultimatevideo_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_ratings` (
  `video_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`video_id`,`user_id`),
  KEY `INDEX` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- create favourite table
DROP TABLE IF EXISTS `engine4_ynultimatevideo_favorites`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_favorites` (
  `favorite_id` int(10) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`favorite_id`),
  UNIQUE KEY `video_id_user_id` (`video_id`,`user_id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- create play list association table
DROP TABLE IF EXISTS `engine4_ynultimatevideo_playlistassoc`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_playlistassoc` (
  `playlistassoc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) unsigned NOT NULL DEFAULT '0',
  `video_id` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `video_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`playlistassoc_id`),
  UNIQUE KEY `playlist_id_video_id` (`playlist_id`,`video_id`),
  KEY `creation_time` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create playlist table
DROP TABLE IF EXISTS `engine4_ynultimatevideo_playlists`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_playlists` (
  `playlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `ordering` smallint(8) unsigned NOT NULL DEFAULT '999',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `video_count` int(10) NOT NULL DEFAULT '0',
  `view_mode` int(10) NOT NULL DEFAULT '0',
  `import_id` int(11) NOT NULL DEFAULT '0',
  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL,
  PRIMARY KEY (`playlist_id`),
  KEY `user_id` (`user_id`),
  KEY `creation_date` (`creation_date`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- create signatures table
DROP TABLE IF EXISTS `engine4_ynultimatevideo_signatures`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_signatures` (
  `signature_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `video_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`signature_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- create watchlaters table
DROP TABLE IF EXISTS `engine4_ynultimatevideo_watchlaters`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_watchlaters` (
  `watchlater_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `watched` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `watched_date` datetime NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`watchlater_id`),
  UNIQUE KEY `video_id_user_id` (`video_id`,`user_id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`),
  KEY `watched` (`watched`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynultimatevideo_history`
--
DROP TABLE IF EXISTS `engine4_ynultimatevideo_history`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_history` (
`history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`item_type` varchar(128) NOT NULL,
`item_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynultimatevideo_imports`
--

CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_imports` (
`import_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`item_type` varchar(128) NOT NULL,
`item_id` int(11) unsigned NOT NULL,
`from_type` varchar(128) NOT NULL,
`from_id` int(11) unsigned NOT NULL,
`status` ENUM('processing','updating','imported') NOT NULL DEFAULT 'processing',
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`import_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynultimatevideo_history`
--

CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_history` (
`history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`item_type` varchar(128) NOT NULL,
`item_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'max' as `name`,
    3 as `value`,
    '20' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN
-- create, edit, delete, view, comment, upload
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'upload' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, edit, delete, view, comment, upload
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'upload' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_video' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- --------------------
-- insert authorization_permissions
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_favorite' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_favorite' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_favorite' as `type`,
    'comment' as `name`,
    2 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynultimatevideo_favorite' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'view' as `name`,
  2 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'create' as `name`,
  2 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'view' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'create' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'comment' as `name`,
  2 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'comment' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'edit' as `name`,
  2 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'delete' as `name`,
  2 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynultimatevideo_playlist' as `type`,
	'delete' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'edit' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'auth_view' as `name`,
  5 as `value`,
  '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'auth_comment' as `name`,
  5 as `value`,
  '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlistasso' as `type`,
  'comment' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlistasso' as `type`,
  'view' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_video' as `type`,
  'max' as `name`,
  3 as `value`,
  100 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_video' as `type`,
  'max' as `name`,
  3 as `value`,
  500 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'ynultimatevideo_playlist' as `type`,
  'remove' as `name`,
  1 as `value`,
  NULL as `params`
FROM `engine4_authorization_levels`;

--
-- Dumping data for table `engine4_core_settings`
--
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('ynultimatevideo.ffmpeg.path', ''),
('ynultimatevideo.jobs', 2),
('ynultimatevideo.embeds', 1);

--
-- Dumping data for table `engine4_core_jobtypes`
--
INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('YN - Ultimate Videos Encode', 'ynultimatevideo_encode', 'ynultimatevideo', 'Ynultimatevideo_Plugin_Job_Encode', NULL, 1, 75, 2),
('YN - Ultimate Video Rebuild Privacy', 'ynultimatevideo_maintenance_rebuild_privacy', 'ynultimatevideo', 'Ynultimatevideo_Plugin_Job_Maintenance_RebuildPrivacy', NULL, 1, 50, 1),
('YN - Ultimate Videos Migration', 'ynultimatevideo_migration', 'ynultimatevideo', 'Ynultimatevideo_Plugin_Job_Migration', NULL, 1, 75, 2),
('YN - Ultimate Videos Upload YouTube', 'ynultimatevideo_uploadyoutube', 'ynultimatevideo', 'Ynultimatevideo_Plugin_Job_UploadToChannel', NULL, 1, 75, 2);

--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynultimatevideo_processed', 'ynultimatevideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynultimatevideo_processed_failed', 'ynultimatevideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('ynultimatevideo_send_video_to_friends', 'ynultimatevideo', '[host],[email],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynultimatevideo_video_new', 'ynultimatevideo', '{item:$subject} posted a new video:', 1, 5, 1, 3, 1, 0),
('ynultimatevideo_add_favorite', 'ynultimatevideo', '{item:$subject} add a video to his/her favorite playlist', 1, 7, 1, 3, 1, 0),
('ynultimatevideo_playlist_new', 'ynultimatevideo', '{item:$subject} posted a new playlist:', 1, 5, 1, 3, 1, 0),
('ynultimatevideo_comment_video', 'ynultimatevideo', '{item:$subject} commented on {item:$owner}\'s {item:$object:video}: {body:$body}', 1, 1, 1, 1, 1, 0),
('comment_ynultimatevideo_playlist', 'ynultimatevideo', '{item:$subject} commented on {item:$owner}\'s {item:$object:video}: {body:$body}', 1, 1, 1, 1, 1, 0),
('ynultimatevideo_playlist_add_video', 'ynultimatevideo', '{item:$subject} add a video to the playlist {item:$object}', 1, 5, 1, 3, 1, 0),
('ynultimatevideo_add_video_new_playlist', 'ynultimatevideo', '{item:$subject} created a new {item:$object:playlist} and added a video to this playlist', 1, 5, 1, 3, 1, 0);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynultimatevideo_processed', 'ynultimatevideo', 'Your {item:$object:video} is ready to be viewed.', 0, '', 1),
('ynultimatevideo_processed_failed', 'ynultimatevideo', 'Your {item:$object:video} has failed to process.', 0, '', 1);

-- Update video auth_view,auth_comment
UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]'
WHERE `type` = 'ynultimatevideo_video' and `name` = 'auth_view';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]'
WHERE `type` = 'ynultimatevideo_video' and `name` = 'auth_comment';

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynultimatevideo_video_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_video_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynultimatevideo_video_fields_maps`
--

INSERT IGNORE INTO `engine4_ynultimatevideo_video_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynultimatevideo_video_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_video_fields_meta` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text COLLATE utf8_unicode_ci,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynultimatevideo_video_fields_meta`
--

INSERT IGNORE INTO `engine4_ynultimatevideo_video_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynultimatevideo_video_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_video_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynultimatevideo_video_fields_options`
--

INSERT IGNORE INTO `engine4_ynultimatevideo_video_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
(1, 1, 'Default Type', 0);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynultimatevideo_video_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_video_fields_search` (
  `item_id` int(11) unsigned NOT NULL,
  `profile_type` enum('1','4') COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` smallint(6) unsigned DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `first_name` (`first_name`),
  KEY `last_name` (`last_name`),
  KEY `gender` (`gender`),
  KEY `birthdate` (`birthdate`),
  KEY `profile_type` (`profile_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynultimatevideo_video_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_video_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynultimatevideo_categories`
--
DROP TABLE IF EXISTS `engine4_ynultimatevideo_categories`;
CREATE TABLE IF NOT EXISTS `engine4_ynultimatevideo_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `pleft` int(11) unsigned NOT NULL,
  `pright` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '0',
  `option_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynultimatevideo_categories`
--

INSERT IGNORE INTO `engine4_ynultimatevideo_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, 1, 1, 36, 0, 'All Categories','1'),
(2, 0, 1, 32, 33, 1, 'Animation','2'),
(3, 0, 1, 30, 31, 1, 'Arts & Design','3'),
(4, 0, 1, 28, 29, 1, 'Cameras & Techniques','4'),
(5, 0, 1, 26, 27, 1, 'Comedy','5'),
(6, 0, 1, 24, 25, 1, 'Documentary','6'),
(7, 0, 1, 22, 23, 1, 'Experimental','7'),
(8, 0, 1, 20, 21, 1, 'Fashion','8'),
(9, 0, 1, 18, 19, 1, 'Food','9'),
(10, 0, 1, 16, 17, 1, 'Instructionals','10'),
(11, 0, 1, 14, 15, 1, 'Music','11'),
(12, 0, 1, 12, 13, 1, 'Narrative','12'),
(13, 0, 1, 10, 11, 1, 'Personal','13'),
(14, 0, 1, 8, 9, 1, 'Reporting & Journalism','14'),
(15, 0, 1, 6, 7, 1, 'Sports','15'),
(16, 0, 1, 4, 5, 1, 'Talks','16'),
(17, 0, 1, 2, 3, 1, 'Travel','17');


--
-- Dumping data for table `engine4_ynultimatevideo_video_fields_options`
--

INSERT IGNORE INTO `engine4_ynultimatevideo_video_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
(1, 1, 'Default Type', 0),
(2, 1, 'Animation', 999),
(3, 1, 'Arts & Design', 999),
(4, 1, 'Cameras & Techniques', 999),
(5, 1, 'Comedy', 999),
(6, 1, 'Documentary', 999),
(7, 1, 'Experimental', 999),
(8, 1, 'Fashion', 999),
(9, 1, 'Food', 999),
(10, 1, 'Instructionals', 999),
(11, 1, 'Music', 999),
(12, 1, 'Narrative', 999),
(13, 1, 'Personal', 999),
(14, 1, 'Reporting & Journalism', 999),
(15, 1, 'Sports', 999),
(16, 1, 'Talks', 999),
(17, 1, 'Travel', 999);