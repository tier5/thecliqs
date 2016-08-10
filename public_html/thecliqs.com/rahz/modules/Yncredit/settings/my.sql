INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('yncredit', 'Younet User Credits', '', '4.01p1', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('yncredit_main', 'standard', 'User Credit Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_yncredit', 'yncredit', 'Credits', 'Yncredit_Plugin_Menus', '{"route":"yncredit_general"}', 'core_main', '', 6),
('core_sitemap_yncredit', 'yncredit', 'Credits', '', '{"route":"yncredit_general"}', 'core_sitemap', '', 6),
('yncredit_main_general', 'yncredit', 'General Information', 'Yncredit_Plugin_Menus', '{"route":"yncredit_general"}', 'yncredit_main', '', 1),
('yncredit_main_my', 'yncredit', 'My Credits', 'Yncredit_Plugin_Menus', '{"route":"yncredit_my"}', 'yncredit_main', '', 2),
('yncredit_main_faq', 'yncredit', 'FAQs', 'Yncredit_Plugin_Menus', '{"route":"yncredit_faq"}', 'yncredit_main', '', 3);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_yncredit', 'yncredit', 'YouNet - Credits', '', '{"route":"admin_default","module":"yncredit","controller":"level"}', 'core_admin_main_plugins', '', 999),
('yncredit_admin_main_level', 'yncredit', 'Member Level Settings', '', '{"route":"admin_default","module":"yncredit","controller":"level"}', 'yncredit_admin_main', '', 1),
('yncredit_admin_main_settings', 'yncredit', 'Global Settings', '', '{"route":"admin_default","module":"yncredit","controller":"settings"}', 'yncredit_admin_main', '', 2),
('yncredit_admin_main_manage_faq', 'yncredit', 'Manage FAQ', '', '{"route":"admin_default","module":"yncredit","controller":"manage-faq"}', 'yncredit_admin_main', '', 3),
('yncredit_admin_main_transactions', 'yncredit', 'Transactions', '', '{"route":"admin_default","module":"yncredit","controller":"transactions"}', 'yncredit_admin_main', '', 5),
('yncredit_admin_main_statistics', 'yncredit', 'Statistics', '', '{"route":"admin_default","module":"yncredit","controller":"statistics"}', 'yncredit_admin_main', '', 6),
('yncredit_admin_main_manage_packages', 'yncredit', 'Packages', '', '{"route":"admin_default","module":"yncredit","controller":"manage-package"}', 'yncredit_admin_main', '', 7),
('yncredit_admin_main_browse_member_credits', 'yncredit', 'Member Credits', '', '{"route":"admin_default","module":"yncredit","controller":"member-credit"}', 'yncredit_admin_main', '', 8);

INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('user_profile_yncredit', 'yncredit', 'Send Credits', 'Yncredit_Plugin_Menus', '', 'user_profile', '', 1, 0, 2);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
('yncredit_receive', 'yncredit', 'You have just received {var:$credits} credits from {item:$subject}.{item:$object}', 0, '', 1),
('yncredit_debit', 'yncredit', 'You have just been debited {var:$credits} credits by {var:$site_name}.{item:$object}', 0, '', 1);


INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_yncredit_receive', 'yncredit', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[credits]'),
('notify_yncredit_debit', 'yncredit', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[credits],[site_name]');

DROP TABLE IF EXISTS `engine4_yncredit_modules`;
CREATE TABLE `engine4_yncredit_modules` (
  `module_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `level_id` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_yncredit_types`;
CREATE TABLE `engine4_yncredit_types` (
  `type_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `action_type` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `group` enum('earn', 'buy', 'send', 'spend', 'receive'),
  `module` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `credit_default` FLOAT NOT NULL DEFAULT '0',
  `link_params` VARCHAR(255) COLLATE utf8_unicode_ci NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Core actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('core', 'signup', 'earn', 'Sign Up', '20'),
('core', 'user_login', 'earn', 'Log in', '1'),
('core', 'network_join', 'earn', 'Joining in %s network', '1'),
('core', 'friends', 'earn', 'Adding a new friend with %s', '5'),
('core', 'status', 'earn', '%s update status', '1'),
('core', 'post_self', 'earn', 'Share an item on wall', '1'),
('core', 'post', 'earn', 'Post on %s profile', '1'),
('core', 'share', 'earn', 'Share %s item', '1'),
('core', 'core_link', 'earn', 'Attaching %s link', '1'),
('core', 'core_like', 'earn', 'Like %s', '1'),
('core', 'core_comment', 'earn', 'Commenting %s', '1'),
('core', 'activity_like', 'earn', 'Like %s post', '1'),
('core', 'activity_comment', 'earn', 'Comment %s post', '1'),
('core', 'user_profile_edit', 'earn', 'Edit user profile', '1'),
('core', 'profile_photo_update', 'earn', 'Update profile photo', '1');

-- Album actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('album', 'album_photo_new', 'earn', 'Adding %d photo(s) to %s album', '1', '{"route":"album_general","action":"upload"}'),
('advalbum', 'advalbum_photo_new', 'earn', 'Adding %d photo(s) to %s album', '1', '{"route":"album_general","action":"upload"}');

-- Blog actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('blog', 'blog_new', 'earn', 'Writing %s blog entry', '5', '{"route":"blog_general","action":"create"}'),
('ynblog', 'ynblog_new', 'earn', 'Writing %s blog entry', '5', '{"route":"blog_general","action":"create"}');

-- Classifieds
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('classified', 'classified_new', 'earn', 'Posting %s classified listing', '5', '{"route":"classified_general","action":"create"}');

-- Polls
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('poll', 'poll_new', 'earn', 'Creating %s poll', '5', '{"route":"poll_general","action":"create"}');

-- Forum actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('forum', 'forum_topic_create', 'earn', 'Posting %s topic in a forum', '5'),
('forum', 'forum_topic_reply', 'earn', 'Reply in %s topic', '1'),
('ynforum', 'ynforum_topic_create', 'earn', 'Posting %s topic in a forum', '5'),
('ynforum', 'ynforum_topic_reply', 'earn', 'Reply in %s topic', '1'),
('ynforum', 'ynforum_post_thank', 'earn', 'Thanking to a post in %s topic', '1');

-- Event actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('event', 'event_create', 'earn', 'Creating %s event', '10', '{"route":"event_general","action":"create"}'),
('event', 'event_join', 'earn', 'Joining %s event', '3', ''),
('event', 'event_topic_create', 'earn', 'Posting %s topic in an event', '2', ''),
('event', 'event_topic_reply', 'earn', 'Reply in %s topic in an event', '2', ''),
('event', 'event_photo_upload', 'earn', 'Adding %d photo(s) to %s event', '1', ''),
('ynevent', 'ynevent_create', 'earn', 'Creating %s event', '10', '{"route":"event_general","action":"create"}'),
('ynevent', 'ynevent_join', 'earn', 'Joining %s event', '3', ''),
('ynevent', 'ynevent_topic_create', 'earn', 'Creating a new topic in %s event', '2', ''),
('ynevent', 'ynevent_topic_reply', 'earn', 'Reply a topic in %s event', '2', ''),
('ynevent', 'ynevent_photo_upload', 'earn', 'Adding %d photo(s) to %s event', '1', '');

-- Group actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('group', 'group_create', 'earn', 'Creating %s group', '10', '{"route":"group_general","action":"create"}'),
('group', 'group_join', 'earn', 'Joining %s group', '5', ''),
('group', 'group_topic_create', 'earn', 'Posting a new topic in %s group', '2', ''),
('group', 'group_topic_reply', 'earn', 'Reply a topic in %s group', '2', ''),
('group', 'group_photo_upload', 'earn', 'Adding %d photo(s) to %s group', '1', ''),
('advgroup', 'advgroup_create', 'earn', 'Creating %s group', '10', '{"route":"group_general","action":"create"}'),
('advgroup', 'advgroup_join', 'earn', 'Joining %s group', '5', ''),
('advgroup', 'advgroup_topic_create', 'earn', 'Posting a new topic in %s group', '2', ''),
('advgroup', 'advgroup_topic_reply', 'earn', 'Reply a topic in %s group', '2', ''),
('advgroup', 'advgroup_photo_upload', 'earn', 'Adding %d photo(s) to %s group', '1', '');

-- Video actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('video', 'video_new', 'earn', 'Posting new %s video', '5', '{"route":"video_general","action":"create"}'),
('ynvideo', 'ynvideo_video_new', 'earn', 'Posting new %s video', '5', '{"route":"video_general","action":"create"}');

-- Music actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('music', 'music_playlist_song', 'earn', 'Adding new song to %s playlist', '3', '{"route":"music_general","action":"create"}'),
('mp3music', 'mp3music_album_song', 'earn', 'Adding new song to %s album', '3', '{"route":"mp3music_create_album"}'),
('mp3music', 'buy_mp3music', 'earn', 'Buy music', '5', '');

-- Store
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('socialstore', 'social_store_new', 'earn', 'Creating %s store', '10', '{"route":"socialstore_mystore_general","action":"create-store"}'),
('socialstore', 'social_product_new', 'earn', 'Creating %s product', '3', '{"route":"socialstore_mystore_general"}'),
('socialstore', 'social_product_buy', 'earn', 'Purchase for a shopping card', '20', '');

-- Group Buy
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('groupbuy', 'groupbuy_new', 'earn', 'Posting %s deal', '10', '{"route":"groupbuy_general","action":"create"}'),
('groupbuy', 'groupbuy_buy', 'earn', 'Buying %s deal', '20', '');

-- Contest: add new contest, join contest, post new entry
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('yncontest', 'yncontest_new', 'earn', 'Creating %s contest', '20', '{"route":"yncontest_mycontest","action":"create-contest"}'),
('yncontest', 'yncontest_entry', 'earn', 'Creating %s entry', '10', ''),
('yncontest', 'yncontest_join', 'earn', 'Join %s contest', '5', '');
-- Fund raising
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('ynfundraising', 'ynfundraising_new', 'earn', 'Creating %s campaign', '10', '{"route":"ynfundraising_general","action":"create"}'),
('ynfundraising', 'ynfundraising_donate_msg', 'earn', 'Donated on %s campaign', '20', '');

-- Auction
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('ynauction', 'ynauction_new', 'earn', 'Posting %s auction', '10', '{"route":"ynauction_general","action":"create"}'),
('ynauction', 'ynauction_won', 'earn', 'Winning %s auction', '5', ''),
('ynauction', 'ynauction_buy', 'earn', 'Buying %s auction', '10', '');

-- Idea box
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('ynidea', 'ynidea_trophy_new', 'earn', 'Creating %s trophy', '10', '{"route":"ynidea_trophies","action":"create"}'),
('ynidea', 'ynidea_idea_publish', 'earn', 'Creating %s idea', '10', '{"route":"ynidea_general","action":"create"}');

-- Wiki
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('ynwiki', 'ynwiki_new', 'earn', 'Creating %s wiki page', '10', '{"route":"ynwiki_general","action":"create"}');

-- Filesharing
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('ynfilesharing', 'file', 'earn', 'Add file %s', '5', '{"route":"ynfilesharing_general","controller":"folder","action":"create"}');

-- Payment
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('yncredit', 'buy_credits', 'buy', 'Buy credits', '0');

-- Contact importer
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('contactimporter', 'contactimporter_joined', 'earn', '%s has joined', '5', '{"route":"contactimporter"}');

-- Social media importer
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES 
('ynmediaimporter', 'ynmediaimporter_imported', 'earn', 'Import %s album', '5', '{"route":"ynmediaimporter_general"}');

-- Social stream
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('socialstream', 'socialstream_facebook', 'earn', 'Get feed from Facebook', '2'),
('socialstream', 'socialstream_twitter', 'earn', 'Get feed from Twitter', '2'),
('socialstream', 'socialstream_linkedin', 'earn', 'Get feed from LinkedIn', '2');

-- Social publisher
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('socialpublisher', 'socialpublisher_publish', 'earn', 'Publish %s item', '2');

-- User Profile Completeness
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('profile-completeness', 'profile_completed', 'earn', 'User Profile Completed', '20');


-- Transfer credit
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('yncredit', 'send_credits', 'send', 'Give credits to %s', '0'),
('yncredit', 'receive_credits', 'receive', 'Receive credits from %s', '0');

-- Admin Mass credit
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('yncredit', 'debit_credits', 'spend', '%s has debited credits', '0');


-- Ultimatevideo Video actions
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`, `link_params`) VALUES
('ynultimatevideo', 'ynultimatevideo_video_new', 'earn', 'Posting new %s video', '5', '{"route":"ynultimatevideo_general","action":"create"}');

-- Spend
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES 
('yncredit', 'upgrade_subscription', 'spend', 'Buy or Upgrade Subscription', '0'),
('yncredit', 'publish_deal', 'spend', 'Use credit to publishing %s deal', '0'),
('yncredit', 'buy_deal', 'spend', 'Use credit to buy %s deal', '0'),
('yncredit', 'publish_contest', 'spend', 'Use credit to publishing %s contest', '0');

-- Affiliate
INSERT IGNORE INTO `engine4_yncredit_types` (`module`, `action_type`, `group`, `content`, `credit_default`) VALUES
('ynaffiliate', 'ynaffiliate_subscription', 'earn', 'Affiliate - %s has paid a subscription fee', '10'),
('ynaffiliate', 'ynaffiliate_buy_mp3music', 'earn', 'Affiliate - %s has bought a music', '3'),
('ynaffiliate', 'ynaffiliate_publish_store', 'earn', 'Affiliate - %s has published a store', '3'),
('ynaffiliate', 'ynaffiliate_publish_product', 'earn', 'Affiliate - %s has published a product', '3'),
('ynaffiliate', 'ynaffiliate_buy_product', 'earn', 'Affiliate - %s has bought a product', '20'),
('ynaffiliate', 'ynaffiliate_publish_deal', 'earn', 'Affiliate - %s has published a deal', '3'),
('ynaffiliate', 'ynaffiliate_buy_deal', 'earn', 'Affiliate - %s has bought a deal', '20'),
('ynaffiliate', 'ynaffiliate_publish_ynauction', 'earn', 'Affiliate - %s has published an auction', '3'),
('ynaffiliate', 'ynaffiliate_buy_ynauction', 'earn', 'Affiliate - %s has bought an auction', '20');

DROP TABLE IF EXISTS `engine4_yncredit_credits`;
CREATE TABLE `engine4_yncredit_credits` (
  `credit_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `level_id` INT(11) UNSIGNED NOT NULL,
  `type_id` INT(11) UNSIGNED NOT NULL,
  `first_amount` INT(11) NOT NULL DEFAULT '0',
  `first_credit` FLOAT NOT NULL DEFAULT '0',
  `credit` FLOAT NOT NULL DEFAULT '0',
  `max_credit` FLOAT NOT NULL DEFAULT '0',
  `period` SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`credit_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_yncredit_balances`;
CREATE TABLE `engine4_yncredit_balances` (
  `user_id` INT(11) UNSIGNED NOT NULL,
  `current_credit` FLOAT NOT NULL DEFAULT '0',
  `earned_credit` FLOAT NOT NULL DEFAULT '0',
  `spent_credit` FLOAT NOT NULL DEFAULT '0',
  `bought_credit` FLOAT NOT NULL DEFAULT '0',
  `sent_credit` FLOAT NOT NULL DEFAULT '0',
  `received_credit` FLOAT NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_yncredit_logs`;
CREATE TABLE `engine4_yncredit_logs` (
  `log_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `credit_id` INT(11) UNSIGNED NOT NULL,
  `type_id` INT(11) UNSIGNED NOT NULL,
  `object_type` VARCHAR(24) COLLATE utf8_unicode_ci NOT NULL,
  `object_id` INT(11) UNSIGNED NOT NULL,
  `credit` FLOAT NOT NULL DEFAULT '0',
  `body` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_count` INT( 11 ) NOT NULL DEFAULT  '1',
  `creation_date` DATETIME DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `credit_id` (`credit_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_yncredit_packages`;
CREATE TABLE `engine4_yncredit_packages` (
  `package_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `credit` decimal(16,2) NOT NULL,
  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `active` TINYINT( 1 ) NOT NULL DEFAULT  '1',
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_yncredit_faqs`;
CREATE TABLE `engine4_yncredit_faqs` (
    `faq_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('show','hide') NOT NULL DEFAULT 'hide',
    `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `question` VARCHAR(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `creation_date` DATETIME NOT NULL,
    PRIMARY KEY (`faq_id`)
)
COLLATE='utf8_general_ci' ENGINE=MyISAM ROW_FORMAT=DEFAULT;

DROP TABLE IF EXISTS `engine4_yncredit_orders`;
CREATE TABLE `engine4_yncredit_orders` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `gateway_id` int(10) unsigned NOT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
  `creation_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `package_id` int(10) unsigned NOT NULL DEFAULT '0',
  `credit` decimal(16,2) NOT NULL DEFAULT '0.00',
  `price` decimal(16,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `state` (`status`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('User Credits Recurring Plans', 'yncredit', 'Yncredit_Plugin_Task_Recurring', 300, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);

-- Init default credit
INSERT IGNORE INTO `engine4_yncredit_credits`
  SELECT
    NULL AS `credit_id`,
    (SELECT level_id FROM engine4_authorization_levels WHERE `type` = 'user') AS `level_id`,
    type_id AS `type_id`,
    0 AS `first_amount`,
    0 AS `first_credit`,
    credit_default AS `credit`,
    100 AS `max_credit`,
    1 AS `period`
  FROM `engine4_yncredit_types`;
  
INSERT IGNORE INTO `engine4_yncredit_credits`
  SELECT
    NULL AS `credit_id`,
    (SELECT level_id FROM engine4_authorization_levels WHERE `type` = 'moderator') AS `level_id`,
    type_id AS `type_id`,
    0 AS `first_amount`,
    0 AS `first_credit`,
    credit_default AS `credit`,
    100 AS `max_credit`,
    1 AS `period`
  FROM `engine4_yncredit_types`;
  
INSERT IGNORE INTO `engine4_yncredit_credits`
  SELECT
    NULL AS `credit_id`,
    (SELECT level_id FROM engine4_authorization_levels WHERE `type` = 'admin' AND `flag` = 'superadmin') AS `level_id`,
    type_id AS `type_id`,
    0 AS `first_amount`,
    0 AS `first_credit`,
    credit_default AS `credit`,
    100 AS `max_credit`,
    1 AS `period`
  FROM `engine4_yncredit_types`;
  
INSERT IGNORE INTO `engine4_yncredit_credits`
  SELECT
    NULL AS `credit_id`,
    (SELECT level_id FROM engine4_authorization_levels WHERE `type` = 'admin' AND `flag` != 'superadmin') AS `level_id`,
    type_id AS `type_id`,
    0 AS `first_amount`,
    0 AS `first_credit`,
    credit_default AS `credit`,
    100 AS `max_credit`,
    1 AS `period`
  FROM `engine4_yncredit_types`;  

-- Init Member Level General Settings
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'spend' AS `name`,
    5 AS `value`,
    '["upgrade_subscription","buy_deal","publish_deal","publish_contest"]' AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'send' AS `name`,
    1 AS `value`,
    NULL AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'max_send' AS `name`,
    3 AS `value`,
    '1500' AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'period_send' AS `name`,
    3 AS `value`,
    'day' AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'receive' AS `name`,
    1 AS `value`,
    NULL AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'max_receive' AS `name`,
    3 AS `value`,
    '1500' AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'period_receive' AS `name`,
    3 AS `value`,
    'day' AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'use_credit' AS `name`,
    1 AS `value`,
    NULL AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'general_info' AS `name`,
    1 AS `value`,
    NULL AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id AS `level_id`,
    'yncredit' AS `type`,
    'faq' AS `name`,
    1 AS `value`,
    NULL AS `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
