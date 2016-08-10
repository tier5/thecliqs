
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  
('yncontest', 'YN - Contest Plugin', 'Contest Plugin', '4.02p6', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_yncontest', 'yncontest', 'Contest', '', '{"route":"yncontest_general"}', 'core_main', '', 999),
('core_admin_main_plugins_yncontest', 'yncontest', 'YN - Contest', '', '{"route":"admin_default","module":"yncontest","controller":"manage"}', 'core_admin_main_plugins', '', 999),
('yncontest_admin_main_manage', 'yncontest', 'Manage Contest', '', '{"route":"admin_default","module":"yncontest","controller":"manage"}', 'yncontest_admin_main', '', 1),
('yncontest_admin_main_statistic', 'yncontest', 'Statistics', '', '{"route":"admin_default","module":"yncontest","controller":"statistic"}', 'yncontest_admin_main', '', 4),
('yncontest_admin_main_level', 'yncontest', 'Member Level Settings', '', '{"route":"admin_default","module":"yncontest","controller":"level"}', 'yncontest_admin_main', '', 5),
('yncontest_admin_main_settings', 'yncontest', 'Global Settings', '', '{"route":"admin_default","module":"yncontest","controller":"settings"}', 'yncontest_admin_main', '', 6),
('yncontest_admin_main_transactions', 'yncontest', 'Transactions', '', '{"route":"admin_default","module":"yncontest","controller":"transaction"}', 'yncontest_admin_main', '', 10),
('yncontest_main_contest', 'yncontest', 'All Contests', '', '{"route":"yncontest_general"}', 'yncontest_main', '', 1),
('yncontest_main_mycontests', 'yncontest', 'My Contests', 'Yncontest_Plugin_Menus::canMyContests', '{"route":"yncontest_mycontest"}', 'yncontest_main', '', 2),
('yncontest_main_myentries', 'yncontest', 'My Entries', 'Yncontest_Plugin_Menus::canMyEntries', '{"route":"yncontest_myentries"}', 'yncontest_main', '', 4),
('yncontest_main_create_contest', 'yncontest', 'Create New Contest', 'Yncontest_Plugin_Menus::canCreateContest', '{"route":"yncontest_mycontest","action":"create-contest"}', 'yncontest_main', '', 5)
;

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('yncontest_main', 'standard', 'Yncontest Main Navigation Menu');


-- --------------------------------------------------------

--
-- Table structure for table `engine4_yncontest_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_yncontest_orders` (
`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`gateway_id` int(11) unsigned NOT NULL,
`gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
`status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
`creation_date` datetime NOT NULL,
`payment_date` datetime DEFAULT NULL,
`item_id` int(11) unsigned NOT NULL DEFAULT '0',
`price` decimal(16,2) NOT NULL DEFAULT '0',
`currency` char(3),
`security_code` text NOT NULL,
PRIMARY KEY (`order_id`),
KEY `user_id` (`user_id`),
KEY `gateway_id` (`gateway_id`),
KEY `state` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `engine4_yncontest_contests`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_contests` (
  `contest_id` int(11) unsigned NOT NULL auto_increment,
  `contest_name` varchar(128) NOT NULL, 
  `contest_type` varchar(128) NOT NULL,
  `user_id` int(11) unsigned NOT NULL, 
  `photo_id` int(11) unsigned NOT NULL,
  `album_id` int(11) unsigned default '0',
  `featured_id` int(11) unsigned NOT NULL default '0',
  `premium_id` int(11) unsigned NOT NULL default '0', 
  `endingsoon_id` int(11) unsigned NOT NULL default '0',  
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `follow_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `favourite_count` int(11) unsigned NOT NULL default '0',
  `search` tinyint(1) unsigned NOT NULL default '1',  
  `approve_status` enum('approved','denied','new','pending') NOT NULL default 'new',  
  `contest_status` enum('close','denied','draft','published','waiting') NOT NULL default 'draft',    
  `description` text default NULL,  
  `condition` text default NULL, 
  `award` text default NULL,  
  `activated` tinyint(1) DEFAULT 1,
  `award_number` int(11) unsigned NOT NULL default '0',
  `vote_desc` varchar(128) , 
  `reason_desc` varchar(128),
  `winner_desc` text default NULL,   
  `creation_date` datetime  NULL,
  `start_date` datetime  NULL,
  `modified_date` datetime NOT NULL,  
  `end_date` datetime  NULL,  
  `approved_date` datetime  NULL, 
  `approval` tinyint(1) NOT NULL DEFAULT '0', 
  `click_count` int(11) unsigned default 0,
  `share_count` int(11) unsigned default 0, 
  `longitude` VARCHAR( 64 ) NULL DEFAULT NULL,
  `latitude` VARCHAR( 64 ) NULL DEFAULT NULL,
  `location` varchar(256) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`contest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `engine4_yncontest_entries`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_entries` (
  `entry_id` int(11) unsigned NOT NULL auto_increment,
  `contest_id` int(11) unsigned NOT NULL,
  `award_id` int(11) unsigned NOT NULL default '0',
  `entry_name` varchar(128) NOT NULL,
  `summary` text NOT NULL,  
  `entry_type` varchar(128) NOT NULL,
  `user_id` int(11) unsigned NOT NULL, 
  `item_id` int(11) unsigned NOT NULL,  
  `photo_id` int(11) unsigned ,  
  `content` text NOT NULL,  
  `type` int(11) unsigned,  
  `start_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,  
  `approved_date` DATETIME NULL DEFAULT NULL,
  `search` tinyint(1) unsigned NOT NULL default '1',  
  `view_count` int(11) unsigned NOT NULL default '0',
  `comment_count` int(11) unsigned NOT NULL default '0',
  `approve_status` enum('approved','denied','new','pending') NOT NULL default 'new',  
  `entry_status`   enum('denied','draft','published','win') NOT NULL default 'draft',   
  `activated` tinyint(1) DEFAULT 1, 
  `give_award_status`   int(11)  NOT NULL default '0',  
  `follow_count` int(11) unsigned NOT NULL default '0',
  `like_count` int(11) unsigned NOT NULL default '0',
  `favourite_count` int(11) unsigned NOT NULL default '0',
  `vote_count` int(11) unsigned NOT NULL default '0',
  `hidden` int(11) unsigned NOT NULL default '0',
  `waiting_win` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `engine4_yncontest_transactions`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_transactions` (
  `transaction_id` int(11) unsigned NOT NULL auto_increment,  
  `transaction_date` datetime DEFAULT NULL,
  `user_buyer` int(11) DEFAULT NULL,
  `user_seller` int(11) DEFAULT NULL,
  `contest_id` int(11) unsigned NOT NULL,
  `number` int(11) NOT NULL DEFAULT '1',
  `amount` decimal(11,2) DEFAULT NULL,
  `currency` VARCHAR( 10 ) NOT NULL,
  `transaction_status` enum('pending','success','failure') NOT NULL,
  `approve_status` enum('pending','approved','denied') NOT NULL,
  `option_service` TINYINT(1) NOT NULL DEFAULT '1',
  `params` text default null,
  `payment_type` varchar(128) default NULL,
  `security` varchar(128) default NULL,
  PRIMARY KEY  (`transaction_id`),
  KEY `user_buyer` (`user_buyer`),
  KEY `user_seller` (`user_seller`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `engine4_yncontest_currencies` (
  `code` varchar(10) NOT NULL,
  `name` varchar(64) NOT NULL,
  `symbol` varchar(50) NOT NULL,
  `status` enum('Enable','Disable') NOT NULL default 'Enable',
  `position` enum('Standard','Left','Right') NOT NULL default 'Standard',
  `precision` tinyint(4) unsigned NOT NULL default '2',
  `script` tinyint(64) default NULL,
  `format` varchar(64) default NULL,
  `display` enum('No Symbol','Use Symbol','Use Shortname','Use Name') NOT NULL default 'Use Symbol',
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



INSERT IGNORE INTO `engine4_yncontest_currencies` (`code`, `name`, `symbol`, `status`, `position`, `precision`, `script`, `format`, `display`) VALUES
('AUD', 'Australian Dollar', 'A$', 'Enable', 'Standard', 2, NULL, NULL, 'No Symbol'),
('BRL', 'Brazilian Real	', 'BRL', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('CAD', 'Canadian Dollar', 'C$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('CHF', 'Swiss Franc', 'CHF', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('CZK', 'Czech Koruna', 'CZK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('DKK', 'Danish Krone', 'DKK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('EUR', 'Euro', '&euro;', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('GBP', 'British Pound', '&pound;', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('HKD', 'Hong Kong Dollar', 'H$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('HUF', 'Hungarian Forint', 'HUF', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('ILS', 'Israeli New Shekel', 'ILS', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('JPY', 'Japanese Yen', '&yen;', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('MXN', 'Mexican Peso', 'MXN', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('MYR', 'Malaysian Ringgit', 'MYR', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('NOK', 'Norwegian Krone', 'NOK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('NZD', 'New Zealand Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('PHP', 'Philippine Peso', 'PHP', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('PLN', 'Polish Zloty', 'PLN', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('SEK', 'Swedish Krona', 'SEK', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('SGD', 'Singapore Dollar', '$', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('THB', 'Thai Baht', 'THB', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('TRY', 'Turkish Lira', 'TRY', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('TWD', 'New Taiwan Dollar', 'TWD', 'Enable', 'Standard', 2, NULL, NULL, 'Use Symbol'),
('USD', 'U.S. Dollar', '$', 'Enable', 'Standard', 1, NULL, NULL, 'Use Symbol');


DROP TABLE IF EXISTS `engine4_yncontest_settings`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_settings` (
  `setting_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `contest_id` int(11) unsigned NOT NULL,
  `comment` tinyint(1) unsigned NOT NULL default '1', 
  `comment_entries` tinyint(1) unsigned NOT NULL default '1',   
  `entries_approve` tinyint(1) unsigned NOT NULL default '1', 
  `post_send_email` tinyint(1) unsigned NOT NULL default '1',  
  `max_entries` int(11) unsigned default '0',
  `numbers_entries` int(11) unsigned default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,  
  
  PRIMARY KEY  (`setting_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `engine4_yncontest_announcements` (
  `announcement_id` int(11) unsigned NOT NULL auto_increment,
  `contest_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text NOT NULL,
  `start_date` datetime NOT NULL,
  `modified_date` datetime NULL,
  PRIMARY KEY  (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


CREATE TABLE IF NOT EXISTS `engine4_yncontest_mailtemplates` (
  `mailtemplate_id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(255) NOT NULL,
  `vars` varchar(255) NOT NULL,
  PRIMARY KEY  (`mailtemplate_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

INSERT IGNORE  INTO `engine4_yncontest_mailtemplates` (`mailtemplate_id`, `type`, `vars`) VALUES
(5, 'contest_denied',''),
(6, 'contest_approved',''),
(7, 'register_service',''),
(8, 'participate_participant',''),
(9, 'participate_organizer',''),
(11,'members_banned',''),
(12,'submit_entry',''),
(14,'get_awards',''),
(15,'expired_contest',''),
(17,'entry_approved',''),
(18,'entry_denied',''),
(19,'entry_edited','')
;


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES

('notify_contest_denied', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),

('notify_close_contest', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),

('notify_contest_approved', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),

('notify_new_participant', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),

('notify_submit_entry', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),

('notify_register_service', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]'),

('notify_entry_win_vote', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[vote_desc]'),


('notify_entry_denied', 'yncontest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message],[group_title]')

;

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES

('close_contest', 'yncontest', '{item:$subject} has just closed contest {item:$object}', 0, ''),
('close_contest_members', 'yncontest', '{item:$subject} has just closed contest {item:$object}', 0, ''),

('contest_invite', 'yncontest', '{item:$subject} has invited you to the contest {item:$object}.', 1, 'contest.widget.request-contest'),

('contest_denied', 'yncontest', '{item:$subject} has just denied your contest {item:$object}.', 0, ''),
('contest_edited', 'yncontest', '{item:$subject} has just edited contest {item:$object}.', 0, ''),
('contest_approve', 'yncontest', '{item:$subject} has requested to join the contest {item:$object}.', 0, ''),

('entry_win_vote', 'yncontest', 'Your entry {item:$subject} won in contest {item:$object}.', 0, ''),
('entry_win_vote_f', 'yncontest', 'Entry {item:$subject} won in contest {item:$object}.', 0, ''),

('entry_no_win_vote', 'yncontest', 'Your entry {item:$subject} has just lost award in contest {item:$object}.', 0, ''),
('entry_no_win_vote_f', 'yncontest', 'Entry {item:$subject} has just lost award in contest {item:$object}.', 0, ''),

('entry_denied', 'yncontest', 'Your entry {item:$subject} has just been denied in contest {item:$object}.', 0, ''),


('contest_accepted', 'yncontest', 'Your request to join the contest {item:$subject} has been approved.', 0, ''),

('submit_entry', 'yncontest', 'You have submited new entry in contest {item:$object}.', 0, ''),
('submit_entry_f', 'yncontest', '{item:$subject} has submited new entry in contest {item:$object}.', 0, ''),

('new_participant', 'yncontest', 'You have been new participant in contest {item:$object}.', 0, ''),
('new_participant_f', 'yncontest', 'You have new participant in contest {item:$object}.', 0, ''),

('new_vote', 'yncontest', '{item:$subject}  has just voted your entry {item:$object}.', 0, ''),


('contest_approved', 'yncontest', '{item:$subject} has just approved your contest {item:$object}', 0, ''),

('create_annoucement', 'yncontest', '{item:$subject} has just created new announcement contest {item:$object}', 0, ''),



('register_service', 'yncontest', 'You have successfully registered contest {item:$object}', 0, ''),




('contest_cancel_invite', 'yncontest' , 'The contest {item:$subject} invitation has been cancel, please contact contest owner for more information.',0,'')
;



INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'viewentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'createcontests' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'createentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'editcontests' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'editentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'deletecontests' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'deleteentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'voteentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yncontest_entry' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


 INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'or_edit_entries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'or_give_award' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'or_ban_user' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');  


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'viewentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'createcontests' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'createentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'editcontests' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'editentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'deletecontests' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'deleteentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'voteentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'yncontest_entry' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'or_edit_entries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'or_give_award' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'or_ban_user' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

 INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'contest' as `type`,
    'viewentries' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');



DROP TABLE IF EXISTS `engine4_yncontest_membership`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `resource_approved` tinyint(1) NOT NULL default '0',
  `user_approved` tinyint(1) NOT NULL default '0',
  `message` text NULL,
  `title` text NULL,
  `rejected_ignored` tinyint(1) NOT NULL DEFAULT '0',  
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`resource_id`, `user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

DROP TABLE IF EXISTS `engine4_yncontest_lists`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_lists` (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL default '',
  `user_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`list_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
  
DROP TABLE IF EXISTS `engine4_yncontest_follows`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_follows` (
  `follow_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `contest_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`follow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;  
DROP TABLE IF EXISTS `engine4_yncontest_favourites`; 
 CREATE TABLE IF NOT EXISTS `engine4_yncontest_favourites` (
  `favourite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `contest_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`favourite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `engine4_yncontest_entriesfavourites`; 
 CREATE TABLE IF NOT EXISTS `engine4_yncontest_entriesfavourites` (
  `favourite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `entry_id` int(11) unsigned NOT NULL DEFAULT '0',
  `contest_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`favourite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `engine4_yncontest_announcements`; 
CREATE TABLE IF NOT EXISTS `engine4_yncontest_announcements` (
  `announcement_id` int(11) unsigned NOT NULL auto_increment,
  `contest_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `start_date` datetime NOT NULL,
  `modified_date` datetime NULL,
  PRIMARY KEY  (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 DROP TABLE IF EXISTS `engine4_yncontest_votes`; 
 CREATE TABLE IF NOT EXISTS `engine4_yncontest_votes` (
  `vote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `entry_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `engine4_yncontest_listitems`;
CREATE TABLE IF NOT EXISTS `engine4_yncontest_listitems` (
  `listitem_id` int(11) unsigned NOT NULL auto_increment,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`listitem_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;





 
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('yncontest.mode',  '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('yncontest.print',  '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('yncontest.download',  '1');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('contest.page',  '12');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('yncontest.maxfeature',  '10');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('yncontest.endingsoonbefore',  '10');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('contest.entries.page',  '10');
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('contest.approval',  '0');

 INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('yncontest_new', 'yncontest', '{item:$subject} created a new contest', 1, 5, 1, 3, 1, 1),
('yncontest_update', 'yncontest', '{item:$subject} updated a contest', 1, 5, 1, 3, 1, 1),
('comment_yncontest', 'yncontest', '{item:$subject} commented on {item:$owner}''s {item:$object:Page}: {body:$body}', 1, 1, 1, 1, 1, 0);

   
INSERT IGNORE INTO `engine4_core_tasks` (`task_id`, `title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES 
(NULL, 'Contest Close', 'yncontest', 'Yncontest_Plugin_Task_Timeout', '3600', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0');


/* contest 402 /*
ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `start_date_submit_entries` datetime  NULL;
ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `end_date_submit_entries` datetime  NULL;

ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `start_date_vote_entries` datetime  NULL;
ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `end_date_vote_entries` datetime  NULL


ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `category_id` int(11) unsigned NOT NULL;

CREATE TABLE IF NOT EXISTS `engine4_yncontest_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `parent_category_id` int(11) unsigned NOT NULL default '0', 
  `level` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
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


/* contest 4.02*/
UPDATE `engine4_core_modules` SET `version` = '4.02' where 'name' = 'yncontest';

ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `start_date_submit_entries` datetime  NULL AFTER  `end_date`,
ADD COLUMN `end_date_submit_entries` datetime  NULL AFTER  `start_date_submit_entries`,
ADD COLUMN `start_date_vote_entries` datetime  NULL AFTER  `end_date_submit_entries`,
ADD COLUMN `end_date_vote_entries` datetime  NULL AFTER  `start_date_vote_entries`;

ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `category_id` int(11) unsigned NOT NULL;

CREATE TABLE IF NOT EXISTS `engine4_yncontest_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `parent_category_id` int(11) unsigned NOT NULL default '0', 
  `level` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
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

  
