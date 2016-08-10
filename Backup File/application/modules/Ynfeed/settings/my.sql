INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynfeed', 'YN - Advanced Feed', 'This is advanced feed plugin.', '4.01', 1, 'extra') ;

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynfeed', 'ynfeed', 'YN - Advanced Feed', '', '{"route":"admin_default","module":"ynfeed","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('ynfeed_admin_main_settings', 'ynfeed', 'Global Settings', '', '{"route":"admin_default","module":"ynfeed","controller":"settings"}', 'ynfeed_admin_main', '', 1),
('ynfeed_admin_main_filters', 'ynfeed', 'Manage Filters', '', '{"route":"admin_default","module":"ynfeed","controller":"filters"}', 'ynfeed_admin_main', '', 2),
('ynfeed_admin_main_welcome', 'ynfeed', 'Manage Welcome Tab', '', '{"route":"admin_default","module":"ynfeed","controller":"welcome"}', 'ynfeed_admin_main', '', 3);

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_tags` (
`tag_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(11) UNSIGNED NOT NULL,
`action_id` int(11) UNSIGNED NOT NULL,
`item_type` varchar(128) NOT NULL,
`item_id` INTEGER UNSIGNED NOT NULL,
PRIMARY KEY (`tag_id`)
)
ENGINE = InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_hashtags` (
`hashtag_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(11) UNSIGNED NOT NULL,
`action_id` int(11) UNSIGNED NOT NULL,
`action_type` VARCHAR( 128 ) NOT NULL ,
`hashtag` varchar(128) NOT NULL,
PRIMARY KEY (`hashtag_id`)
)
ENGINE = InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_tagfriends` (
`tagfriend_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(11) UNSIGNED NOT NULL,
`action_id` int(11) UNSIGNED NOT NULL,
`friend_id` varchar(128) NOT NULL,
PRIMARY KEY (`tagfriend_id`)
)
ENGINE = InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynfeed_tag', 'ynfeed', '{item:$subject} tagged you in a {item:$object:post}.', 0, ''),
('follow_liked', 'ynfeed', '{item:$subject} likes {item:$object:post}.', 0, ''),
('follow_commented', 'ynfeed', '{item:$subject} has commented on {item:$object:post}.', 0, '');

--
-- Dumping data for table `engine4_core_mailtemplates`
--

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynfeed_tag', 'ynfeed', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_follow_liked', 'ynfeed', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_follow_commented', 'ynfeed', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_emoticons` (
`emoticon_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
`title` char(100) NOT NULL,
`text` varchar(200) NOT NULL,
`image` char(100) NOT NULL,
`ordering` smallint(4) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (`emoticon_id`),
UNIQUE KEY `text` (`text`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

--
-- Dumping data for table `engine4_ynfeed_emoticons`
--

INSERT IGNORE INTO `engine4_ynfeed_emoticons` (`emoticon_id`, `title`, `text`, `image`, `ordering`) VALUES
(1, 'angel', '(angel)', 'angel.gif', 0),
(2, 'angry', ':@', 'angry.gif', 0),
(3, 'bearhug', '(hug)', 'bearhug.gif', 0),
(4, 'beer', '(beer)', 'beer.gif', 0),
(5, 'blush', '(blush)', 'blush.gif', 0),
(6, 'bow', '(bow)', 'bow.gif', 0),
(7, 'boxing', '(punch)', 'boxing.gif', 0),
(8, 'brokenheart', '(u)', 'brokenheart.gif', 0),
(9, 'cake', '(^)', 'cake.gif', 0),
(10, 'callme', '(call)', 'callme.gif', 0),
(11, 'cash', '(cash)', 'cash.gif', 0),
(12, 'cellphone', '(mp)', 'cellphone.gif', 0),
(13, 'clapping', '(clap)', 'clapping.gif', 0),
(14, 'coffee', '(coffee)', 'coffee.gif', 0),
(15, 'cool', '8-)', 'cool.gif', 0),
(16, 'crying', ';(', 'crying.gif', 0),
(17, 'dance', '(dance)', 'dance.gif', 0),
(18, 'devil', '(devil)', 'devil.gif', 0),
(19, 'doh', '(doh)', 'doh.gif', 0),
(20, 'drink', '(d)', 'drink.gif', 0),
(21, 'dull', '|-(', 'dull.gif', 0),
(22, 'emo', '(emo)', 'emo.gif', 0),
(23, 'evilgrin', ']:)', 'evilgrin.gif', 0),
(24, 'flex', '(flex)', 'flex.gif', 0),
(25, 'flower', '(F)', 'flower.gif', 0),
(26, 'giggle', '(chuckle)', 'giggle.gif', 0),
(27, 'handshake', '(handshake)', 'handshake.gif', 0),
(28, 'happy', '(happy)', 'happy.gif', 0),
(29, 'heart', '(h)', 'heart.gif', 0),
(30, 'hi', '(wave)', 'hi.gif', 0),
(31, 'inlove', '(inlove)', 'inlove.gif', 0),
(32, 'itwasntme', '(wasntme)', 'itwasntme.gif', 0),
(33, 'jealous', '(envy)', 'jealous.gif', 0),
(34, 'kiss', ':*', 'kiss.gif', 0),
(35, 'laughing', ':D', 'laughing.gif', 0),
(36, 'mail', '(e)', 'mail.gif', 0),
(37, 'makeup', '(makeup)', 'makeup.gif', 0),
(38, 'mmm', '(mm)', 'mmm.gif', 0),
(39, 'music', '(music)', 'music.gif', 0),
(40, 'nerd', '8-|', 'nerd.gif', 0),
(41, 'no', '(n)', 'no.gif', 0),
(42, 'nod', '(nod)', 'nod.gif', 0),
(43, 'nospeak', ':x', 'nospeak.gif', 0),
(44, 'party', '(party)', 'party.gif', 0),
(45, 'puke', '(puke)', 'puke.gif', 0),
(46, 'rofl', '(rofl)', 'rofl.gif', 0),
(47, 'sad', ':(', 'sad.gif', 0),
(48, 'shakeno', '(shake)', 'shakeno.gif', 0),
(49, 'smile', ':)', 'smile.gif', 0),
(50, 'speechless', ':-|', 'speechless.gif', 0),
(51, 'sweating', '(sweat)', 'sweating.gif', 0),
(52, 'thinking', '(think)', 'thinking.gif', 0),
(53, 'tongue out', ':p', 'tongueout.gif', 0),
(54, 'wait', '(wait)', 'wait.gif', 0),
(55, 'whew', '(whew)', 'whew.gif', 0),
(56, 'wink', ';)', 'wink.gif', 0),
(57, 'worried', ':S', 'worried.gif', 0),
(58, 'yes', '(y)', 'yes.gif', 0);

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_maps` (
`map_id` int(11) NOT NULL AUTO_INCREMENT,
`action_id` int(11) UNSIGNED NOT NULL,
`title` text COLLATE utf8_unicode_ci,
`latitude` varchar(64),
`longitude` varchar(64),
`user_id` int(11) DEFAULT NULL,
`business_id` int(11) UNSIGNED NOT NULL,
PRIMARY KEY (`map_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_savefeeds` (
`user_id` INT( 11 ) NOT NULL ,
`action_type` VARCHAR( 128 ) NOT NULL ,
`action_id` INT( 11 ) NOT NULL ,
PRIMARY KEY ( `user_id` , `action_id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_optionfeeds` (
`user_id` INT( 11 ) NOT NULL ,
`action_type` VARCHAR( 128 ) NOT NULL ,
`action_id` INT( 11 ) NOT NULL ,
`type` VARCHAR( 64 ) NOT NULL,
`value` TINYINT( 1 ) NOT NULL DEFAULT '0',
PRIMARY KEY ( `user_id` , `action_id`, `type` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_hide` (
`user_id` INT( 11 ) NOT NULL ,
`hide_resource_type` VARCHAR( 128 ) NOT NULL ,
`hide_resource_id` INT( 11 ) NOT NULL ,
PRIMARY KEY ( `user_id` , `hide_resource_type`, `hide_resource_id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

-- Update action type to can add attachment when post status.
UPDATE  `engine4_activity_actiontypes` SET  `attachable` =  '1' WHERE `engine4_activity_actiontypes`.`type` =  'status';

-- ALL
-- auth_view
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynfeed_map' as `type`,
'auth_view' as `name`,
5 as `value`,
'["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynfeed_map' as `type`,
'view' as `name`,
2 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynfeed_map' as `type`,
'view' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynfeed_map' as `type`,
'view' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_contents` (
`content_id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`module_name` VARCHAR( 128 ) NOT NULL ,
`filter_type` VARCHAR( 128 ) NOT NULL ,
`resource_title` VARCHAR( 128 ) NOT NULL ,
`photo_id` INT( 11 ) NOT NULL DEFAULT '0',
`order` smallint(6) NOT NULL DEFAULT '99',
`default` TINYINT( 1 ) NOT NULL DEFAULT '0',
`show` TINYINT( 1 ) NOT NULL DEFAULT '1',
`content_tab` TINYINT( 1 ) NOT NULL DEFAULT '1',
PRIMARY KEY ( `content_id`),
KEY (`module_name`, `filter_type`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynfeed_contents` ( `module_name`, `filter_type`, `resource_title`, `photo_id`, `order`, `show`, `default`, `content_tab`) VALUES
('activity', 'all', 'All Updates', '0', '1', '1', '1', '1'),
('user', 'membership', 'Friends', '0', '2', '1', '1', '1'),
('activity', 'posts', 'Posts', '0', '3', '1', '1', '1'),
('user', 'member_list', 'Friends List', '0', '4', '1', '1', '0'),
('network', 'only_network', 'Networks', '0', '4', '1', '1', '0'),
('core', 'custom_list', 'Custom Lists', '0', '4', '1', '1', '0'),
('album', 'photo', 'Photos', '0', '5', '1', '0', '1'),
('advalbum', 'advalbum', 'Photos', '0', '5', '1', '0', '1'),
('blog', 'blog', 'Blogs', '0', '6', '1', '0', '1'),
('ynblog', 'ynblog', 'Blogs', '0', '6', '1', '0', '1'),
('video', 'video', 'Videos', '0', '7', '1', '0', '1'),
('ynvideo', 'ynvideo', 'Videos', '0', '7', '1', '0', '1'),
('event', 'event', 'Events', '0', '8', '1', '0', '1'),
('ynevent', 'ynevent', 'Events', '0', '8', '1', '0', '1'),
('group', 'group', 'Groups', '0', '9', '1', '0', '1'),
('advgroup', 'advgroup', 'Groups', '0', '9', '1', '0', '1'),
('music', 'music', 'Music', '0', '10', '1', '0', '1'),
('mp3music', 'mp3music', 'Mp3 Music', '0', '11', '1', '0', '1'),
('forum', 'forum', 'Forums', '0', '12', '1', '0', '1'),
('ynforum', 'ynforum', 'Forums', '0', '12', '1', '0', '1'),
('ynfeed', 'user_saved', 'Saved Feeds', '0', '13', '1', '1', '1'),
('ynfeed', 'user_follow', 'Following Feeds', '0', '14', '1', '1', '1'),
('socialstream', 'facebook_feeds', 'Facebook Feeds', '0', '15', '1', '1', '1'),
('socialstream', 'linkedin_feeds', 'LinkedIn Feeds', '0', '16', '1', '1', '1'),
('socialstream', 'twitter_feeds', 'Twitter Feeds', '0', '17', '1', '1', '1'),
('ynvideochannel', 'ynvideochannel', 'Video Channel', '0', '18', '1', '0', '1'),
('ynmusic', 'ynmusic', 'Social Music', '0', '19', '1', '0', '1'),
('ynultimatevideo', 'ynultimatevideo', 'Ultimate Video', '0', '20', '1', '0', '1'),
('ynlistings', 'ynlistings', 'Listings', '0', '21', '1', '0', '1'),
('ynmultilisting', 'ynmultilisting', 'Multiple Listings', '0', '22', '1', '0', '1'),
('ynfeedback', 'ynfeedback', 'Feedback', '0', '23', '1', '0', '1'),
('ynbusinesspages', 'ynbusinesspages', 'Business Pages', '0', '24', '1', '0', '1'),
('ynwiki', 'ynwiki', 'Wiki', '0', '25', '1', '0', '1'),
('ynidea', 'ynidea', 'Ideas', '0', '26', '1', '0', '1'),
('socialstore', 'socialstore', 'Store', '0', '27', '1', '0', '1'),
('groupbuy', 'groupbuy', 'Group Buy', '0', '28', '1', '0', '1'),
('ynfundraising', 'ynfundraising', 'Fundraising', '0', '29', '1', '0', '1'),
('ynauction', 'ynauction', 'Auction', '0', '30', '1', '0', '1');

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_customtypes` (
`customtype_id` INT( 11 ) NOT NULL AUTO_INCREMENT,
`module_name` VARCHAR( 128 ) NOT NULL ,
`resource_type` VARCHAR( 128 ) NOT NULL ,
`resource_title` VARCHAR( 128 ) NOT NULL ,
`default` TINYINT( 1 ) NOT NULL DEFAULT '0',
`order` smallint(6) NOT NULL DEFAULT '99',
`enabled` TINYINT( 1 ) NOT NULL DEFAULT '1',
PRIMARY KEY ( `customtype_id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynfeed_customtypes` ( `module_name`, `resource_type`, `resource_title`, `enabled`, `default`) VALUES 
('user', 'user', 'Friends', '1', '1');

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_lists` (
  `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_listitems` (
  `listitem_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(10) unsigned NOT NULL,
  `child_id` int(10) unsigned NOT NULL,
  `child_type` varchar(128) NOT NULL,
  PRIMARY KEY (`listitem_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `engine4_ynfeed_welcomes` (
  `welcome_id` INT( 11 ) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `display_limit` TINYINT(1) unsigned NOT NULL,
  `number_of_limit` int(11) unsigned NOT NULL,
  `networks` text COLLATE utf8_unicode_ci,
  `member_levels` text COLLATE utf8_unicode_ci,
  `enabled_contact` TINYINT(1) unsigned NOT NULL,
  `enabled_friend` TINYINT(1) unsigned NOT NULL,
  `number_of_friend` int(11) unsigned NOT NULL,
  `enabled_search_fr` TINYINT(1) unsigned NOT NULL,
  `enabled_member_sug` TINYINT(1) unsigned NOT NULL,
  `number_of_member` int(11) unsigned NOT NULL,
  `enabled_group_sug` TINYINT(1) unsigned NOT NULL,
  `number_of_group` int(11) unsigned NOT NULL,
  `enabled_event_sug` TINYINT(1) unsigned NOT NULL,
  `number_of_event` int(11) unsigned NOT NULL,
  `enabled_most_like` TINYINT(1) unsigned NOT NULL,
  `number_of_like` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `show` TINYINT(1) unsigned NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '99',
  PRIMARY KEY ( `welcome_id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ADMIN
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'announcement' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('admin');

-- MODERATOR, USER, PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'announcement' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'user', 'public');
