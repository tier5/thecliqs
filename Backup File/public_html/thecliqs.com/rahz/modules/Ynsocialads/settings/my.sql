INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynsocialads', 'Social Ads', 'The plugin functions similarly to popular social ads platform nowadays, such as Facebook Ads where all business logic, workflow, terminology are simulated well.', '4.01', 1, 'extra');

--
-- Table structure for table `engine4_ynsocialads_campaigns`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_campaigns` (
  `campaign_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('active','deleted') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`campaign_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_faqs` (
  `faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_transactions` (
`transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`payment_transaction_id` varchar(128),
`creation_date` datetime NOT NULL,
`start_date` datetime NULL,
`status` enum('initialized','expired','pending','completed','canceled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`gateway_id` int(11) NOT NULL,
`amount` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`ad_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_tracks` (
`track_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`date` datetime NOT NULL,
`ad_id` int(11) NOT NULL,
`clicks` int(11) NOT NULL DEFAULT 0,
`unique_clicks` int(11) NOT NULL DEFAULT 0,
`impressions` int(11) NOT NULL DEFAULT 0,
`reaches` int(11) NOT NULL DEFAULT 0,
PRIMARY KEY (`track_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_modules`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_modules` (
  `module_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(128) NOT NULL,
  `module_title` varchar(128) NOT NULL,
  `table_item` varchar(128) NOT NULL,
  `title_field` varchar(128) NOT NULL,
  `body_field` varchar(128) NOT NULL,
  `owner_field` varchar(128) NOT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynsocialads_ads`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_ads` (
  `ad_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) unsigned NOT NULL,
  `package_id` int(11) unsigned NOT NULL,
  `benefit_total` int(11) unsigned NOT NULL,
  `ad_type` ENUM('text','banner','feed') NOT NULL COLLATE 'utf8_unicode_ci',
  `user_id` int(11) unsigned NOT NULL,
  `module_id` int(11) unsigned ,
  `item_id` int(11) unsigned,
  `url` varchar(128),
  `name` varchar(128) NOT NULL,
  `description` text NULL,
  `photo_id` int(11) NULL,
  `status` ENUM('draft','unpaid','pending','denied','running','paused','completed','approved','deleted') NOT NULL COLLATE 'utf8_unicode_ci',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `creation_date` datetime  NULL,
  `modified_date` datetime  NULL,
  `start_date` datetime  NULL,
  `end_date` datetime  NULL,
  `running_date` datetime  NULL,
  `click_count` int(11) unsigned NOT NULL DEFAULT '0',
  `impressions_count` int(11) unsigned NOT NULL DEFAULT '0',
  `unique_click_count` int(11) unsigned NOT NULL DEFAULT '0',
  `reaches_count` int(11) unsigned NOT NULL DEFAULT '0',
  `last_view` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynsocialads_moneyrequests`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_moneyrequests` (
  `moneyrequest_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` ENUM('pending','approved','rejected') NOT NULL COLLATE 'utf8_unicode_ci',
  `user_id` int(11) unsigned NOT NULL,
  `paypal_email` varchar(128)NOT NULL,
  `amount` decimal(16,2) unsigned NOT NULL,
  `currency` char(3),
  `request_date` datetime NOT NULL,
  `response_date` datetime  NULL,
  `request_message` varchar(128) NOT NULL,
  `response_message` varchar(128)  NULL,
  `payment_transaction_id` varchar(128),
  PRIMARY KEY (`moneyrequest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynsocialads_statistics`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_statistics` (
`statistic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned ,
`IP` varbinary(16) ,
`timestamp` timestamp NOT NULL,
`type` ENUM('click', 'impression') NOT NULL,
`ad_id` int(11) unsigned NOT NULL,
PRIMARY KEY (`statistic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_packages`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_packages` (
  `package_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `price` decimal(16,2) unsigned NOT NULL,
  `currency` char(3),
  `benefit_amount` int(11) unsigned,
  `benefit_type` ENUM('click', 'impression', 'day') NOT NULL,
  `description` text,
  `modules` text,
  `allowed_ad_types` text,
  `show` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynsocialads_packageblocks`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_packageblocks` (
  `packageblock_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(11) unsigned NOT NULL,
  `block_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`packageblock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_hiddens`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_hiddens` (
  `hidden_id`   int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned ,
  `IP` varbinary(16) ,
  `id` int(11) unsigned NOT NULL,
  `type` varchar(128),
  PRIMARY KEY (`hidden_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_orders` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `gateway_id` int(11) unsigned NOT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
  `creation_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `package_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ad_id` int(11) unsigned NOT NULL DEFAULT '0',
  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `currency` char(3),
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `state` (`status`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `engine4_ynsocialads_virtuals`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_virtuals` (
  `virtual_id`  int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `total` decimal(16,2) NOT NULL DEFAULT '0.00',
  `remain` decimal(16,2) NOT NULL DEFAULT '0.00',
  `currency` char(3),
  PRIMARY KEY (`virtual_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_adsblocks`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_adblocks` (
  `adblock_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL,
  `content_id` int(11) unsigned NOT NULL,
  `placement` enum('left_top','left_bottom','middle_top', 'middle_bottom', 'right_top', 'right_bottom') NULL,
  `title` varchar(64) NOT NULL,
  `creation_date` datetime NOT NULL,
  `ads_limit` int(11) NOT NULL,
  `ajax` boolean NOT NULL DEFAULT 0,
  `enable` boolean NOT NULL DEFAULT 1,
  `deleted` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (`adblock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_mappings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_mappings` (
  `mapping_id`  int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) unsigned NOT NULL,
  `adblock_id` int(11) unsigned NOT NULL,
  `content_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`mapping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynsocialads_adtargets`
--

CREATE TABLE IF NOT EXISTS `engine4_ynsocialads_adtargets` (
`adtarget_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`ad_id` int(11) unsigned NOT NULL,
`age_from` int(11) unsigned NOT NULL,
`age_to` int(11) unsigned NOT NULL,
`gender` int(11) unsigned NOT NULL,
`cities` text COLLATE utf8_unicode_ci,
`countries` text COLLATE utf8_unicode_ci,
`interests` text COLLATE utf8_unicode_ci,
`birthday` tinyint(1) NOT NULL DEFAULT '0',
`networks` text COLLATE utf8_unicode_ci,
`profile_type` int(11) unsigned NOT NULL,
`public` boolean NOT NULL DEFAULT 1,
PRIMARY KEY (`adtarget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- change table permissions (change length of column type)
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynsocialads_main', 'standard', 'YN Social Ads Main Navigation Menu', 999);

INSERT IGNORE INTO `engine4_ynsocialads_modules` 
(`module_name`, `module_title`, `table_item`, `title_field`, `body_field`, `owner_field`) VALUES
('blog', 'Blogs', 'blog', 'title', 'body', 'owner_id'),
('ynblog', 'Advanced Blog', 'blog', 'title', 'body', 'owner_id'),
('event', 'Events', 'event', 'title', 'description', 'user_id'),
('ynevent', 'Advanced Event', 'event', 'title', 'description', 'user_id'),
('video', 'Videos', 'video', 'title', 'description', 'owner_id'),
('ynvideo', 'YouNet Video Plugin', 'video', 'title', 'description', 'owner_id'),
('classified', 'Classifieds', 'classified', 'title', 'body', 'owner_id'),
('poll', 'Polls', 'poll', 'title', 'description', 'user_id'),
('ynauction', 'Auction', 'ynauction_product', 'title', 'description', 'user_id'),
('album', 'Albums', 'album', 'title', 'description', 'owner_id'),
('advalbum', 'Advanced Album - Albums', 'advalbum_album', 'title', 'description', 'owner_id'),
('album', 'Albums Photo', 'album_photo', 'title', 'description', 'owner_id'),
('advalbum', 'Advanced Album - Photos', 'advalbum_photo', 'title', 'description', 'owner_id'),
('yncontest', 'Contest - Contests', 'yncontest_contest', 'contest_name', 'description', 'user_id'),
('yncontest', 'Contest - Entries', 'yncontest_entry', 'entry_name', 'summary', 'user_id'),
('ynfilesharing', 'File Sharing - Folders', 'ynfilesharing_folder', 'title', 'title', 'user_id'),
('ynfilesharing', 'File Sharing - Files', 'ynfilesharing_file', 'name', 'name', 'user_id'),
('forum', 'Forum Topic', 'forum_topic', 'title', 'description', 'user_id'),
('ynforum', 'Advanced Forum - Topics', 'ynforum_topic', 'title', 'description', 'user_id'),
('group', 'Groups', 'group', 'title', 'description', 'user_id'),
('advgroup', 'Advanced Groups', 'advgroup', 'title', 'description', 'user_id'),
('groupbuy', 'Group Buy Deal', 'groupbuy_deal', 'title', 'description', 'user_id'),
('ynfundraising', 'Fundraising Campaign', 'ynfundraising_campaign', 'title', 'main_description', 'user_id'),
('music', 'Music Playlist', 'music_playlist', 'title', 'description', 'owner_id'),
('mp3music', 'Mp3 Music - Playlists', 'mp3music_playlist', 'title', 'description', 'user_id'),
('mp3music', 'Mp3 Music - Albums', 'mp3music_album', 'title', 'description', 'user_id'),
('ynwiki', 'Social Wiki Page', 'ynwiki_page', 'title', 'description', 'user_id'),
('socialstore', 'Social Store - Stores', 'social_store', 'title', 'description', 'owner_id'),
('socialstore', 'Social Store - Products', 'social_product', 'title', 'description', 'owner_id')
;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynsocialads', 'ynsocialads', 'YN Social Ads', '', '{"route":"admin_default","module":"ynsocialads","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynsocialads_admin_main_settings_global', 'ynsocialads', 'Global Settings', '', '{"route":"admin_default","module":"ynsocialads","controller":"settings", "action":"global"}', 'ynsocialads_admin_main', '', 1),
('ynsocialads_admin_main_settings_level', 'ynsocialads', 'Member Level Settings', '', '{"route":"admin_default","module":"ynsocialads","controller":"settings", "action":"level"}', 'ynsocialads_admin_main', '', 2),
('ynsocialads_admin_main_ads', 'ynsocialads', 'Manage Ads', '', '{"route":"admin_default","module":"ynsocialads","controller":"ads"}', 'ynsocialads_admin_main', '', 3),
('ynsocialads_admin_main_campaigns', 'ynsocialads', 'Manage Campaigns', '', '{"route":"admin_default","module":"ynsocialads","controller":"campaigns"}', 'ynsocialads_admin_main', '', 4),
('ynsocialads_admin_main_packages', 'ynsocialads', 'Manage Packages', '', '{"route":"admin_default","module":"ynsocialads","controller":"packages"}', 'ynsocialads_admin_main', '', 6),
('ynsocialads_admin_main_adblocks', 'ynsocialads', 'Manage Ad Blocks', '', '{"route":"admin_default","module":"ynsocialads","controller":"ad-blocks"}', 'ynsocialads_admin_main', '', 5),
('ynsocialads_admin_main_modules', 'ynsocialads', 'Manage Modules', '', '{"route":"admin_default","module":"ynsocialads","controller":"modules"}', 'ynsocialads_admin_main', '', 7),
('ynsocialads_admin_main_money_requests', 'ynsocialads', 'Manage Money Requests', '', '{"route":"admin_default","module":"ynsocialads","controller":"money-requests"}', 'ynsocialads_admin_main', '', 8),
('ynsocialads_admin_main_transactions', 'ynsocialads', 'Manage Transactions', '', '{"route":"admin_default","module":"ynsocialads","controller":"transactions"}', 'ynsocialads_admin_main', '', 9),
('ynsocialads_admin_main_paylater', 'ynsocialads', 'Manage Pay Later Requests', '', '{"route":"admin_default","module":"ynsocialads","controller":"paylater"}', 'ynsocialads_admin_main', '', 10),
('ynsocialads_admin_main_faqs', 'ynsocialads', 'Manage FAQs', '', '{"route":"admin_default","module":"ynsocialads","controller":"faqs"}', 'ynsocialads_admin_main', '', 11),

('core_main_ynsocialads', 'ynsocialads', 'Social Ads', 'Ynsocialads_Plugin_Menus', '{"route":"ynsocialads_campaigns"\n}', 'core_main', '', 999),
('ynsocialads_main_campaigns', 'ynsocialads', 'My Campaigns', 'Ynsocialads_Plugin_Menus', '{"route":"ynsocialads_campaigns","module":"ynsocialads","controller":"campaigns"}', 'ynsocialads_main', '', 1),
('ynsocialads_main_account', 'ynsocialads', 'My Account', 'Ynsocialads_Plugin_Menus', '{"route":"ynsocialads_account","module":"ynsocialads","controller":"account"}', 'ynsocialads_main', '', 4),
('ynsocialads_main_faqs', 'ynsocialads', 'FAQs', '', '{"route":"ynsocialads_faqs","module":"ynsocialads","controller":"faqs"}', 'ynsocialads_main', '', 6),
('ynsocialads_main_report', 'ynsocialads', 'Report', '', '{"route":"ynsocialads_report","module":"ynsocialads","controller":"report"}', 'ynsocialads_main', '', 3),
('ynsocialads_main_my_ads', 'ynsocialads', 'My Ads', 'Ynsocialads_Plugin_Menus', '{"route":"ynsocialads_extended","module":"ynsocialads","controller":"ads"}', 'ynsocialads_main', '', 2),
('ynsocialads_account_virtual_money', 'ynsocialads', 'Virtual Money', 'Ynsocialads_Plugin_Menus', '{"route":"ynsocialads_account","module":"ynsocialads","controller":"account", "action":"virtual-money"}', 'ynsocialads_account', '', 2),
('ynsocialads_account_payment_transaction', 'ynsocialads', 'Payment Transactions', 'Ynsocialads_Plugin_Menus', '{"route":"ynsocialads_account","module":"ynsocialads","controller":"account"}', 'ynsocialads_account', '', 1),
('ynsocialads_main_create_ad', 'ynsocialads', 'Create New Ad', 'Ynsocialads_Plugin_Menus', '{"route":"ynsocialads_ads","module":"ynsocialads","controller":"ads", "action":"create-choose-package"}', 'ynsocialads_main', '', 5);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynsocialads_admin_money_approve', 'ynsocialads', 'Your payment request has been granted.', 0, '', 1),
('ynsocialads_admin_money_reject', 'ynsocialads', 'Your payment request has been denied.', 0, '', 1),
('ynsocialads_admin_ad_delete', 'ynsocialads', 'Your ad {item:$object} has been deleted.', 0, '', 1),
('ynsocialads_admin_ad_approve', 'ynsocialads', 'Your ad {item:$object} has been approved.', 0, '', 1),
('ynsocialads_admin_ad_deny', 'ynsocialads', 'Your ad {item:$object} has been denied.', 0, '', 1),
('ynsocialads_admin_ad_pause', 'ynsocialads', 'Your ad {item:$object} has been paused.', 0, '', 1),
('ynsocialads_admin_ad_resume', 'ynsocialads', 'Your ad {item:$object} has been resumed.', 0, '', 1)
;

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Social Ads Update Status', 'ynsocialads', 'Ynsocialads_Plugin_Task_UpdateStatus', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynsocialads_ad_create', 'ynsocialads', '{item:$subject} created a new ad:', 1, 5, 1, 1, 1, 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynsocialads_admin_money_reject', 'ynsocialads', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

-- set permissions for campaigns

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');  

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_campaign' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- set permissions for ads

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'pay_later' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'pay_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'virtual_money' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'approve' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');  

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'pay_later' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'virtual_money' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'pay_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_ad' as `type`,
    'approve' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- set permissions for packages

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_package' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_package' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_package' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_package' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_package' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_package' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- set permissions for money requests

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_money' as `type`,
    'min_amount' as `name`,
    3 as `value`,
    20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_money' as `type`,
    'max_amount' as `name`,
    3 as `value`,
    100 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_money' as `type`,
    'min_amount' as `name`,
    3 as `value`,
    20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynsocialads_money' as `type`,
    'max_amount' as `name`,
    3 as `value`,
    100 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_core_settings` VALUES
('ynsocialads.noadsshown', 3),
('ynsocialads.posfeedads', 0),
('ynsocialads.paylaterexpiretime', 5);

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Social Ads Update Status', 'ynsocialads', 'Ynsocialads_Plugin_Task_UpdateStatus', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);
