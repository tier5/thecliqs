-- change table permissions (change length of column type)
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- insert module
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynvideochannel', 'YN - Video Channel', 'YN - Video Channel', '4.01', 1, 'extra') ;

-- insert menus
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('ynvideochannel_main', 'standard', 'YN - Video Channel Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_main_ynvideochannel', 'ynvideochannel', 'Video Channel', '', '{"route":"ynvideochannel_general"}', 'core_main', '', 1, 0, 999),
('ynvideochannel_main_browse', 'ynvideochannel', 'All Videos', '', '{"route":"ynvideochannel_general"}', 'ynvideochannel_main', '', 1, 0, 1),
('ynvideochannel_main_channels', 'ynvideochannel', 'All Channels', '', '{"route":"ynvideochannel_general","action":"channels"}', 'ynvideochannel_main', '', 1, 0, 2),
('ynvideochannel_main_playlists', 'ynvideochannel', 'All Playlist', '', '{"route":"ynvideochannel_general","action":"playlists"}', 'ynvideochannel_main', '', 1, 0, 3),
('ynvideochannel_main_manage', 'ynvideochannel', 'My Items', 'Ynvideochannel_Plugin_Menus', '{"route":"ynvideochannel_general","action":"manage-videos"}', 'ynvideochannel_main', '', 1, 0, 4),
('ynvideochannel_main_subscriptions', 'ynvideochannel', 'Subscriptions', 'Ynvideochannel_Plugin_Menus', '{"route":"ynvideochannel_general","action":"subscriptions"}', 'ynvideochannel_main', '', 1, 0, 5),
('ynvideochannel_main_share_video', 'ynvideochannel', 'Share a Video', 'Ynvideochannel_Plugin_Menus::canShareVideos', '{"route":"ynvideochannel_general","action":"share-video"}', 'ynvideochannel_main', '', 1, 0, 6),
('ynvideochannel_main_add_channel', 'ynvideochannel', 'Add a Channel', 'Ynvideochannel_Plugin_Menus::canAddChannel', '{"route":"ynvideochannel_general","action":"add-channel"}', 'ynvideochannel_main', '', 1, 0, 7),
('ynvideochannel_main_add_playlist', 'ynvideochannel', 'Create New Playlist', 'Ynvideochannel_Plugin_Menus::canAddPlaylist', '{"route":"ynvideochannel_general","action":"create-playlist"}', 'ynvideochannel_main', '', 1, 0, 8),
('ynvideochannel_quick_sharevideo', 'ynvideochannel', 'Share a Video', 'Ynvideochannel_Plugin_Menus::canShareVideos', '{"route":"ynvideochannel_general","action":"share-video"}', 'ynvideochannel_create_quick', '',1, 0, 1),
('ynvideochannel_quick_addchannel', 'ynvideochannel', 'Add a Channel', 'Ynvideochannel_Plugin_Menus::canAddChannel', '{"route":"ynvideochannel_general","action":"add-channel"}', 'ynvideochannel_create_quick', '',1, 0, 2),
('ynvideochannel_quick_myvideos', 'ynvideochannel', 'My Videos', 'Ynvideochannel_Plugin_Menus::canShareVideos', '{"route":"ynvideochannel_general","action":"manage-videos"}', 'ynvideochannel_manage_quick', '',1, 0, 1),
('ynvideochannel_quick_favorites', 'ynvideochannel', 'Favorites', '', '{"route":"ynvideochannel_general","action":"favorites"}', 'ynvideochannel_manage_quick', '',1, 0, 2),
('ynvideochannel_quick_mychannels', 'ynvideochannel', 'My Channels', 'Ynvideochannel_Plugin_Menus::canAddChannel', '{"route":"ynvideochannel_general","action":"manage-channels"}', 'ynvideochannel_manage_quick', '',1, 0, 3),
('ynvideochannel_quick_myplaylists', 'ynvideochannel', 'My Playlists', 'Ynvideochannel_Plugin_Menus::canAddPlaylist', '{"route":"ynvideochannel_general","action":"manage-playlists"}', 'ynvideochannel_manage_quick', '',1, 0, 4),
('core_admin_main_plugins_ynvideochannel', 'ynvideochannel', 'YN - Video Channel', '', '{"route":"admin_default","module":"ynvideochannel","controller":"manage-videos"}', 'core_admin_main_plugins', '', 1, 0, 999),
('ynvideochannel_admin_main_settings', 'ynvideochannel', 'Global Settings', '', '{"route":"admin_default","module":"ynvideochannel","controller":"settings"}', 'ynvideochannel_admin_main', '', 1, 0, 1),
('ynvideochannel_admin_main_level', 'ynvideochannel', 'Member Level Settings', '', '{"route":"admin_default","module":"ynvideochannel","controller":"settings","action":"level"}', 'ynvideochannel_admin_main', '', 1, 0, 2),
('ynvideochannel_admin_main_managevideos', 'ynvideochannel', 'Manage Videos', '', '{"route":"admin_default","module":"ynvideochannel","controller":"manage-videos"}', 'ynvideochannel_admin_main', '', 1, 0, 3),
('ynvideochannel_admin_main_managechannels', 'ynvideochannel', 'Manage Channels', '', '{"route":"admin_default","module":"ynvideochannel","controller":"manage-channels"}', 'ynvideochannel_admin_main', '', 1, 0, 4),
('ynvideochannel_admin_main_manageplaylists', 'ynvideochannel', 'Manage Playlists', '', '{"route":"admin_default","module":"ynvideochannel","controller":"manage-playlists"}', 'ynvideochannel_admin_main', '', 1, 0, 5),
('ynvideochannel_admin_main_categories', 'ynvideochannel', 'Categories', '', '{"route":"admin_default","module":"ynvideochannel","controller":"categories"}', 'ynvideochannel_admin_main', '', 1, 0, 6);

-- create videos table
DROP TABLE IF EXISTS `engine4_ynvideochannel_videos`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_videos` (
  `video_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `owner_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `parent_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci default NULL,
  `parent_id` int(11) unsigned default NULL,
  `channel_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `favorite_count` int(11) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(11) unsigned NOT NULL default '0',
  `code` varchar(150) NOT NULL,
  `photo_id` int(11) unsigned default NULL,
  `rating` float NOT NULL,
  `category_id` int(11) unsigned NOT NULL default '0',
  `duration` int(9) unsigned NOT NULL,
  `is_featured` TINYINT(1) DEFAULT '0',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  PRIMARY KEY  (`video_id`),
  KEY `owner_id` (`owner_id`,`owner_type`),
  KEY `search` (`search`),
  KEY `creation_date` (`creation_date`),
  KEY `view_count` (`view_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- create video rating
DROP TABLE IF EXISTS `engine4_ynvideochannel_ratings`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (rating_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- create favourite table
DROP TABLE IF EXISTS `engine4_ynvideochannel_favorites`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_favorites` (
  `favorite_id` int(10) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `favorited` tinyint(1) unsigned NOT NULL default 1,
  PRIMARY KEY (`favorite_id`),
  UNIQUE KEY `video_id_user_id` (`video_id`,`user_id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_ynvideochannel_usershareds`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_usershareds` (
  `usershared_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `type` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`usershared_id`),
   KEY `item_id` (`item_id`),
   KEY `user_id` (`user_id`),
   KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `engine4_ynvideochannel_playlists`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_playlists` (
  `playlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(11) unsigned NOT NULL default '0',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `owner_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `video_count` int(11) NOT NULL DEFAULT '0',
  `view_mode` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playlist_id`),
  KEY `owner_id` (`owner_id`),
  KEY `creation_date` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `engine4_ynvideochannel_playlistvideos`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_playlistvideos` (
  `playlistvideo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) unsigned NOT NULL DEFAULT '0',
  `video_id` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `video_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`playlistvideo_id`),
  UNIQUE KEY `playlist_id_video_id` (`playlist_id`,`video_id`),
  KEY `creation_time` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `engine4_ynvideochannel_channels`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_channels` (
  `channel_id` int(11) unsigned NOT NULL auto_increment,
  `channel_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `owner_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `subscriber_count` int(11) unsigned NOT NULL default '0',
  `photo_id` int(11) unsigned default NULL,
  `cover_id` int(11) unsigned default NULL,
  `category_id` int(11) unsigned NOT NULL default '0',
  `video_count` int(11) NOT NULL DEFAULT '0',
  `is_featured` TINYINT(1) DEFAULT '0',
  `of_day` TINYINT(1) DEFAULT '0',
  `auto_update` TINYINT(1) DEFAULT '0',
  PRIMARY KEY  (`channel_id`),
  KEY `owner_id` (`owner_id`,`owner_type`),
  KEY `search` (`search`),
  KEY `creation_date` (`creation_date`),
  KEY `view_count` (`view_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- create subscribe table
DROP TABLE IF EXISTS `engine4_ynvideochannel_subscribes`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_subscribes` (
  `subscribe_id` int(11) unsigned NOT NULL auto_increment,
  `channel_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`subscribe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- create categories table

DROP TABLE IF EXISTS `engine4_ynvideochannel_categories`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `pleft` int(11) unsigned NOT NULL,
  `pright` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '99',
  `option_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table categories`
--

INSERT IGNORE INTO `engine4_ynvideochannel_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, NULL, 1, 36, 0, 'All Categories','1'),
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

-- Table structure for table `engine4_videochannel_video_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_video_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynvideochannel_video_fields_maps`
--

INSERT IGNORE INTO `engine4_ynvideochannel_video_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynvideochannel_video_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_video_fields_meta` (
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
-- Dumping data for table `engine4_ynvideochannel_video_fields_meta`
--

INSERT IGNORE INTO `engine4_ynvideochannel_video_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynvideochannel_video_fields_options`
--
DROP TABLE IF EXISTS `engine4_ynvideochannel_video_fields_options`;
CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_video_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynvideochannel_video_fields_options`
--

INSERT IGNORE INTO `engine4_ynvideochannel_video_fields_options` (`option_id`, `field_id`, `label`, `order`) VALUES
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

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynvideochannel_video_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_video_fields_search` (
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
-- Table structure for table `engine4_ynvideochannel_video_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynvideochannel_video_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table `engine4_activity_actiontypes`

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynvideochannel_video_new', 'ynvideochannel', '{item:$subject} shared a new video:', 1, 5, 1, 3, 1, 0),
('ynvideochannel_playlist_new', 'ynvideochannel', '{item:$subject} added a new playlist:', 1, 5, 1, 3, 1, 0),
('ynvideochannel_channel_new', 'ynvideochannel', '{item:$subject} added a new channel:', 1, 5, 1, 3, 1, 0),
('ynvideochannel_channel_video_new', 'ynvideochannel', '{item:$subject} added {var:$count} video(s) to the channel {item:$object}:', 1, 5, 1, 3, 1, 0),
('ynvideochannel_add_favorite', 'ynvideochannel', '{item:$subject} add a video to his/her favorite playlist', 1, 7, 1, 3, 1, 0),
('ynvideochannel_comment_video', 'ynvideochannel', '{item:$subject} commented on {item:$owner}\'s {item:$object:video}: {body:$body}', 1, 1, 1, 1, 1, 0),
('ynvideochannel_comment_playlist', 'ynvideochannel', '{item:$subject} commented on {item:$owner}\'s {item:$object:playlist}: {body:$body}', 1, 1, 1, 1, 1, 0),
('ynvideochannel_playlist_add_video', 'ynvideochannel', '{item:$subject} add a video to the playlist {item:$object}', 1, 5, 1, 3, 1, 0),
('ynvideochannel_add_video_new_playlist', 'ynvideochannel', '{item:$subject} created a new {item:$object:playlist} and added a video to this playlist', 1, 5, 1, 3, 1, 0);

-- Dumping data for table `engine4_activity_notificationtypes`
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynvideochannel_favorite_video', 'ynvideochannel', '{item:$subject} has favorited your {item:$object}.', 0, '', 1),
('ynvideochannel_send_video_to_friends', 'ynvideochannel', '{item:$subject} has sent to you this video {item:$object}.',0, '', 1),
('ynvideochannel_send_channel_to_friends', 'ynvideochannel', '{item:$subject} has sent to you this channel {item:$object}.',0, '', 1),
('ynvideochannel_add_video_to_channel', 'ynvideochannel', '{item:$subject} has added {var:$count} video(s) to the channel {item:$object}.',0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynvideochannel_favorite_video', 'ynvideochannel', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynvideochannel_send_video_to_friends', 'ynvideochannel', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynvideochannel_send_channel_to_friends', 'ynvideochannel', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynvideochannel_add_video_to_channel', 'ynvideochannel', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

--
-- TASK TO AUTO UPDATE CHANNESL
--
INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES
('Ynvideochannel Get Videos', 'ynvideochannel', 'Ynvideochannel_Plugin_Task_GetVideos', 43200, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- auth_view, auth_comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');


-- ADMIN
-- create, edit, delete, view, comment, max
-- video
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

  INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'Ynvideochannel_video' as `type`,
  'max' as `name`,
  3 as `value`,
  500 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- playlist
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'Ynvideochannel_playlist' as `type`,
  'max' as `name`,
  3 as `value`,
  500 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- channel
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'Ynvideochannel_channel' as `type`,
  'max' as `name`,
  3 as `value`,
  500 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


-- USER
-- create, edit, delete, view, comment, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

  INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'Ynvideochannel_video' as `type`,
  'max' as `name`,
  3 as `value`,
  200 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- playlist
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

  INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'Ynvideochannel_playlist' as `type`,
  'max' as `name`,
  3 as `value`,
  200 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- channel
    INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

  INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
  level_id as `level_id`,
  'Ynvideochannel_channel' as `type`,
  'max' as `name`,
  3 as `value`,
  200 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_video' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideochannel_channel' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- --------------------
