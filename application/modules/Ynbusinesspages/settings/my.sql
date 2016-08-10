-- change table permissions (change length of column type)
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_authorization_permissions` MODIFY `name` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;


--
-- Table structure for table `engine4_ynbusinesspages_receivers`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_receivers` (
  `receiver_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) unsigned NOT NULL,
  `department` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`receiver_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- Contact customed fields
CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_contacts` (
  `contact_id` INT(11) NOT NULL AUTO_INCREMENT,
  `business_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `form_description` TEXT COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_contact_fields_meta` (
  `field_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id` INT(11) UNSIGNED NOT NULL,
  `type` VARCHAR(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `required` TINYINT(1) NOT NULL DEFAULT '0',
  `enabled` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`field_id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_contact_fields_options` (
  `option_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` INT(11) UNSIGNED NOT NULL,
  `label` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


--
-- Table structure for table `engine4_ynbusinesspages_checkin`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_checkin` (
  `checkin_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` int(11) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`checkin_id`),
  KEY(`business_id`, `user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynbusinesspages_reviews`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `rate_number` smallint(5) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`review_id`),
  KEY `business_id` (`business_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynbusinesspages_announcement_marks`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_announcement_marks` (
  `mark_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`mark_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynbusinesspages_announcements`
--
CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_announcements` (
  `announcement_id` int(11) unsigned NOT NULL auto_increment,
  `business_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NULL,
  PRIMARY KEY  (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynbusinesspages_membership`
--
CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `resource_approved` tinyint(1) NOT NULL default '0',
  `user_approved` tinyint(1) NOT NULL default '0',
  `message` text NULL,
  `title` text NULL,
  `list_id` int(11) unsigned NOT NULL default '0',
  `actived_date` datetime NULL,
  PRIMARY KEY  (`resource_id`, `user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynbusinesspages_renewals`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_renewals` (
`renewal_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`time` VARCHAR(16) NOT NULL,
`notified` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`renewal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_claimrequests`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_claimrequests` (
`claimrequest_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`approve_date` datetime DEFAULT NULL,
`status` enum('pending','approved','denied') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
PRIMARY KEY (`claimrequest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_covers`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_covers` (
`cover_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`photo_id` int(11) unsigned NOT NULL,
`order` int(3) unsigned,
PRIMARY KEY (`cover_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_lists`
--
CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_lists` (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `owner_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `child_count` int(11) unsigned NOT NULL default '0',
  `privacy` text default '',
  `can_delete` tinyint(1) unsigned NOT NULL default '1',
  `can_edit` tinyint(1) unsigned NOT NULL default '1',
  `follow` int(11),
  `type` enum('admin','member','registered','non-registered','custom') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'custom',
  PRIMARY KEY  (`list_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_listitems`
--
CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_listitems` (
  `listitem_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`listitem_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_modulesettings`
--
CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_modulesettings` (
  `modulesetting_id` int(11) unsigned NOT NULL auto_increment,
  `module_name` varchar(64) NOT NULL,
  `key` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `edit_or_delete` tinyint(1) unsigned NOT NULL default '0',
  `admin` tinyint(1) unsigned NOT NULL default '1',
  `member` tinyint(1) unsigned NOT NULL default '1',
  `registered` tinyint(1) unsigned NOT NULL default '0',
  `non-registered` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`modulesetting_id`),
  KEY(`module_name`, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynbusinesspages_modulesettings` (`module_name`, `key`, `title`, `edit_or_delete`, `admin`, `member`, `registered`, `non-registered`) VALUES
('ynbusinesspages', 'view', 'View Business', '0', 1, 1, 1, 1),
('ynbusinesspages', 'comment', 'Comment Business', '0', 1, 1, 0, 0),
('ynbusinesspages', 'edit', 'Edit Business', '0', 1, 0, 0, 0),
('ynbusinesspages', 'invite', 'Invite Members', '0', 1, 0, 0, 0),
('ynbusinesspages', 'approve', 'Approve Members', '0', 1, 0, 0, 0),

('album', 'album_create', 'Create Albums', '0', 1, 1, 0, 0),
('album', 'album_delete', 'Delete Albums to Business', '1', 1, 1, 0, 0),

('advalbum', 'album_create', 'Create Albums', '0', 1, 1, 0, 0),
('advalbum', 'album_delete', 'Delete Albums to Business', '1', 1, 1, 0, 0),

('event', 'event_create', 'Create Events', '0', 1, 1, 0, 0),
('event', 'event_delete', 'Delete Events to Business', '1', 1, 1, 0, 0),

('ynevent', 'event_create', 'Create Events', '0', 1, 1, 0, 0),
('ynevent', 'event_delete', 'Delete Events to Business', '1', 1, 1, 0, 0),

('poll', 'poll_create', 'Create Polls', '0', 1, 1, 0, 0),
('poll', 'poll_delete', 'Delete Polls to Business', '1', 1, 1, 0, 0),

('video', 'video_create', 'Create Videos', '0', 1, 1, 0, 0),
('video', 'video_delete', 'Delete Videos to Business', '1', 1, 1, 0, 0),

('ynvideo', 'video_create', 'Create Videos', '0', 1, 1, 0, 0),
('ynvideo', 'video_delete', 'Delete Videos to Business', '1', 1, 1, 0, 0),

('music', 'music_create', 'Create Musics', '0', 1, 1, 0, 0),
('music', 'music_delete', 'Delete Musics to Business', '1', 1, 1, 0, 0),

('wiki', 'wiki_create', 'Create Wikis', '0', 1, 1, 0, 0),
('wiki', 'wiki_delete', 'Delete Wikis to Business', '1', 1, 1, 0, 0),

('ynfilesharing', 'file_create', 'Create Files', '0', 1, 1, 0, 0),
('ynfilesharing', 'file_delete', 'Delete Files to Business', '1', 1, 1, 0, 0),

('classified', 'classified_create', 'Create Classifieds', '0', 1, 1, 0, 0),
('classified', 'classified_delete', 'Delete Classifieds to Business', '1', 1, 1, 0, 0),

('groupbuy', 'deal_create', 'Create Deals', '0', 1, 1, 0, 0),
('groupbuy', 'deal_delete', 'Delete Deals to Business', '1', 1, 1, 0, 0),

('ynbusinesspages', 'discussion_create', 'Create Discussions', '0', 1, 1, 0, 0),
('ynbusinesspages', 'discussion_delete', 'Delete Discussions', '1', 1, 1, 0, 0),

('ynlistings', 'listing_create', 'Create Listings', '0', 1, 1, 0, 0),
('ynlistings', 'listing_delete', 'Delete Listings to Business', '1', 1, 1, 0, 0),

('blog', 'blog_create', 'Create Blogs', '0', 1, 1, 0, 0),
('blog', 'blog_delete', 'Delete Blogs to Business', '1', 1, 1, 0, 0),

('ynblog', 'blog_create', 'Create Blogs', '0', 1, 1, 0, 0),
('ynblog', 'blog_delete', 'Delete Blogs to Business', '1', 1, 1, 0, 0),

('yncontest', 'contest_create', 'Create Contests', '0', 1, 1, 0, 0),
('yncontest', 'contest_delete', 'Delete Contests to Business', '1', 1, 1, 0, 0),

('ynjobposting', 'job_import', 'Import Jobs', '0', 1, 1, 0, 0),
('ynjobposting', 'job_delete', 'Delete Jobs to Business', '1', 1, 1, 0, 0),

('ynbusinesspages', 'view_dashboard', 'View Dashboard', '0', 1, 0, 0, 0),
('ynbusinesspages', 'manage_cover', 'Manage Cover Photos', '0', 1, 0, 0, 0),
('ynbusinesspages', 'manage_page', 'Manage Pages', '0', 1, 0, 0, 0),
('ynbusinesspages', 'manage_role', 'Manage Member Roles', '0', 1, 0, 0, 0),
('ynbusinesspages', 'manage_rolesetting', 'Configure Settings For Member Roles', '0', 1, 0, 0, 0),
('ynbusinesspages', 'manage_announcement', 'Manage Announcements', '0', 1, 0, 0, 0),
('ynbusinesspages', 'manage_module', 'Manage Modules', '0', 1, 0, 0, 0),
('ynbusinesspages', 'change_theme', 'Change Business Theme', '0', 1, 0, 0, 0),
('ynbusinesspages', 'update_package', 'Update Package', '0', 1, 0, 0, 0),
('ynbusinesspages', 'feature_business', 'Feature Business', '0', 1, 0, 0, 0),
('ynbusinesspages', 'login_business', 'Login as Business', '0', 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_transactions` (
`transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`payment_transaction_id` varchar(128),
`creation_date` datetime NOT NULL,
`status` enum('initialized','expired','pending','completed','canceled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`description` text NOT NULL,
`item_id` int(11) NULL,
`gateway_id` int(11) NOT NULL,
`amount` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`user_id` int(11) NOT NULL,
PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_orders` (
`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`gateway_id` int(11) unsigned NOT NULL,
`gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
`status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
`creation_date` datetime NOT NULL,
`payment_date` datetime DEFAULT NULL,
`package_id` int(11) unsigned NOT NULL DEFAULT '0',
`item_id` int(11) unsigned NOT NULL DEFAULT '0',
`price` decimal(16,2) NOT NULL DEFAULT '0',
`featured` tinyint(1) NOT NULL DEFAULT '0',
`feature_day_number` int(11) unsigned NOT NULL DEFAULT '0',
`currency` char(3),
PRIMARY KEY (`order_id`),
KEY `user_id` (`user_id`),
KEY `gateway_id` (`gateway_id`),
KEY `state` (`status`),
KEY `package_id` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_founders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_founders` (
`founder_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`name` text COLLATE utf8_unicode_ci NULL,
`user_id` int(11) unsigned NULL,
PRIMARY KEY (`founder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_locations`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_locations` (
`location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`title` text COLLATE utf8_unicode_ci NULL,
`location` text COLLATE utf8_unicode_ci NOT NULL,
`longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`main` boolean NOT NULL DEFAULT 0,
PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_operatinghours`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_operatinghours` (
`operatinghour_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`day` text  NULL,
`from` text  NULL,
`to` text  NULL,
PRIMARY KEY (`operatinghour_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_businessinfos`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_businessinfos` (
`businessinfo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`header` text  NULL,
`content` text  NULL,
`business_id` int(11) unsigned NOT NULL,
PRIMARY KEY (`businessinfo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_packagemodules`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_packagemodules` (
`packagemodule_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`package_id` int(11) unsigned NOT NULL,
`module_id` int(11) unsigned NOT NULL,
PRIMARY KEY (`packagemodule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_modules`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_modules` (
`module_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` text NOT NULL,
`item_type` VARCHAR(128) NOT NULL,
PRIMARY KEY (`module_id`),
UNIQUE KEY(`item_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynbusinesspages_modules`
--

INSERT IGNORE INTO `engine4_ynbusinesspages_modules` (`title`, `item_type`) VALUES
('Members', 'user'),
('Albums', 'ynbusinesspages_album'),
('Events', 'event'),
('Videos', 'video'),
('Polls', 'poll'),
('Music', 'music_playlist'),
('Mp3 Music', 'mp3music_album'),
('Discussions', 'ynbusinesspages_topic'),
('File Sharing', 'ynfilesharing_folder'),
('Wiki', 'ynwiki_page'),
('Classified', 'classified'),
('Group Buy', 'groupbuy_deal'),
('Contest', 'yncontest_contest'),
('Blogs', 'blog'),
('Listings', 'ynlistings_listing'),
('Job Posting', 'ynjobposting_job'),
('Social Music', 'ynmusic_song'),
('Ultimate Video', 'ynultimatevideo_video');

-- --------------------------------------------------------

-- Table structure for table `engine4_ynbusinesspages_business`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_business` (
`business_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`package_id` int(11) unsigned NULL,
`theme` text NOT NULL,
`name` text NOT NULL,
`short_description` text NOT NULL,
`description` text NOT NULL,
`search` tinyint(1) NOT NULL default '1',
`approval` tinyint(1) NOT NULL default '0',
`rating` float NOT NULL default '0',
`size` int(11) unsigned NULL,
`phone` text DEFAULT NULL,
`fax` text DEFAULT NULL,
`email` text NOT NULL,
`country` text NOT NULL,
`city` text DEFAULT NULL,
`province` text DEFAULT NULL,
`zip_code` text DEFAULT NULL,
`web_address` text DEFAULT NULL,
`facebook_link` text DEFAULT NULL,
`twitter_link` text DEFAULT NULL,
`timezone` text DEFAULT NULL,
`photo_id` int(11) UNSIGNED DEFAULT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime DEFAULT NULL,
`deleted` boolean NOT NULL DEFAULT 0,
`status` enum('draft', 'pending', 'published', 'closed', 'denied', 'unclaimed', 'claimed', 'deleted', 'expired') NOT NULL DEFAULT 'published',
`approved` boolean NOT NULL DEFAULT 0,
`featured` boolean NOT NULL DEFAULT 0,
`is_claimed` boolean NOT NULL DEFAULT 0,
`last_payment_date` datetime DEFAULT NULL,
`approved_date` datetime DEFAULT NULL,
`expiration_date` datetime DEFAULT NULL,
`like_count` int(11) NOT NULL DEFAULT 0,
`view_count` int(11) NOT NULL DEFAULT 0,
`follow_count` int(11) NOT NULL DEFAULT 0,
`comment_count` int(11) NOT NULL DEFAULT 0,
`review_count` int(11) NOT NULL DEFAULT 0,
`checkin_count` int(11) NOT NULL DEFAULT 0,
`topic_count` int(11) NOT NULL DEFAULT 0,
`never_expire` tinyint(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`business_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_packages`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_packages` (
`package_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(128) NOT NULL,
`price` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`valid_amount` int(11) unsigned,
`valid_period` ENUM('day') NOT NULL,
`description` text,
`show` tinyint(1) NOT NULL DEFAULT '1',
`current` tinyint(1) NOT NULL DEFAULT '1',
`deleted` tinyint(1) NOT NULL DEFAULT '0',
`user_id` int(11) NOT NULL,
`themes` text,
`max_cover` int(11) NOT NULL,
`category_id` text NOT NULL,
`allow_owner_manage_page` boolean NOT NULL DEFAULT 0,
`allow_user_join_business` boolean NOT NULL DEFAULT 0,
`allow_user_share_business` boolean NOT NULL DEFAULT 0,
`allow_user_invite_friend` boolean NOT NULL DEFAULT 0,
`allow_owner_add_contactform` boolean NOT NULL DEFAULT 0,
`allow_owner_add_customfield` boolean NOT NULL DEFAULT 0,
`allow_bussiness_multiple_admin` boolean NOT NULL DEFAULT 0,
`order` smallint(6) NOT NULL DEFAULT '0',
PRIMARY KEY (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_mailtemplates`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_mailtemplates` (
`mailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`type` varchar(255) NOT NULL,
`vars` varchar(255) NOT NULL,
PRIMARY KEY (`mailtemplate_id`),
UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `engine4_ynbusinesspages_mailtemplates`
--

INSERT IGNORE INTO `engine4_ynbusinesspages_mailtemplates` (`type`, `vars`) VALUES
('ynbusinesspages_claim_success', '[website_name],[website_link],[business_link],[business_name]'),
('ynbusinesspages_claim_approved', '[website_name],[website_link],[business_link],[business_name]'),
('ynbusinesspages_business_approved', '[website_name],[website_link],[business_link],[business_name]'),
('ynbusinesspages_business_created', '[website_name],[website_link],[business_link],[business_name]'),
('ynbusinesspages_package_changed', '[website_name],[website_link],[business_name]')
;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_category_business_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_category_business_maps` (
`categorymap_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL,
`main` boolean NOT NULL DEFAULT 0,
PRIMARY KEY (`categorymap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_business_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_business_fields_maps` (
`field_id` int(11) unsigned NOT NULL,
`option_id` int(11) unsigned NOT NULL,
`child_id` int(11) unsigned NOT NULL,
`order` smallint(6) NOT NULL,
PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynbusinesspages_business_fields_maps`
--

INSERT IGNORE INTO `engine4_ynbusinesspages_business_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_business_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_business_fields_meta` (
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
-- Dumping data for table `engine4_ynbusinesspages_business_fields_meta`
--

INSERT IGNORE INTO `engine4_ynbusinesspages_business_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_business_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_business_fields_search` (
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
-- Table structure for table `engine4_ynbusinesspages_business_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_business_fields_values` (
`item_id` int(11) unsigned NOT NULL,
`field_id` int(11) unsigned NOT NULL,
`index` smallint(3) unsigned NOT NULL DEFAULT '0',
`value` text COLLATE utf8_unicode_ci NOT NULL,
`privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_business_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_business_fields_options` (
`option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`field_id` int(11) unsigned NOT NULL,
`label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`order` smallint(6) NOT NULL DEFAULT '999',
PRIMARY KEY (`option_id`),
KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynbusinesspages_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_categories` (
`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`parent_id` int(11) unsigned DEFAULT NULL,
`pleft` int(11) unsigned NOT NULL,
`pright` int(11) unsigned NOT NULL,
`level` int(11) unsigned NOT NULL DEFAULT '0',
`title` varchar(64) NOT NULL,
`description` text NULL,
`photo_id` int(11) DEFAULT '0',
`order` smallint(6) NOT NULL DEFAULT '0',
`option_id` int(11) NOT NULL,
PRIMARY KEY (`category_id`),
KEY `user_id` (`user_id`),
KEY `parent_id` (`parent_id`),
KEY `pleft` (`pleft`),
KEY `pright` (`pright`),
KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynbusinesspages_categories`
--

INSERT IGNORE INTO `engine4_ynbusinesspages_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Categories','1');

--
-- Table structure for table `engine4_ynbusinesspages_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_faqs` (
`faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`order` int(11) NOT NULL,
`creation_date` datetime NOT NULL,
PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynbusinesspages_creators`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_creators` (
`creator_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
PRIMARY KEY (`creator_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynbusinesspages_comparisonfields`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_comparisonfields` (
`comparisonfield_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`type`  varchar(64) NOT NULL default 'header',
`show` boolean NOT NULL default true,
`order` int(11) NOT NULL default 999,
`creation_date` datetime NOT NULL,
PRIMARY KEY (`comparisonfield_id`),
UNIQUE KEY (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `engine4_ynbusinesspages_comparisonfields`
--

INSERT IGNORE INTO `engine4_ynbusinesspages_comparisonfields` (`title`, `type`, `show`, `order`) VALUES
('Ratings', 'rating', 1, 1),
('Members', 'memberCount', 1, 2),
('Followers', 'followerCount', 1, 3),
('Reviews', 'review', 1, 4),
('Contact Detail', 'contact', 1, 5),
('Address', 'address', 1, 6),
('Operating Hours', 'operatingHour', 1, 7),
('Short Description', 'shortDescription', 1, 8),
('Custom Fields', 'customField', 1, 9);

--
-- Table structure for table `engine4_ynbusinesspages_features`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_features` (
`feature_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
`modified_date` datetime DEFAULT NULL,
`expiration_date` datetime DEFAULT NULL,
PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynbusinesspages_claim_success', 'ynbusinesspages', 'The claim request for business {item:$object} has just been claimed successfully. Awaiting approval.', 0, ''),
('ynbusinesspages_claim_approved', 'ynbusinesspages', 'The claim request for business {item:$object} has just been approved.', 0, ''),
('ynbusinesspages_business_approved', 'ynbusinesspages', 'The business {item:$object} has just been approved.', 0, ''),
('ynbusinesspages_business_featured', 'ynbusinesspages', 'The business {item:$object} has just been featured.', 0, ''),
('ynbusinesspages_business_unfeatured', 'ynbusinesspages', 'The business {item:$object} has just been unfeatured.', 0, ''),
('ynbusinesspages_discussion_reply', 'ynbusinesspages', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::business topic} you posted on.', 0, ''),
('ynbusinesspages_discussion_response', 'ynbusinesspages', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::business topic} you created.', 0, ''),
('ynbusinesspages_follow_business', 'ynbusinesspages', '{item:$subject} has added {var:$type} on a {item:$object} you followed.', 0, ''),
('ynbusinesspages_invite', 'ynbusinesspages', '{item:$subject} has invited you to the business {item:$object}.', 1, ''),
('ynbusinesspages_invite_message', 'ynbusinesspages', '{item:$subject} has invited you to the business {item:$object}.', 1, ''),
('ynbusinesspages_transfer_owner', 'ynbusinesspages', '{item:$subject} has became the owner of the business {item:$object}.', 0, ''),
('ynbusinesspages_business_add_review', 'ynbusinesspages', 'Your business {item:$object} has a new review.', 0, ''),
('ynbusinesspages_business_expired', 'ynbusinesspages', 'The business {item:$object} has just been expired.', 0, ''),
('ynbusinesspages_business_never_expire', 'ynbusinesspages', 'The business {item:$object} has just been set to never expire.', 0, ''),
('ynbusinesspages_business_unclaimed', 'ynbusinesspages', 'The business {item:$object} has just unclaimed.', 0, ''),
('ynbusinesspages_business_noticeexpired', 'ynbusinesspages', 'The business {item:$object} will be expired in {var:$time}', 0, ''),
('ynbusinesspages_approve', 'ynbusinesspages', '{item:$subject} has requested to join the business {item:$object}.', 0, ''),
('ynbusinesspages_accepted', 'ynbusinesspages', 'Your request to join the business {item:$object} has been approved.', 0, ''),
('ynbusinesspages_joined', 'ynbusinesspages', '{item:$subject} has joined the business {item:$object}.', 0, ''),
('ynbusinesspages_edited', 'ynbusinesspages', 'The information of {item:$object} business has been updated.', 0, '');


--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynbusinesspages_claim_success', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_claim_approved', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_approved', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_featured', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_unfeatured', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_discussion_reply', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_discussion_response', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_follow_business', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_invite', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_invite_message', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]'),
('notify_ynbusinesspages_transfer_owner', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_add_review', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_expired', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_never_expire', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_unclaimed', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_business_noticeexpired', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_approve', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_accepted', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_joined', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynbusinesspages_edited', 'ynbusinesspages', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');


-- Activity Type
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynbusinesspages_photo_upload', 'ynbusinesspages', '{item:$subject} added {var:$count} photo(s).', 1, 3, 2, 1, 1, 1),
('ynbusinesspages_topic_reply', 'ynbusinesspages', '{item:$subject} replied to the topic: {body:$body}', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_topic_create', 'ynbusinesspages', '{item:$subject} posted a new topic.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_review_create', 'ynbusinesspages', '{item:$subject} add a review for the business {item:$object}', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_business_create', 'ynbusinesspages', '{item:$subject} create the business {item:$object}', 1, 5, 1, 1, 1, 1),
('ynbusinesspages_video_create', 'ynbusinesspages', '{item:$subject} posted a new video.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_music_create', 'ynbusinesspages', '{item:$subject} created a new music playlist.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_mp3music_create', 'ynbusinesspages', '{item:$subject} created a new mp3music album.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_event_create', 'ynbusinesspages', '{item:$subject} created a new event.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_blog_create', 'ynbusinesspages', '{item:$subject} wrote a new blog entry.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_classified_create', 'ynbusinesspages', '{item:$subject} posted a new classified listing.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_deal_create', 'ynbusinesspages', '{item:$subject} created a new deal.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_contest_create', 'ynbusinesspages', '{item:$subject} posted a new contest.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_listing_create', 'ynbusinesspages', '{item:$subject} posted a new listing.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_job_import', 'ynbusinesspages', '{item:$subject} added a new job.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_poll_create', 'ynbusinesspages', '{item:$subject} posted a new poll.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_folder_create', 'ynbusinesspages', '{item:$subject} posted a new folder.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_page_create', 'ynbusinesspages', '{item:$subject} has created a new wiki page.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_join', 'ynbusinesspages', '{item:$subject} joined the business {item:$object}', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_ynmusic_album_create', 'ynbusinesspages', '{item:$subject} add a new social music album.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_ynmusic_songs_create', 'ynbusinesspages', '{item:$subject} add some social music songs.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_ynultimatevideo_video_create', 'ynbusinesspages', '{item:$subject} add a video.', 1, 3, 1, 1, 1, 1);

-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynbusinesspages_main', 'standard', 'YN - Business Main Navigation Menu', 999);

-- insert back-end menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynbusinesspages', 'ynbusinesspages', 'YN - Business', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynbusinesspages_admin_settings_global', 'ynbusinesspages', 'Global Settings', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"settings", "action":"global"}', 'ynbusinesspages_admin_main', '', 1),
('ynbusinesspages_admin_settings_level', 'ynbusinesspages', 'Member Level Settings', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"settings", "action":"level"}', 'ynbusinesspages_admin_main', '', 2),
('ynbusinesspages_admin_main_categories', 'ynbusinesspages', 'Categories', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"category", "action":"index"}', 'ynbusinesspages_admin_main', '', 3),
('ynbusinesspages_admin_main_packages', 'ynbusinesspages', 'Manage Packages', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"packages", "action":"index"}', 'ynbusinesspages_admin_main', '', 4),
('ynbusinesspages_admin_main_businesses', 'ynbusinesspages', 'Manage Businesses', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"businesses", "action":"index"}', 'ynbusinesspages_admin_main', '', 5),
('ynbusinesspages_admin_main_creator', 'ynbusinesspages', 'Manage Business Creators', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"creators", "action":"index"}', 'ynbusinesspages_admin_main', '', 6),
('ynbusinesspages_admin_main_comparison', 'ynbusinesspages', 'Manage Comparison', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"comparison", "action": "index"}', 'ynbusinesspages_admin_main', '', 7),
('ynbusinesspages_admin_main_transactions', 'ynbusinesspages', 'Manage Transactions', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"transactions", "action":"index"}', 'ynbusinesspages_admin_main', '', 8),
('ynbusinesspages_admin_main_emailtemplates', 'ynbusinesspages', 'Email Templates', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"mail", "action":"templates"}', 'ynbusinesspages_admin_main', '', 9),
('ynbusinesspages_admin_main_claims', 'ynbusinesspages', 'Manage Claim Requests', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"claims", "action":"index"}', 'ynbusinesspages_admin_main', '', 10),
('ynbusinesspages_admin_main_faqs', 'ynbusinesspages', 'Manage FAQs', '', '{"route":"admin_default","module":"ynbusinesspages","controller":"faqs", "action":"index"}', 'ynbusinesspages_admin_main', '', 11);

-- insert front-end menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_main_ynbusinesspages', 'ynbusinesspages', 'Business', '', '{"route":"ynbusinesspages_general"}', 'core_main', '', 1, 0, 999),
('ynbusinesspages_main_browse_business', 'ynbusinesspages', 'Browse', '', '{"route":"ynbusinesspages_general"}', 'ynbusinesspages_main', '', 1, 0, 1),
('ynbusinesspages_main_listing_business', 'ynbusinesspages', 'Listing', '', '{"route":"ynbusinesspages_general","controller":"index", "action":"listing"}', 'ynbusinesspages_main', '', 1, 0, 2),
('ynbusinesspages_main_manage_business', 'ynbusinesspages', 'My', 'Ynbusinesspages_Plugin_Menus', '{"route":"ynbusinesspages_general","controller":"index","action":"manage"}', 'ynbusinesspages_main', '', 1, 0, 3),
('ynbusinesspages_main_faqs', 'ynbusinesspages', 'FAQs', '', '{"route":"ynbusinesspages_extended","controller":"faqs","action":"index"}', 'ynbusinesspages_main', '', 1, 0, 4);

-- insert login as business to mini menu
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynbusinesspages_mini_loginasbusiness', 'ynbusinesspages', 'Login as Business', 'Ynbusinesspages_Plugin_Menus', '', 'core_mini', '', 5);

ALTER TABLE  `engine4_activity_likes` CHANGE  `poster_type`  `poster_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE  `engine4_activity_comments` CHANGE  `poster_type`  `poster_type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_albums`
--

DROP TABLE IF EXISTS `engine4_ynbusinesspages_albums` ;
CREATE TABLE `engine4_ynbusinesspages_albums` (
`album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`business_id` int(11) unsigned NOT NULL,
`title` varchar(128) NOT NULL,
`description` varchar(255) NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
`search` tinyint(1) NOT NULL default '1',
`photo_id` int(11) unsigned NOT NULL default '0',
`view_count` int(11) unsigned NOT NULL default '0',
`comment_count` int(11) unsigned NOT NULL default '0',
`collectible_count` int(11) unsigned NOT NULL default '0',
PRIMARY KEY (`album_id`),
KEY (`business_id`),
KEY (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_photos`
--

DROP TABLE IF EXISTS `engine4_ynbusinesspages_photos`;
CREATE TABLE `engine4_ynbusinesspages_photos` (
`photo_id` int(11) unsigned NOT NULL auto_increment,
`album_id` int(11) unsigned NOT NULL,
`business_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`title` varchar(128) NOT NULL,
`description` varchar(255) NOT NULL,
`collection_id` int(11) unsigned NOT NULL,
`file_id` int(11) unsigned NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
`view_count` int(11) unsigned NOT NULL default '0',
`comment_count` int(11) unsigned NOT NULL default '0',
PRIMARY KEY (`photo_id`),
KEY (`album_id`),
KEY (`business_id`),
KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_posts`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_posts` (
`post_id` int(11) unsigned NOT NULL auto_increment,
`topic_id` int(11) unsigned NOT NULL,
`business_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`body` text NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
PRIMARY KEY  (`post_id`),
KEY `topic_id` (`topic_id`),
KEY `business_id` (`business_id`),
KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_topics`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_topics` (
`topic_id` int(11) unsigned NOT NULL auto_increment,
`business_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`title` varchar(64) NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
`sticky` tinyint(1) NOT NULL default '0',
`closed` tinyint(1) NOT NULL default '0',
`view_count` int(11) unsigned NOT NULL default '0',
`post_count` int(11) unsigned NOT NULL default '0',
`lastpost_id` int(11) unsigned NOT NULL,
`lastposter_id` int(11) unsigned NOT NULL,
PRIMARY KEY  (`topic_id`),
KEY `business_id` (`business_id`),
KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_event_topicwatches`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_topicwatches` (
`resource_id` int(10) unsigned NOT NULL,
`topic_id` int(10) unsigned NOT NULL,
`user_id` int(10) unsigned NOT NULL,
`watch` tinyint(1) unsigned NOT NULL default '1',
PRIMARY KEY  (`resource_id`,`topic_id`,`user_id`),
KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_layout_proxies` (
`proxy_id` INT(11) NOT NULL AUTO_INCREMENT,
`page_id` INT(11) NOT NULL,
`page_name` VARCHAR(128) NOT NULL COLLATE 'latin1_general_ci',
`subject_type` VARCHAR(64) NOT NULL COLLATE 'latin1_general_ci',
`subject_id` INT(11) NOT NULL,
PRIMARY KEY (`proxy_id`)
)ENGINE=InnoDb;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynbusinesspages_mappings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_mappings` (
`mapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`owner_id` int(11) unsigned NOT NULL,
`owner_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
`business_id` int(11) unsigned NOT NULL,
`item_id` int(11) unsigned NOT NULL,
`type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime DEFAULT NULL,
PRIMARY KEY (`mapping_id`,`business_id`,`item_id`),
KEY `business_id` (`business_id`,`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_follows` (
`follow_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`business_id` int(11) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
PRIMARY KEY (`follow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_ynbusinesspages_favourites` (
`favourite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`business_id` int(11) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
PRIMARY KEY (`favourite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- set authorization
-- ynbusinesspages_business

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'user_credit' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'create' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'delete' as `name`,
2 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'claim' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'rate' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'autoapprove' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'max' as `name`,
3 as `value`,
3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'user_credit' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'create' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'delete' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'claim' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'rate' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'autoapprove' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_business' as `type`,
'max' as `name`,
3 as `value`,
3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- auth video for business
INSERT IGNORE INTO `engine4_authorization_permissions`
	SELECT
	level_id as `level_id`,
	'ynbusinesspages_business' as `type`,
	'video' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

-- auth video for business
INSERT IGNORE INTO `engine4_authorization_permissions`
	SELECT
	level_id as `level_id`,
	'ynbusinesspages_business' as `type`,
	'ynultimatevideo_video' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

-- auth comment for business
INSERT IGNORE INTO `engine4_authorization_permissions`
	SELECT
	level_id as `level_id`,
	'ynbusinesspages_business' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

-- auth view for business
INSERT IGNORE INTO `engine4_authorization_permissions`
	SELECT
	level_id as `level_id`,
	'ynbusinesspages_business' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user', 'public');


-- PACKAGE
-- ADMIN - MOD
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_package' as `type`,
'view' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
level_id as `level_id`,
'ynbusinesspages_package' as `type`,
'view' as `name`,
1 as `value`,
NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Ynbusinesspages Check Expired Businesses', 'ynbusinesspages', 'Ynbusinesspages_Plugin_Task_CheckExpiredBusinesses', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0),
('Ynbusinesspages Check Expired Claim', 'ynbusinesspages', 'Ynbusinesspages_Plugin_Task_CheckExpiredClaim', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0),
('Ynbusinesspages Check Featured Businesses', 'ynbusinesspages', 'Ynbusinesspages_Plugin_Task_CheckFeaturedBusinesses', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0),
('Ynbusinesspages Check Notification Expired Businesses', 'ynbusinesspages', 'Ynbusinesspages_Plugin_Task_CheckNotificationExpiredBusinesses', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);