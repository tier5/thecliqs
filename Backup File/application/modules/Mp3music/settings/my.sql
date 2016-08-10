ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `engine4_mp3music_albums` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_url` varchar(200) DEFAULT NULL,
  `composer` tinyint(1) NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `photo_id` int(11) DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `play_count` int(11) NOT NULL DEFAULT '0',
  `download_count` int(11) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_download` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`album_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `engine4_mp3music_album_songs` (
  `song_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_url` varchar(60) DEFAULT NULL,
  `album_id` int(11) NOT NULL,
  `filesize` int(10) DEFAULT '0',
  `url` varchar(100) DEFAULT NULL,
  `ext` varchar(10) DEFAULT NULL,
  `lyric` text,
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `artist_id` int(11) NOT NULL DEFAULT '0',
  `singer_id` int(11) NOT NULL DEFAULT '0',
  `other_singer` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `other_singer_title_url` varchar(100) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `comment_count` int(10) NOT NULL DEFAULT '0',
  `play_count` int(10) NOT NULL DEFAULT '0',
  `download_count` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`song_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_mp3music_cats` (
  `cat_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_url` varchar(60) DEFAULT NULL,
  `parent_cat` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_mp3music_cats` (`title`, `title_url`) VALUES
('Pop', 'pop'),
('Dance - Remix', 'dance remix'),
('Jazz', 'jazz'),
('Country', 'country'),
('Rap - Hip Hop', 'rap hip hop'),
('Rock', 'rock');

CREATE TABLE IF NOT EXISTS `engine4_mp3music_artists` (
  `artist_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artist_id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_mp3music_artists` (`artist_id`, `title`, `photo_id`) VALUES
(1, 'Black Eyed Peas', 0),
(2, 'Aaron Carter', 0),
(3, 'Avril Lavigne', 0),
(4, 'Celine Dion', 0);

CREATE TABLE IF NOT EXISTS `engine4_mp3music_playlists` (
  `playlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_url` varchar(200) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `photo_id` int(11) DEFAULT '0',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `is_download` tinyint(1) NOT NULL DEFAULT '1',
  `profile` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`playlist_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_mp3music_playlist_songs` (
  `song_id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) NOT NULL DEFAULT '0',
  `album_song_id` int(11) NOT NULL DEFAULT '0',
  `file_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`song_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_mp3music_singers` (
  `singer_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_url` varchar(60) DEFAULT NULL,
  `singer_type` int(11) NOT NULL DEFAULT '0',
  `photo_id` int(11) DEFAULT NULL,
  `play_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`singer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `engine4_mp3music_singers` (`singer_id`, `title`, `title_url`, `singer_type`, `photo_id`, `play_count`) VALUES
(1, 'Kelly Clarkson', 'kelly clarkson', 1, 0, 0),
(2, 'Jonas Brothers', 'jonas Brothers', 1, 0, 0),
(3, 'Katy Perry', 'katy perry', 1, 0, 0),
(4, 'Carrie Underwood', 'carrie underwood', 1, 0, 0),
(5, 'Britney Spears', 'britney spears', 1, 0, 0),
(6, 'Beyonce', 'beyonce', 1, 0, 0),
(7, 'Madonna', 'madonna', 1, 0, 0),
(8, 'Lady Gaga', 'lady gaga', 1, 0, 0);
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `engine4_mp3music_singer_types` (
  `singertype_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`singertype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `engine4_mp3music_singer_types` (`singertype_id`, `title`) VALUES
(1, 'Singer');
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `engine4_mp3music_ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(10) DEFAULT NULL,
  PRIMARY KEY (`rating_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_mp3music', 'mp3music', 'Mp3 Music', '', '{"route":"default","module":"mp3-music"}', 'core_main', '',  3),
('core_sitemap_mp3music', 'mp3music', 'Mp3 Music', '', '{"route":"default","module":"mp3-music"}', 'core_sitemap', '',  100),
('core_admin_main_plugins_mp3music', 'mp3music', 'YN - Mp3 Music', '', '{"route":"admin_default","module":"mp3-music","controller":"settings"}', 'core_admin_main_plugins', '',  999),
('mp3music_admin_main_manage', 'mp3music', 'Manage Albums', '', '{"route":"admin_default","module":"mp3-music","controller":"manage"}', 'mp3music_admin_main', '',  1),
('mp3music_admin_main_manageplaylist', 'mp3music', 'Manage Playlists', '', '{"route":"admin_default","module":"mp3-music","controller":"manageplaylist"}', 'mp3music_admin_main', '', 3),  
('mp3music_admin_main_managesong', 'mp3music', 'Manage Songs', '', '{"route":"admin_default","module":"mp3-music","controller":"managesong"}', 'mp3music_admin_main', '',  2),
('mp3music_admin_main_settings', 'mp3music', 'Global Settings', '', '{"route":"admin_default","module":"mp3-music","controller":"settings"}', 'mp3music_admin_main', '',  5),
('mp3music_admin_main_level', 'mp3music', 'Album Settings', '', '{"route":"admin_default","module":"mp3-music","controller":"level"}', 'mp3music_admin_main', '',  6),
('mp3music_admin_main_levelplaylist', 'mp3music', 'Playlist Settings', '', '{"route":"admin_default","module":"mp3-music","controller":"levelplaylist"}', 'mp3music_admin_main', '',  7),
('mp3music_admin_main_category', 'mp3music', 'Music Settings', '', '{"route":"admin_default","module":"mp3-music","controller":"category"}', 'mp3music_admin_main', '', 4);

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('mp3music', 'YN - Mp3 Music', 'This is module Mp3 Music.', '4.03p5', 1, 'extra') ;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('mp3music_playlist_new', 'mp3music', '{item:$subject} created a new playlist: {item:$object}', '1', '5', '1', '3', '1', 1),
('mp3comment_playlist',   'mp3music', '{item:$subject} commented on {item:$owner}''s {item:$object:mp3music_playlist}.', 1, 1, 1, 1, 1, 1),
('mp3music_album_new', 'mp3music', '{item:$subject} created a new album: {item:$object}', '1', '5', '1', '3', '1', 1),
('mp3comment_album',   'mp3music', '{item:$subject} commented on {item:$owner}''s {item:$object:mp3music_album}.', 1, 1, 1, 1, 1, 1);

INSERT IGNORE INTO `engine4_core_content` (`page_id`,`type`,`name`,`parent_content_id`,`order`,`params`) VALUES
(5,'widget','mp3music.profile-music',531,10,'{"title":"Mp3 Music","titleCount":true}'),
(5,'widget','mp3music.profile-player',510,5,'');


        --
-- Dumping data for table `engine4_authorization_permissions`
--
-- Playlist Permissions
-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'max_songs' as `name`,
    3 as `value`,
    30 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'max_playlists' as `name`,
    3 as `value`,
    10 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'max_songs' as `name`,
    3 as `value`,
    30 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'max_playlists' as `name`,
    3 as `value`,
    10 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
 
-- Album Permissions

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'is_download' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_songs' as `name`,
    3 as `value`,
    30 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_albums' as `name`,
    3 as `value`,
    10 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

   INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_filesize' as `name`,
    3 as `value`,
    10000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

   INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_storage' as `name`,
    3 as `value`,
    100000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'is_download' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_songs' as `name`,
    3 as `value`,
    30 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_albums' as `name`,
    3 as `value`,
    10 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

   INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_filesize' as `name`,
    3 as `value`,
    10000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

   INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'max_storage' as `name`,
    3 as `value`,
    100000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'mp3music_album' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
 

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'user_profile_subscribeMp3', 'mp3music', 'Subscribe Mp3 Music', 'Mp3music_Plugin_Menus', '', 'user_profile', '', 1, 0, 99);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('mp3music_subscribed_new', 'mp3music', '{item:$subject} has created a new album: {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_mp3music_subscribed_new', 'mp3music', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');


DROP TABLE IF EXISTS `engine4_mp3music_subscriptions`;
CREATE TABLE IF NOT EXISTS `engine4_mp3music_subscriptions` (
  `subscription_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `subscriber_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`subscription_id`),
  UNIQUE KEY `user_id` (`user_id`,`subscriber_user_id`),
  KEY `subscriber_user_id` (`subscriber_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;