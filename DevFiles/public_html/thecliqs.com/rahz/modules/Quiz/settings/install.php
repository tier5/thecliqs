<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Quiz_Installer extends Engine_Package_Installer_Module {

    public function onPreInstall() {
        parent::onPreInstall();

        $db = $this->getDb();
        $translate = Zend_Registry::get('Zend_Translate');

        $select = $db->select()
                ->from('engine4_core_modules')
                ->where('name = ?', 'hecore')
                ->where('enabled = ?', 1);

        $hecore = $db->fetchRow($select);

        if (!$hecore) {
            $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
            return $this->_error($error_message);
        }

        if (version_compare($hecore['version'], '4.2.0p1') < 0) {
            $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
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

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_quiz_answers` (
		  `answer_id` int(11) NOT NULL auto_increment,
		  `question_id` int(11) NOT NULL,
		  `result_id` int(11) NOT NULL,
		  `label` text NOT NULL,
		  PRIMARY KEY  (`answer_id`),
		  KEY `question_id` (`question_id`),
		  KEY `result_id` (`result_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_quiz_categories` (
		  `category_id` int(11) NOT NULL auto_increment,
		  `category_name` varchar(255) NOT NULL,
		  PRIMARY KEY  (`category_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_quiz_takes` (
		  `take_id` int(11) NOT NULL auto_increment,
		  `user_id` int(11) NOT NULL,
		  `quiz_id` int(11) NOT NULL,
		  `result_id` int(11) NOT NULL,
		  `answers` text NOT NULL,
		  `took_date` datetime NOT NULL,
		  PRIMARY KEY  (`take_id`),
		  KEY `user_id` (`user_id`),
		  KEY `quiz_id` (`quiz_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_quiz_questions` (
		  `question_id` int(11) NOT NULL auto_increment,
		  `quiz_id` int(11) NOT NULL,
		  `text` text NOT NULL,
		  `photo_id` int(11) default NULL,
		  PRIMARY KEY  (`question_id`),
		  KEY `quiz_id` (`quiz_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_quiz_quizs` (
		  `quiz_id` int(11) NOT NULL auto_increment,
		  `category_id` int(11) NOT NULL default '0',
		  `user_id` int(11) NOT NULL,
		  `title` varchar(255) NOT NULL default '',
		  `description` text,
		  `photo_id` int(11) default NULL,
		  `published` tinyint(1) NOT NULL default '0',
		  `approved` tinyint(1) NOT NULL default '1',
		  `creation_date` datetime NOT NULL,
		  `modified_date` datetime default NULL,
		  `comment_count` int(11) NOT NULL default '0',
		  `view_count` int(11) NOT NULL default '0',
		  `take_count` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`quiz_id`),
		  KEY `user_id` (`user_id`),
		  KEY `category_id` (`category_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_quiz_results` (
		  `result_id` int(11) NOT NULL auto_increment,
		  `quiz_id` int(11) NOT NULL,
		  `title` varchar(255) NOT NULL default '',
		  `description` text,
		  `photo_id` int(11) default NULL,
		  PRIMARY KEY  (`result_id`),
		  KEY `quiz_id` (`quiz_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_quiz_choices` (
		  `choice_id` int(11) NOT NULL auto_increment,
		  `quiz_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  `answer_id` int(11) NOT NULL,
		  PRIMARY KEY  (`choice_id`),
		  KEY `quiz_id` (`quiz_id`,`user_id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("INSERT IGNORE INTO `engine4_quiz_categories` (`category_id`, `category_name`) VALUES
		  (1, 'Arts & Culture'),
		  (2, 'Business'),
		  (3, 'Entertainment'),
		  (5, 'Family & Home'),
		  (6, 'Health'),
		  (7, 'Recreation'),
		  (8, 'Personal'),
		  (9, 'Shopping'),
		  (10, 'Society'),
		  (11, 'Sports'),
		  (12, 'Technology'),
		  (13, 'Other');");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
		  ('core_main_quiz', 'quiz', 'Quizzes', '', '{\"route\":\"quiz_browse\"}', 'core_main', '', 4),
		  ('core_sitemap_quiz', 'quiz', 'Quizzes', '', '{\"route\":\"quiz_browse\"}', 'core_sitemap', '', 4),
		  ('core_main_quiz', 'quiz', 'Quizzes', '', '{\"route\":\"default\",\"module\":\"quiz\"}', 'core_main', '', 3),
		  ('core_admin_main_plugins_quiz', 'quiz', 'HE - Quizzes', '', '{\"route\":\"admin_default\",\"module\":\"quiz\",\"controller\":\"settings\"}', 'core_admin_main_plugins', '', 888),
		  ('quiz_admin_main_quizzes', 'quiz', 'View Quizzes', '', '{\"route\":\"admin_default\",\"module\":\"quiz\",\"controller\":\"quizzes\"}', 'quiz_admin_main', '', 1),
		  ('quiz_admin_main_settings', 'quiz', 'Quiz Settings', '', '{\"route\":\"admin_default\",\"module\":\"quiz\",\"controller\":\"settings\"}', 'quiz_admin_main', '', 2),
		  ('quiz_admin_main_level', 'quiz', 'Quiz Level Settings', '', '{\"route\":\"admin_default\",\"module\":\"quiz\",\"controller\":\"level\"}', 'quiz_admin_main', '', 3),
		  ('quiz_admin_main_categories', 'quiz', 'Categories', '', '{\"route\":\"admin_default\",\"module\":\"quiz\",\"controller\":\"categories\"}', 'quiz_admin_main', '', 4);");

            $db->query("INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
		  ('quiz_new', 'quiz', '{item:\$subject} created a new quiz:', 1, 5, 1, 3, 1, 1),
		  ('quiz_take', 'quiz', '{item:\$subject} took a quiz: {item:\$object:quiz}', 1, 1, 1, 1, 0, 0),
		  ('comment_quiz', 'quiz', '{item:\$subject} commented on {item:\$owner}''s {item:\$object:quiz}', 1, 1, 1, 1, 1, 0);");

            $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
		  (1, 'quiz', 'create', 1, NULL),
		  (1, 'quiz', 'edit', 1, NULL),
		  (1, 'quiz', 'delete', 1, NULL),
		  (1, 'quiz', 'view', 1, NULL),
		  (1, 'quiz', 'comment', 1, NULL),
		  (1, 'quiz', 'take', 1, NULL),
		  (1, 'quiz', 'auth_comment', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),
		  (1, 'quiz', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
		  (1, 'quiz', 'auth_view', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),

		  (2, 'quiz', 'create', 1, NULL),
		  (2, 'quiz', 'edit', 1, NULL),
		  (2, 'quiz', 'delete', 1, NULL),
		  (2, 'quiz', 'view', 1, NULL),
		  (2, 'quiz', 'comment', 1, NULL),
		  (2, 'quiz', 'take', 1, NULL),
		  (2, 'quiz', 'auth_comment', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),
		  (2, 'quiz', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
		  (2, 'quiz', 'auth_view', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),

		  (3, 'quiz', 'create', 1, NULL),
		  (3, 'quiz', 'edit', 1, NULL),
		  (3, 'quiz', 'delete', 1, NULL),
		  (3, 'quiz', 'view', 1, NULL),
		  (3, 'quiz', 'comment', 1, NULL),
		  (3, 'quiz', 'take', 1, NULL),
		  (3, 'quiz', 'auth_comment', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),
		  (3, 'quiz', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
		  (3, 'quiz', 'auth_view', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),

		  (4, 'quiz', 'create', 1, NULL),
		  (4, 'quiz', 'delete', 1, NULL),
		  (4, 'quiz', 'view', 1, NULL),
		  (4, 'quiz', 'comment', 1, NULL),
		  (4, 'quiz', 'take', 1, NULL),
		  (4, 'quiz', 'auth_comment', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),
		  (4, 'quiz', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
		  (4, 'quiz', 'auth_view', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),

		  (5, 'quiz', 'view', 1, NULL),
		  (5, 'quiz', 'auth_comment', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]'),
		  (5, 'quiz', 'auth_html', 3, 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'),
		  (5, 'quiz', 'auth_view', 5, '[\"everyone\",\"owner_network\",\"owner_member_member\",\"owner_member\",\"owner\"]');");

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } else { //$operation = upgrade|refresh
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}