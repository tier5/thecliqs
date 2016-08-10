-- Change table permissions (change length of column type)

ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynmusic_main', 'standard', 'YN - Social Music - Main Navigation Menu', 999);

-- build backend menu

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynmusic', 'ynmusic', 'YN - Music', '', '{"route":"admin_default","module":"ynmusic","controller":"settings"}', 'core_admin_main_plugins', '',  999),
('ynmusic_admin_settings_global', 'ynmusic', 'Global Settings', '', '{"route":"admin_default","module":"ynmusic","controller":"settings"}', 'ynmusic_admin_main', '',  1),
('ynmusic_admin_settings_level', 'ynmusic', 'Member Level Settings', '', '{"route":"admin_default","module":"ynmusic","controller":"settings","action":"level"}', 'ynmusic_admin_main', '',  2),
('ynmusic_admin_main_genres', 'ynmusic', 'Manage Genres', '', '{"route":"admin_default","module":"ynmusic","controller":"genres"}', 'ynmusic_admin_main', '',  3),
('ynmusic_admin_main_artists', 'ynmusic', 'Manage Artists', '', '{"route":"admin_default","module":"ynmusic","controller":"artists"}', 'ynmusic_admin_main', '', 4),  
('ynmusic_admin_main_albums', 'ynmusic', 'Manage Albums', '', '{"route":"admin_default","module":"ynmusic","controller":"albums"}', 'ynmusic_admin_main', '',  5),
('ynmusic_admin_main_songs', 'ynmusic', 'Manage Songs', '', '{"route":"admin_default","module":"ynmusic","controller":"songs"}', 'ynmusic_admin_main', '', 6),
('ynmusic_admin_main_playlists', 'ynmusic', 'Manage Playlists', '', '{"route":"admin_default","module":"ynmusic","controller":"playlists"}', 'ynmusic_admin_main', '', 7),
('ynmusic_admin_main_migration', 'ynmusic', 'Migrate From Mp3 Music', 'Ynmusic_Plugin_Menus::canMigrate', '{"route":"admin_default","module":"ynmusic","controller":"migration"}', 'ynmusic_admin_main', '', 8),
('ynmusic_admin_main_migrationsemusic', 'ynmusic', 'Migrate From SE Music', 'Ynmusic_Plugin_Menus::canMigrateSEMusic', '{"route":"admin_default","module":"ynmusic","controller":"migration-semusic"}', 'ynmusic_admin_main', '', 9),
('ynmusic_admin_main_faqs', 'ynmusic', 'Manage FAQs', '', '{"route":"admin_default","module":"ynmusic","controller":"faqs"}', 'ynmusic_admin_main', '', 10);

-- build front-end menu
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynmusic', 'ynmusic', 'Social Music', '', '{"route":"ynmusic_general"}', 'core_main', '',  999),
('ynmusic_main_browse', 'ynmusic', 'Browse Music', '', '{"route":"ynmusic_general"}', 'ynmusic_main', '', 1),
('ynmusic_main_albums', 'ynmusic', 'Albums', 'Ynmusic_Plugin_Menus', '{"route":"ynmusic_album"}', 'ynmusic_main', '', 2),
('ynmusic_main_songs', 'ynmusic', 'Songs', 'Ynmusic_Plugin_Menus', '{"route":"ynmusic_song"}', 'ynmusic_main', '', 3),
('ynmusic_main_playlists', 'ynmusic', 'Playlists', 'Ynmusic_Plugin_Menus', '{"route":"ynmusic_playlist"}', 'ynmusic_main', '', 4),
('ynmusic_main_artists', 'ynmusic', 'Artists', '', '{"route":"ynmusic_artist"}', 'ynmusic_main', '', 5),
('ynmusic_main_manage_albums', 'ynmusic', 'My Music', 'Ynmusic_Plugin_Menus', '{"route":"ynmusic_album","action":"manage"}', 'ynmusic_main', '', 6),
('ynmusic_main_upload', 'ynmusic', 'Upload Songs', 'Ynmusic_Plugin_Menus', '{"route":"ynmusic_song","action":"upload"}', 'ynmusic_main', '', 7),
('ynmusic_main_faqs', 'ynmusic', 'FAQs', '', '{"route":"ynmusic_faqs"}', 'ynmusic_main', '', 8);

--
-- Table structure for table `engine4_ynmusic_songs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_songs` (
  `song_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `photo_id` int(11) DEFAULT '0',
  `cover_id` int(11) DEFAULT '0',
  `file_id` int(11) DEFAULT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `permalink` text NULL,
  `album_id` int(11) NOT NULL,
  `wave_play` int(11) NOT NULL,
  `wave_noplay` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `order` int(11) DEFAULT '999',
  `play_count` int(10) NOT NULL DEFAULT '0',
  `view_count` int(10) NOT NULL DEFAULT '0',
  `download_count` int(10) NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `downloadable` tinyint(1) NOT NULL DEFAULT '1',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `duration` int(11) DEFAULT '0',
  `import_id` int(11) NOT NULL DEFAULT '0',
  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL,
  `update_wave` tinyint(1) NOT NULL DEFAULT '0',
  `cover_top` int(11) NOT NULL DEFAULT '0',
  `composer` INT( 11 ) NOT NULL DEFAULT  '0',
  `search` TINYINT( 1 ) NOT NULL DEFAULT  '1',
  PRIMARY KEY (`song_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_albums`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_albums` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(11) DEFAULT '0',
  `cover_id` int(11) DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `released_date` datetime NULL,
  `play_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `download_count` int(11) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `import_id` int(11) NOT NULL DEFAULT '0',
  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL,
  `cover_top` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_artists`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_artists` (
  `artist_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  `cover_id` int(11) NOT NULL DEFAULT '0',
  `short_description` text NOT NULL,
  `description` text NOT NULL,
  `country` text NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '1',
  `search` tinyint(1) NOT NULL DEFAULT '0',
  `cover_top` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artist_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_artistmappings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_artistmappings` (
	`artistmapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`artist_id` int(11) NOT NULL,
	`item_type` text NOT NULL,
	`item_id` int(11) NOT NULL,
	PRIMARY KEY (`artistmapping_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	

--
-- Table structure for table `engine4_ynmusic_genremappings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_genremappings` (
	`genremapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`genre_id` int(11) NOT NULL,
	`item_type` text NOT NULL,
	`item_id` int(11) NOT NULL,
	PRIMARY KEY (`genremapping_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
--
-- Table structure for table `engine4_ynmusic_genres`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_genres` (
  `genre_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '1',
  `order` int(11) NOT NULL DEFAULT '999',
  PRIMARY KEY (`genre_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


--
-- Table structure for table `engine4_ynmusic_playlists`
--
CREATE TABLE IF NOT EXISTS `engine4_ynmusic_playlists` (
  `playlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `photo_id` int(11) DEFAULT '0',
  `cover_id` int(11) DEFAULT '0',
  `like_count` int(11) NOT NULL DEFAULT '0',
  `share_count` int(11) NOT NULL DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `play_count` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `import_id` int(11) NOT NULL DEFAULT '0',
  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL,
  `cover_top` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playlist_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_playlist_songs`
--
CREATE TABLE IF NOT EXISTS `engine4_ynmusic_playlist_songs` (
  `playlistsong_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) NOT NULL DEFAULT '0',
  `song_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`playlistsong_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_faqs` (
`faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`order` int(11) NOT NULL DEFAULT  '999',
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_alonesongs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_alonesongs` (
`alonesong_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`song_ids` text NOT NULL,
`user_id` int(11) unsigned NOT NULL,  
`creation_date` datetime NOT NULL,
PRIMARY KEY (`alonesong_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_history`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_history` (
`history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`item_type` varchar(128) NOT NULL,
`item_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY (`history_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynmusic_imports`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmusic_imports` (
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
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynmusic_album_create', 'ynmusic', '{item:$subject} created a new album: {item:$object}', 1, 5, 1, 1, 1, 1),
('ynmusic_album_addsong', 'ynmusic', '{item:$subject} upload {var:$count} new song(s) to album {item:$object}', 1, 5, 1, 1, 1, 1),
('ynmusic_song_addalonesongs', 'ynmusic', '{item:$subject} upload {var:$count} new song(s)', 1, 5, 1, 1, 1, 1),
('ynmusic_playlist_create', 'ynmusic', '{item:$subject} created a new playlist: {item:$object}', 1, 5, 1, 1, 1, 1);

-- set default authorization for member level settings

-- album
-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_album' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_album' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_album' as `type`,
    'auth_download' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'max_songs' as `name`,
	3 as `value`,
	20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'edit' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'delete' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'max_songs' as `name`,
	3 as `value`,
	20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'edit' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'delete' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_album' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- playlist
-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_playlist' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_playlist' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_playlist' as `type`,
    'auth_download' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'max_songs' as `name`,
	3 as `value`,
	20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'edit' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'delete' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'max_songs' as `name`,
	3 as `value`,
	20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'edit' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'delete' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_playlist' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- song
-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_song' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_song' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_song' as `type`,
    'auth_download' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'max_filesize' as `name`,
	3 as `value`,
	10240 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'max_storage' as `name`,
	3 as `value`,
	1048576 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'edit' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'delete' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'max_filesize' as `name`,
	3 as `value`,
	10240 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'max_storage' as `name`,
	3 as `value`,
	1048576 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'edit' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'delete' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_song' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- alonesong
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_alonesong' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_alonesong' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_alonesong' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES 
('Ynmusic Update Wave For Songs', 'ynmusic', 'Ynmusic_Plugin_Task_UpdateWaveForSongs', 3600, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('Social Music Migration', 'ynmusic_migration', 'ynmusic', 'Ynmusic_Plugin_Job_Migration', NULL, 1, 75, 2);