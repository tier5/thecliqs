-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynlistings_follows`
--
CREATE TABLE IF NOT EXISTS `engine4_ynlistings_follows` (
  `follow_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`follow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynlistings_reports`
--
CREATE TABLE IF NOT EXISTS `engine4_ynlistings_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`),
  KEY `listing_id` (`listing_id`),
  KEY `topic_id` (`topic_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_posts`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_posts` (
  `post_id` int(11) unsigned NOT NULL auto_increment,
  `topic_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_topics`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_topics` (
  `topic_id` int(11) unsigned NOT NULL auto_increment,
  `listing_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(64) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `sticky` tinyint(1) NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  `post_count` int(11) unsigned NOT NULL default '0',
  `view_count` int(11) unsigned NOT NULL default '0',
  `lastpost_id` int(11) unsigned NOT NULL default '0',
  `lastposter_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_topicwatches`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_topicwatches` (
  `resource_id` int(10) unsigned NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `watch` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`resource_id`,`topic_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_imports`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_imports` (
  `import_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creation_date` datetime NOT NULL,
  `file_name` text,
  `number_listings` text,
  `list_listings` text,
  PRIMARY KEY (`import_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_mappings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_mappings` (
  `mapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`mapping_id`,`listing_id`,`item_id`),
  KEY `user_id` (`listing_id`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_albums`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `collectible_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_photos`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) unsigned NOT NULL,
  `album_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `image_title` varchar(128) NOT NULL,
  `image_description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_orders` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `gateway_id` int(11) unsigned NOT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
  `creation_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `listing_id` int(11) unsigned NOT NULL DEFAULT '0',
  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `currency` char(3),
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `feature_day_number` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `state` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_listing_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_listing_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynlistings_listing_fields_maps`
--

INSERT IGNORE INTO `engine4_ynlistings_listing_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_listing_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_listing_fields_meta` (
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
-- Dumping data for table `engine4_ynlistings_listing_fields_meta`
--

INSERT IGNORE INTO `engine4_ynlistings_listing_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_listing_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_listing_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynlistings_listing_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_listing_fields_search` (
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
-- Table structure for table `engine4_ynlistings_listing_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_listing_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynlistings_categories`
--
CREATE TABLE IF NOT EXISTS `engine4_ynlistings_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `pleft` int(11) unsigned NOT NULL,
  `pright` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  `photo_id` int(11) DEFAULT NULL,
  `themes` text,
  `use_parent_category` tinyint(1) NOT NULL DEFAULT '0',
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
-- Dumping data for table `engine4_ynlistings_categories`
--

INSERT IGNORE INTO `engine4_ynlistings_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Categories','0');

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_faqs` (
  `faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynlistings_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_transactions` (
`transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`payment_transaction_id` varchar(128),
`creation_date` date NOT NULL,
`status` enum('initialized','expired','pending','completed','canceled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`description` text NOT NULL,
`gateway_id` int(11) NOT NULL,
`amount` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`listing_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynlistings_listings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_listings` (
`listing_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(128) NOT NULL,
`user_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL DEFAULT 0,
`theme` text,
`creation_date` datetime NOT NULL,
`approved_date` datetime NULL,
`end_date` datetime NULL,
`approved_status` enum('pending','approved','denied') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
`status` enum('closed','open','draft','expired') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`featured` BOOLEAN NOT NULL DEFAULT 0,
`feature_expiration_date` datetime DEFAULT NULL,
`feature_day_number` int(11) unsigned NOT NULL DEFAULT '0',
`highlight` BOOLEAN NOT NULL DEFAULT 0,
`location` text COLLATE utf8_unicode_ci NOT NULL,
`longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`short_description` text COLLATE utf8_unicode_ci NOT NULL,
`description` text COLLATE utf8_unicode_ci NOT NULL,
`about_us` text COLLATE utf8_unicode_ci NOT NULL,
`photo_id` int(11),
`video_id` int(11),
`price` decimal(16,2) unsigned NOT NULL,
`currency` char(3),
`search` tinyint(1) NOT NULL DEFAULT '1',
`view_count` int(11) NOT NULL DEFAULT 0,
`like_count` int(11) NOT NULL DEFAULT 0,
`view_time` datetime NOT NULL,
PRIMARY KEY (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynlistings_reviews`
--

CREATE TABLE IF NOT EXISTS `engine4_ynlistings_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `listing_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `rate_number` smallint(5) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`review_id`),
  KEY `listing_id` (`listing_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- change table permissions (change length of column type)
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);

-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynlistings_main', 'standard', 'YN Listings Main Navigation Menu', 999);

-- insert admin menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynlistings', 'ynlistings', 'YN Listings', '', '{"route":"admin_default","module":"ynlistings","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynlistings_admin_settings_global', 'ynlistings', 'Global Settings', '', '{"route":"admin_default","module":"ynlistings","controller":"settings", "action":"global"}', 'ynlistings_admin_main', '', 1),
('ynlistings_admin_settings_level', 'ynlistings', 'Member Level Settings', '', '{"route":"admin_default","module":"ynlistings","controller":"settings", "action":"level"}', 'ynlistings_admin_main', '', 2),
('ynlistings_admin_main_categories', 'ynlistings', 'Categories', '', '{"route":"admin_default","module":"ynlistings","controller":"category", "action":"index"}', 'ynlistings_admin_main', '', 3),
('ynlistings_admin_main_listings', 'ynlistings', 'Manage Listings', '', '{"route":"admin_default","module":"ynlistings","controller":"listings", "action":"index"}', 'ynlistings_admin_main', '', 4),
('ynlistings_admin_main_imports', 'ynlistings', 'Import Listings', '', '{"route":"admin_default","module":"ynlistings","controller":"imports", "action":"index"}', 'ynlistings_admin_main', '', 5),
('ynlistings_admin_main_transactions', 'ynlistings', 'Manage Transactions', '', '{"route":"admin_default","module":"ynlistings","controller":"transactions", "action":"index"}', 'ynlistings_admin_main', '', 6),
('ynlistings_admin_main_statistics', 'ynlistings', 'Statistics', '', '{"route":"admin_default","module":"ynlistings","controller":"statistics", "action":"index"}', 'ynlistings_admin_main', '', 7),
('ynlistings_admin_main_reports', 'ynlistings', 'Manage Reports', '', '{"route":"admin_default","module":"ynlistings","controller":"report", "action":"manage"}', 'ynlistings_admin_main', '', 8),
('ynlistings_admin_main_faqs', 'ynlistings', 'Manage FAQs', '', '{"route":"admin_default","module":"ynlistings","controller":"faqs", "action":"index"}', 'ynlistings_admin_main', '', 9),
('core_main_ynlistings', 'ynlistings', 'Listings', '', '{"route":"ynlistings_general"\n}', 'core_main', '', 999),

-- insert main menu items
('ynlistings_main_home', 'ynlistings', 'Listings Home Page', '', '{"route":"ynlistings_general","module":"ynlistings","controller":"index","action":"index"}', 'ynlistings_main', '', 1),
('ynlistings_main_browse', 'ynlistings', 'Browse Listings', '', '{"route":"ynlistings_general","module":"ynlistings","controller":"index","action":"browse"}', 'ynlistings_main', '', 2),
('ynlistings_main_manage', 'ynlistings', 'My Listings', 'Ynlistings_Plugin_Menus', '{"route":"ynlistings_general","module":"ynlistings","controller":"index","action":"manage"}', 'ynlistings_main', '', 3),
('ynlistings_main_post_listing', 'ynlistings', 'Post A New Listing', 'Ynlistings_Plugin_Menus', '{"route":"ynlistings_general","module":"ynlistings","controller":"index","action":"create"}', 'ynlistings_main', '', 4),
('ynlistings_main_import_listing', 'ynlistings', 'Import Listings', 'Ynlistings_Plugin_Menus', '{"route":"ynlistings_general","module":"ynlistings","controller":"index","action":"import"}', 'ynlistings_main', '', 5),
('ynlistings_main_faqs', 'ynlistings', 'FAQs', '', '{"route":"ynlistings_faqs","module":"ynlistings","controller":"faqs"}', 'ynlistings_main', '', 6);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

-- set default permissions for level settings if listing

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynlistings_discussion_response', 'ynlistings', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::listing topic} you created.', 0, ''),
('ynlistings_discussion_reply', 'ynlistings', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::listing topic} you posted on.', 0, ''),
('ynlistings_listing_follow', 'ynlistings', '{item:$subject} has create a new {item:$object:listing}.', 0, ''),
('ynlistings_listing_approve', 'ynlistings', 'Your listing {item:$object} has been approved.', 0, ''),
('ynlistings_listing_deny', 'ynlistings', 'Your listing {item:$object} has been denied.', 0, ''),
('ynlistings_listing_follow_owner', 'ynlistings', '{item:$subject} start to follow your listings.', 0, ''),
('ynlistings_listing_add_review', 'ynlistings', 'Your listing {item:$object} has a new review.', 0, '');
--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynlistings_video_create', 'ynlistings', '{item:$subject} posted a new video:', 1, 3, 1, 1, 1, 1),
('ynlistings_topic_reply', 'ynlistings', '{item:$subject} replied to the topic {body:$body}', 1, 3, 1, 1, 1, 1),
('ynlistings_topic_create', 'ynlistings', '{item:$subject} posted a new topic:', 1, 3, 1, 1, 1, 1),
('ynlistings_photo_upload', 'ynlistings', '{item:$subject} added {var:$count} photo(s).', 1, 3, 2, 1, 1, 1),
('ynlistings_topic_reply', 'ynlistings', '{item:$subject} replied to the topic {body:$body}', 1, 3, 1, 1, 1, 1),
('ynlistings_review_create', 'ynlistings', '{item:$subject} add a review for the listing {item:$object}', 1, 3, 1, 1, 1, 1),
('ynlistings_listing_transfer', 'ynlistings', '{item:$subject} has became the owner of the listing {item:$object}', 1, 3, 1, 1, 1, 1),
('ynlistings_listing_create', 'ynlistings', '{item:$subject} add a new listing:', 1, 5, 1, 1, 1, 1);
--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('ynlistings_email_to_friends', 'ynlistings', '[host],[email],[date],[sender_title],[sender_link],[sender_photo],[object_title],[message],[object_link],[object_photo],[object_description]'),
('notify_ynlistings_discussion_reply', 'ynlistings', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynlistings_discussion_response', 'ynlistings', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynlistings_listing_follow', 'ynlistings', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynlistings_listing_approve', 'ynlistings', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynlistings_listing_deny', 'ynlistings', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

-- set default permissions for level settings if listing

-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'publish_fee' as `name`,
    3 as `value`,
    10 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'feature_fee' as `name`,
    3 as `value`,
    100 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'publish_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'follow' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'select_theme' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'upload_photos' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'upload_videos' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'discussion' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'approve' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'share' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'print' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'import' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'export' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'report' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'rate' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'view_listings' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'add_photos' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'add_videos' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'sharing' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'printing' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'add_discussions' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'max_listings' as `name`,
    3 as `value`,
    100 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'publish_fee' as `name`,
    3 as `value`,
    10 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'feature_fee' as `name`,
    3 as `value`,
    100 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'publish_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'follow' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'select_theme' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'upload_photos' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'upload_videos' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'discussion' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'approve' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'print' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'share' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'import' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'export' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'report' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'rate' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'view_listings' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'add_photos' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'add_videos' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'sharing' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'printing' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'add_discussions' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'max_listings' as `name`,
    3 as `value`,
    100 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_listing' as `type`,
    'print' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- set permissions for listing review

-- ADMIN
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_review' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_review' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_review' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_review' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_review' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_review' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynlistings_review' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Ynlistings Check Listing', 'ynlistings', 'Ynlistings_Plugin_Task_CheckListing', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);