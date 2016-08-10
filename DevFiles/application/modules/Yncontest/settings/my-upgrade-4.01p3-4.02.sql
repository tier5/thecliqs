UPDATE `engine4_core_modules` SET `version` = '4.02' where 'name' = 'yncontest';

CREATE TABLE IF NOT EXISTS `engine4_yncontest_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `parent_category_id` int(11) unsigned NOT NULL default '0', 
  `level` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT IGNORE INTO  `engine4_yncontest_categories` (`category_id`,`parent_category_id` ,`level` ,`name`)
VALUES ('1',  '0',  '1',  'Default');


/* add category contest end*/

/* contest album & photo*/
CREATE TABLE IF NOT EXISTS `engine4_yncontest_albums` (
  `album_id` int(11) unsigned NOT NULL auto_increment,
  `contest_id` int(11) unsigned NOT NULL,
  `title` varchar(128) character set utf8 collate utf8_unicode_ci NOT NULL,
  `description` varchar(255) character set utf8 collate utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `photo_id` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`album_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_photos` (
  `photo_id` int(11) unsigned NOT NULL auto_increment,
  `album_id` int(11) unsigned NOT NULL,
  `contest_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `slideshow` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`photo_id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'max_entries' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'max_entries' as `name`,
    0 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

ALTER TABLE `engine4_yncontest_contests`
ADD `activated` tinyint(1) DEFAULT 1;  

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('contest_activated', 'yncontest', '{item:$subject} has just activated your contest {item:$object}.', 0, ''),
('contest_inactivated', 'yncontest', '{item:$subject} has just inactivated your contest {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_contest_activated', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),
('notify_contest_inactivated', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]');

ALTER TABLE `engine4_yncontest_entries`
ADD `activated` tinyint(1) DEFAULT 1;  

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('entry_activated', 'yncontest', '{item:$subject} has just activated your entry {item:$object}.', 0, ''),
('entry_inactivated', 'yncontest', '{item:$subject} has just inactivated your entry {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_entry_activated', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),
('notify_entry_inactivated', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]');

