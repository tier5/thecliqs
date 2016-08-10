<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 20.03.12 17:24 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Credit_Installer extends Engine_Package_Installer_Module {

    public function onPreInstall() {
        parent::onPreInstall();

        $db = $this->getDb();
        $translate = Zend_Registry::get('Zend_Translate');

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'hecore')
                ->where('enabled = ?', 1);

        $hecore = $db->fetchRow($select);

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'store')
                ->where('enabled = ?', 1);

        $store = $db->fetchRow($select);

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'donation')
                ->where('enabled = ?', 1);
        $donation = $db->fetchRow($select);

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'wall')
                ->where('enabled = ?', 1);
        $wall = $db->fetchRow($select);

        if (!$hecore) {
            $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
            return $this->_error($error_message);
        }

        if (version_compare($hecore['version'], '4.2.0p1') < 0) {
            $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
            return $this->_error($error_message);
        }

        if ($store && version_compare($store['version'], '4.2.2') < 0) {
            $error_message = $translate->_('This plugin requires Store Plugin. We found that you have old version of Store module, please download latest version of Store Module and install.');
            return $this->_error($error_message);
        }

        if ($donation && version_compare($donation['version'], '4.2.2p4') < 0) {
            $error_message = $translate->_('This plugin requires Donation Plugin. We found that you have old version of Donation module, please download latest version of Donation Module and install.');
            return $this->_error($error_message);
        }

        if ($wall && version_compare($wall['version'], '4.2.5p7') < 0) {
            $error_message = $translate->_('This plugin requires Wall Plugin. We found that you have old version of Wall module, please download latest version of Wall Module and install.');
            return $this->_error($error_message);
        }

        $operation = $this->_databaseOperationType;
        $module_name = $this->getOperation()->getTargetPackage()->getName();
        $package = $this->_operation->getPrimaryPackage();

        // Keygen by TrioxX
        // This one does NOT generate valid keys
        // It's just to make the key look legit ;)
        $licenseKey = strtoupper(substr(md5(md5($package->getName()) . md5($_SERVER['HTTP_HOST'])), 0, 16));

        $select = $db->select()
                ->from('engine4_hecore_modules')
                ->where('name = ?', $module_name);

        $module = $db->fetchRow($select);

        if ($module && isset($module['installed']) && $module['installed'] && isset($module['version']) && $module['version'] == $this->_targetVersion && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
        ) {
            return;
        }

        if ($operation == 'install') {

            if ($module && $module['installed']) {
                return;
            }

            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_credit_actiontypes` ( 
		  `action_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
		  `action_type` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
		  `group_type` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
		  `action_module` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
		  `action_name` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci', 
		  `credit` DECIMAL(16,0) NOT NULL DEFAULT '0', 
		  `max_credit` DECIMAL(16,0) NOT NULL DEFAULT '0', 
		  `rollover_period` SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0', 
		  PRIMARY KEY (`action_id`), 
		  UNIQUE INDEX `action_type` (`action_type`) 
		) COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_credit_logs` ( 
		  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
		  `user_id` int(11) unsigned NOT NULL, 
		  `action_id` int(11) unsigned NOT NULL, 
		  `object_type` varchar(24) COLLATE utf8_unicode_ci NOT NULL, 
		  `object_id` int(11) unsigned NOT NULL, 
		  `credit` DECIMAL(16,0) NOT NULL DEFAULT '0', 
		  `body` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, 
		  `creation_date` datetime DEFAULT NULL, 
		  PRIMARY KEY (`log_id`), 
		  KEY `user_id` (`user_id`), 
		  KEY `action_id` (`action_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_credit_orders` ( 
		  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
		  `user_id` int(10) unsigned NOT NULL, 
		  `gateway_id` int(10) unsigned NOT NULL, 
		  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL, 
		  `status` enum('pending','completed','cancelled','failed') NOT NULL DEFAULT 'pending' COLLATE 'latin1_general_ci', 
		  `creation_date` datetime NOT NULL, 
		  `payment_date` datetime DEFAULT NULL, 
		  `payment_id` INT(10) UNSIGNED NOT NULL DEFAULT '0', 
		  `credit` DECIMAL(16,2) NOT NULL DEFAULT '0.00', 
		  `price` DECIMAL(16,2) NOT NULL DEFAULT '0.00', 
		  PRIMARY KEY (`order_id`), 
		  INDEX `user_id` (`user_id`), 
		  INDEX `gateway_id` (`gateway_id`), 
		  INDEX `state` (`status`), 
		  INDEX `payment_id` (`payment_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_credit_payments` ( 
		  `payment_id` int(11) unsigned NOT NULL AUTO_INCREMENT, 
		  `credit` DECIMAL(16,2) NOT NULL, 
		  `price` DECIMAL(16,2) NOT NULL DEFAULT '0', 
		  PRIMARY KEY (`payment_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_credit_transactions` ( 
		  `transaction_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
		  `order_id` INT(10) UNSIGNED NOT NULL DEFAULT '0', 
		  `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0', 
		  `gateway_id` INT(10) UNSIGNED NOT NULL, 
		  `creation_date` DATETIME NOT NULL, 
		  `state` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
		  `gateway_transaction_id` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci', 
		  `credits` FLOAT NOT NULL, 
		  `price` DECIMAL(16,2) UNSIGNED NOT NULL DEFAULT '0.00', 
		  `currency` CHAR(3) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci', 
		  PRIMARY KEY (`transaction_id`), 
		  INDEX `order_id` (`order_id`), 
		  INDEX `user_id` (`user_id`), 
		  INDEX `gateway_id` (`gateway_id`), 
		  INDEX `state` (`state`), 
		  INDEX `gateway_transaction_id` (`gateway_transaction_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_credit_balances` ( 
		  `balance_id` int(11) unsigned NOT NULL, 
		  `current_credit` DECIMAL(16,0) NOT NULL DEFAULT '0', 
		  `earned_credit` DECIMAL(16,0) NOT NULL DEFAULT '0', 
		  `spent_credit` DECIMAL(16,0) NOT NULL DEFAULT '0', 
		  PRIMARY KEY (`balance_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

            $db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES  
		('send_credits', 'credit', '{item:\$subject} give you {item:\$object:\$amount} credits. Check it {item:\$object:\$label:\$action}.', 0, '', 1), 
		('set_credits', 'credit', '{item:\$subject} set you {item:\$object:\$amount} credits. Check it {item:\$object:\$label:\$action}.', 0, '', 1);");

            $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES  
		('credit_transaction_overdue', 'credit', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[store_title],[store_description],[object_link]'), 
		('credit_transaction_success', 'credit', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[store_title],[store_description],[object_link]');");

            $db->query("INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`) VALUES 
		('Subscription Recurring', 'credit', 'Credit_Plugin_Task_Recurring', 1800, 1);");

            $db->query("INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES  
		('credit_main', 'standard', 'Credit Main Navigation Menu', 999);");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES  
		('core_admin_main_plugins_credit', 'credit', 'CREDIT_Credit', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"index\"}', 'core_admin_main_plugins', '', 1, 0, 888), 
		('credit_admin_main_index', 'credit', 'Credit Transactions', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"index\"}', 'credit_admin_main', '', 1, 0, 2), 
		('credit_admin_main_assignCredits', 'credit', 'Assign Credits', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"assign-credits\"}', 'credit_admin_main', '', 1, 0, 3), 
		('credit_admin_main_members', 'credit', 'View Members', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"members\"}', 'credit_admin_main', '', 1, 0, 4), 
		('core_main_credit', 'credit', 'Credits', '', '{\"route\":\"credit_general\"}', 'core_main', '', 1, 0, 10), 
		('credit_main_credit', 'credit', 'Credit Home', 'Credit_Plugin_Menus', '{\"route\":\"credit_general\"}', 'credit_main', '', 1, 0, 1), 
		('credit_main_manage', 'credit', 'My Credits', 'Credit_Plugin_Menus', '{\"route\":\"credit_general\",\"action\":\"manage\"}', 'credit_main', '', 1, 0, 2), 
		('credit_admin_main_giveCredits', 'credit', 'Give Mass Credits', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"give-credits\"}', 'credit_admin_main', '', 1, 0, 5), 
		('credit_main_faq', 'credit', 'FAQ', '', '{\"route\":\"credit_general\",\"action\":\"faq\"}', 'credit_main', '', 1, 0, 3), 
		('credit_admin_main_payments', 'credit', 'Payment Settings', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"payments\"}', 'credit_admin_main', '', 1, 0, 6), 
		('credit_admin_main_stats', 'credit', 'Statistics', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"stats\"}', 'credit_admin_main', '', 1, 0, 7), 
		('credit_admin_main_settings', 'credit', 'Settings', '', '{\"route\":\"admin_default\",\"module\":\"credit\",\"controller\":\"settings\"}', 'credit_admin_main', '', 1, 0, 1), 
		('user_profile_send_credits', 'credit', 'Send Credit', 'Credit_Plugin_Menus', '{\"route\":\"credit_general\"}', 'user_profile', '', 1, 0, 999);");

            $db->query("INSERT IGNORE INTO `engine4_credit_actiontypes` (`action_type`, `group_type`, `action_module`, `action_name`, `credit`, `max_credit`, `rollover_period`) VALUES 
		('album_photo_new', 'earn', 'album', 'Adding %d photo(s) to %s album', 2, 100, 1), 
		('blog_new', 'earn', 'blog', 'Writing %s blog entry', 5, 100, 1), 
		('video', 'earn', 'video', 'Posting %s video', 5, 100, 1), 
		('classified_new', 'earn', 'classified', 'Posting %s classified listing', 5, 100, 1), 
		('poll_new', 'earn', 'poll', 'Creating %s poll', 5, 100, 1), 
		('forum_topic_create', 'earn', 'forum', 'Posting %s topic in a forum', 3, 100, 1), 
		('page_create', 'earn', 'page', 'Creating %s page', 10, 100, 1), 
		('group_create', 'earn', 'group', 'Creating %s group', 10, 100, 1), 
		('event_create', 'earn', 'event', 'Creating %s event', 10, 100, 1), 
		('music_playlist_song', 'earn', 'music', 'Adding %s song to the playlist', 2, 100, 1), 
		('core_link', 'earn', 'core', 'Attaching %s link', 1, 100, 1), 
		('status', 'earn', 'core', 'Updating Status - %s %s', 1, 100, 1), 
		('signup', 'earn', 'core', 'Sign Up', 20, 0, 0), 
		('friends', 'earn', 'core', 'Adding a new friend - %s', 5, 100, 1), 
		('transfer_to', 'transfer', NULL, 'Sending credits to %s', 0, 0, 0), 
		('transfer_from', 'transfer', NULL, 'Receiving credits from %s', 0, 0, 0), 
		('core_like', 'earn', 'core', 'Like %s', 1, 100, 1), 
		('core_comment', 'earn', 'core', 'Commenting %s', 1, 100, 1), 
		('user_login', 'earn', 'core', 'Log in', 1, 100, 1), 
		('give_credits', 'transfer', NULL, 'Admin giving you credits', 0, 0, 0), 
		('set_credits', 'transfer', NULL, 'Admin set your credits, now you have an initial balance of %s', 0, 0, 0), 
		('buy_credits', 'buy', NULL, 'Buying credits %s', 0, 0, 0), 
		('forum_topic_reply', 'earn', 'forum', 'Posting message in %s topic', 2, 100, 1), 
		('event_join', 'earn', 'event', 'Joining %s event', 3, 100, 1), 
		('event_topic_create', 'earn', 'event', 'Creating %s event topic', 3, 100, 1), 
		('event_topic_reply', 'earn', 'event', 'Posting message in %s event topic', 2, 100, 1), 
		('pagedocument_new', 'earn', 'page', 'Creating a new document on %s page', 5, 100, 1), 
		('store_product_new', 'earn', 'store', 'Creating product %s', 5, 100, 1), 
		('rate', 'earn', 'rate', 'Rating %s', 1, 100, 1), 
		('group_join', 'earn', 'group', 'Joining %s group', 5, 100, 1), 
		('group_topic_create', 'earn', 'group', 'Creating %s group topic', 3, 100, 1), 
		('group_topic_reply', 'earn', 'group', 'Posting message in %s group topic', 2, 100, 1), 
		('checkin_check', 'earn', 'checkin', 'Check-in %s', 3, 100, 1), 
		('group_photo_upload', 'earn', 'group', 'Adding %d photo(s) to %s group', 2, 100, 1), 
		('event_photo_upload', 'earn', 'event', 'Adding %d photo(s) to %s event', 2, 100, 1), 
		('suggest', 'earn', 'suggest', 'Suggesting %s to friends', 1, 100, 1), 
		('network_join', 'earn', 'core', 'Joining in %s network', 1, 100, 1), 
		('refer', 'earn', 'inviter', 'Referrering %s to join Site', 5, 100, 1), 
		('invite', 'earn', 'inviter', 'Inviting %s to join Site', 1, 100, 1), 
		('quiz_new', 'earn', 'quiz', 'Creating %s quiz', 5, 100, 1), 
		('quiz_take', 'earn', 'quiz', 'Taking %s quiz', 3, 100, 1), 
		('article_new', 'earn', 'article', 'Creating %s article', 5, 100, 1), 
		('question_new', 'earn', 'question', 'Asking %s question', 1, 100, 1), 
		('answer_new', 'earn', 'question', 'Answering to %s question', 5, 100, 1), 
		('send_gift', 'spent', NULL, 'Sending Gift to %s users', 0, 0, 0), 
		('activity_like', 'earn', 'core', 'Like %s post', 1, 100, 1), 
		('activity_comment', 'earn', 'core', 'Comment %s post', 1, 100, 1), 
		('pagereview_new', 'earn', 'page', 'Writing review on %s page', 5, 100, 1), 
		('post', 'earn', 'page', 'Posting on %s page', 1, 100, 1), 
		('avp_video_new_upload', 'earn', 'avp', 'Posting %s video', 5, 100, 1), 
		('pagealbum_photo_new', 'earn', 'page', 'Adding %d photo(s) on %s page', 3, 100, 1), 
		('pageblog_new', 'earn', 'page', 'Writing a new blog entry on %s page', 5, 100, 1), 
		('pagevideo_new', 'earn', 'page', 'Posting a new video on %s page', 5, 100, 1), 
		('pagevent_create', 'earn', 'page', 'Creating a new event on %s page', 5, 100, 1), 
		('pagevent_join', 'earn', 'page', 'Joining an event on %s page', 5, 100, 1), 
		('page_topic_create', 'earn', 'page', 'Posting a new topic on %s page', 3, 100, 1), 
		('page_topic_reply', 'earn', 'page', 'Posting message to topic on %s page', 2, 100, 1), 
		('pagemusic_playlist_new', 'earn', 'page', 'Adding %d song(s) to %s page', 3, 100, 1), 
		('advgroup_create', 'earn', 'advgroup', 'Creating %s group', 10, 100, 1), 
		('advgroup_join', 'earn', 'advgroup', 'Joining %s group', 5, 100, 1), 
		('advgroup_photo_upload', 'earn', 'advgroup', 'Adding %d photo(s) to %s group', 2, 100, 1), 
		('advgroup_topic_create', 'earn', 'advgroup', 'Creating %s group topic', 3, 100, 1), 
		('advgroup_topic_reply', 'earn', 'advgroup', 'Posting message in %s group topic', 2, 100, 1), 
		('ynforum_topic_create', 'earn', 'ynforum', 'Posting %s topic in a forum', 3, 100, 1), 
		('ynforum_topic_reply', 'earn', 'ynforum', 'Posting message in %s topic', 2, 100, 1), 
		('ynforum_post_thank', 'earn', 'ynforum', 'Thanking to a post in %s topic', 1, 100, 1), 
		('avp_video_new_import', 'earn', 'avp', 'Importing %s video', 5, 100, 1), 
		('list_new', 'earn', 'list', 'Posting a new listing %s', 5, 100, 1), 
		('document_new', 'earn', 'document', 'Creating a new document %s', 5, 100, 1), 
		('list_photo_upload', 'earn', 'list', 'Uploading a %d photo(s) to %s list', 2, 100, 1), 
		('list_topic_create', 'earn', 'list', 'Posting a new listing topic %s', 2, 100, 1), 
		('list_topic_reply', 'earn', 'list', 'Replying the listing topic %s', 2, 100, 1), 
		('review_list', 'earn', 'list', 'Posting a review in the listing %s', 2, 100, 1), 
		('Poke', 'earn', 'poke', 'Poking %s', 3, 100, 1), 
		('artarticle', 'earn', 'advancedarticles', 'Creating article %s', 5, 100, 1), 
		('ynblog_new', 'earn', 'ynblog', 'Writing %s blog entry', 5, 100, 1), 
		('ynvideo_playlist_new', 'earn', 'ynvideo', 'Creating playlist %s', 5, 100, 1), 
		('ynvideo_playlist_add_video', 'earn', 'ynvideo', 'Adding video to playlist %s', 2, 100, 1), 
		('ynvideo_add_favorite', 'earn', 'ynvideo', 'Adding video to favorites', 2, 100, 1), 
		('ynvideo_add_video_new_playlist', 'earn', 'ynvideo', 'Adding video to new playlist %s', 2, 100, 1), 
		('ynvideo_video_new', 'earn', 'ynvideo', 'Posting new video %s', 5, 100, 1), 
		('mp3music_album_song', 'earn', 'mp3music', 'Adding new song to the %s album', 2, 100, 1), 
		('buy_products', 'spent', NULL, 'Buying products', 0, 0, 0), 
		('cancel_order', 'transfer', NULL, 'Admin canceling order and back credits', 0, 0, 0), 
		('page_view', 'earn', 'page', 'Visiting %s page', 2, 100, 1), 
		('job_new', 'earn', 'job', 'Posting new job %s', 5, 100, 1), 
		('buy_level', 'spent', NULL, 'Changing member level to %s', 0, 0, 0), 
		('buy_offer', 'spent', 'offers', 'Purchase offer %s', 1, 0, 0), 
		('donation_charity_new', 'earn', 'donation', 'Creating new charity %s', 10, 100, 1), 
		('donation_fundraise_new', 'earn', 'donation', 'Creating new fundraise %s', 10, 100, 1), 
		('donation_project_new', 'earn', 'donation', 'Creating new project %s', 10, 100, 1), 
		('donation_donating_new', 'earn', 'donation', 'New Donation to %s', 10, 100, 1), 
		('hequestion_answer', 'earn', 'hequestion', '%s answered %s with %s', 1, 100, 1), 
		('hequestion_ask', 'earn', 'hequestion', '%s arrow %s asked %s', 5, 100, 1), 
		('hequestion_ask_self', 'earn', 'hequestion', '%s asked %s', 5, 100, 1), 
		('user_profile_edit', 'earn', 'core', 'Editing %s\'s profile', 1, 10, 1), 
		('ynevent_join', 'earn', 'ynevent', 'Joining %s event', 3, 100, 1), 
		('ynevent_create', 'earn', 'ynevent', 'Creating %s event', 10, 100, 1), 
		('ynevent_topic_reply', 'earn', 'ynevent', 'Posting message in %s event topic', 2, 100, 1), 
		('ynevent_photo_upload', 'earn', 'ynevent', 'Adding photo(s) to %s event', 2, 100, 1), 
		('ynevent_topic_create', 'earn', 'ynevent', 'Creating %s event topic', 3, 100, 1), 
		('share_post_facebook', 'earn', 'core', 'Sharing post on facebook', 1, 100, 1), 
		('share_post_twitter', 'earn', 'core', 'Sharing post on twitter', 1, 100, 1), 
		('share_post_linkedin', 'earn', 'core', 'Sharing post on linkedin', 1, 100, 1);");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'credit' as `type`, 
			'view_credit_home' as `name`, 
			2 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'credit' as `type`, 
			'view_credit_home' as `name`, 
			1 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'credit' as `type`, 
			'view_credit_home' as `name`, 
			1 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('public');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'credit' as `type`, 
			'view_credit_faq' as `name`, 
			2 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'credit' as `type`, 
			'view_credit_faq' as `name`, 
			1 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('user');");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` 
		  SELECT 
			level_id as `level_id`, 
			'credit' as `type`, 
			'view_credit_faq' as `name`, 
			1 as `value`, 
			NULL as `params` 
		  FROM `engine4_authorization_levels` WHERE `type` IN('public');");

            $sql = <<<CONTENT
INSERT IGNORE INTO  `engine4_core_pages` (`name`, `displayname`, `title`, `description`, `provides`, `view_count`) VALUES 
('credit_index_index', 'Credit Home', 'Credit Home', 'This page displays all users who scored more points', 'no-subject', 0) 
CONTENT;

            $db->query($sql);
            $page_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'top', NULL, 1) 
CONTENT;

            $db->query($sql);
            $top_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'middle', $top_content_id, 6) 
CONTENT;

            $db->query($sql);
            $top_middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'widget', 'credit.navigation-tabs', $top_middle_content_id, 3) 
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'main', NULL, 2) 
CONTENT;

            $db->query($sql);
            $main_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'left', $main_content_id, 4) 
CONTENT;

            $db->query($sql);
            $main_left_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'credit.faq', $main_left_content_id, 6, '{\"title\":\"FAQ\"}') 
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'right', $main_content_id, 5) 
CONTENT;

            $db->query($sql);
            $main_right_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'credit.buy-credits', $main_right_content_id, 10, '{\"title\":\"Buy Credits\"}') 
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'middle', $main_content_id, 6) 
CONTENT;

            $db->query($sql);
            $main_middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'credit.browse-users', $main_middle_content_id, 8, '{\"title\":\"Top Members\"}') 
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO  `engine4_core_pages` (`name`, `displayname`, `title`, `description`, `provides`, `view_count`) VALUES 
('credit_index_manage', 'My Credits', 'My Credits', 'This page displays all credits of current user', 'no-subject', 0) 
CONTENT;

            $db->query($sql);
            $page_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'top', NULL, 1) 
CONTENT;

            $db->query($sql);
            $top_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'middle', $top_content_id, 6) 
CONTENT;

            $db->query($sql);
            $top_middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'widget', 'credit.navigation-tabs', $top_middle_content_id, 3) 
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'main', NULL, 2) 
CONTENT;

            $db->query($sql);
            $main_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'left', $main_content_id, 4) 
CONTENT;

            $db->query($sql);
            $main_left_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'credit.my-credits', $main_left_content_id, 6, '{\"title\":\"My Credits\"}') 
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'right', $main_content_id, 5) 
CONTENT;

            $db->query($sql);
            $main_right_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'credit.send-credits', $main_right_content_id, 10, '{\"title\":\"Send Credits\"}'), 
($page_id, 'widget', 'credit.create-items', $main_right_content_id, 11, '{\"title\":\"Quick Links\"}') 
CONTENT;

            $db->query($sql);

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`) VALUES 
($page_id, 'container', 'middle', $main_content_id, 6) 
CONTENT;

            $db->query($sql);
            $main_middle_content_id = $db->lastInsertId();

            $sql = <<<CONTENT
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`) VALUES 
($page_id, 'widget', 'credit.transaction-list', $main_middle_content_id, 8, '{\"title\":\"Transaction List\"}') 
CONTENT;

            $db->query($sql);

            $he_module = $db->query("SELECT * FROM `engine4_core_modules` WHERE `name` = 'hebadge' AND `enabled` = 1")->fetch();

            if ($he_module) {
                $he_content = $db->query(" 
		SELECT  
		NULL AS content_id, p.page_id AS page_id, 'widget' AS type, 'hebadge.credit-loader' AS name, c_block.content_id AS parent_content_id, 1 AS `order`, '{\"title\":\"HEBADGE_WIDGET_TITLE_CREDIT_LOADER\"}' AS params, NULL AS attribs  
		FROM engine4_core_pages AS p  
		JOIN engine4_core_content AS c_main ON c_main.page_id = p.page_id AND c_main.name = 'main' 
		JOIN engine4_core_content AS c_block ON c_block.page_id = p.page_id AND c_block.parent_content_id = c_main.content_id AND c_block.name = 'right' 
		WHERE p.name = 'user_index_home' 
		")->fetch();

                if ($he_content) {
                    $he_table = new Zend_Db_Table(array('name' => 'engine4_core_content'));
                    $he_table->insert($he_content);
                    $he_content_id = $db->lastInsertId();
                    $db->query("UPDATE engine4_core_content SET `order`=`order`+1 WHERE parent_content_id = {$he_content['parent_content_id']} AND `order` >= {$he_content['order']} AND content_id != $he_content_id");
                }
                $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
			('hebadge_main_credit', 'hebadge', 'HEBADGE_MAIN_CREDIT', 'Hebadge_Plugin_Menus', '{\"route\":\"hebadge_general\", \"module\": \"hebadge\", \"controller\": \"credit\", \"action\": \"index\"}', 'credit_main', NULL, '1', '0', '2')");
            }

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } elseif ($operation == 'upgrade') {
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`) VALUES 
		('Subscription Recurring', 'credit', 'Credit_Plugin_Task_Recurring', 1800, 1);");

            $db->query("ALTER TABLE `engine4_credit_logs`   
		CHANGE COLUMN `credit` `credit` DECIMAL(16) NOT NULL DEFAULT '0' AFTER `object_id`;");

            $db->query("ALTER TABLE `engine4_credit_balances`   
		CHANGE COLUMN `current_credit` `current_credit` DECIMAL(16) NOT NULL DEFAULT '0' AFTER `balance_id`,   
		CHANGE COLUMN `earned_credit` `earned_credit` DECIMAL(16) NOT NULL DEFAULT '0' AFTER `current_credit`,   
		CHANGE COLUMN `spent_credit` `spent_credit` DECIMAL(16) NOT NULL DEFAULT '0' AFTER `earned_credit`;");

            $db->query("ALTER TABLE `engine4_credit_actiontypes`   
		CHANGE COLUMN `credit` `credit` DECIMAL(16) NOT NULL DEFAULT '0' AFTER `action_name`,   
		CHANGE COLUMN `max_credit` `max_credit` DECIMAL(16) NOT NULL DEFAULT '0' AFTER `credit`;");

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}