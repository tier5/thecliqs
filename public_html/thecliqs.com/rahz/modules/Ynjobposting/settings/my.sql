-- --------------------------------------------------------

--
-- Change table permissions (change length of column type)
--

ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_jobtypes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_jobtypes` (
  `jobtype_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`jobtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynjobposting_jobtypes` (`title`) VALUES
('Full-time'),
('Part-time'),
('Unpaid'),
('Internship'),
('Contractor'),
('Freelancer');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_joblevels`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_joblevels` (
  `joblevel_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`joblevel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynjobposting_joblevels` (`title`) VALUES
('New Grad/Entry Level'),
('Experienced (Non-Manager)'),
('Team Leader/Supervisor'),
('Manager'),
('Vice Director'),
('Director'),
('CEO'),
('Vice President'),
('President');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_sentjobs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_sentjobs` (
  `sentjob_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(11) unsigned NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`sentjob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_alerts`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_alerts` (
  `alert_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `industry_id` int(11) unsigned NOT NULL,
  `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `within` int(11) NULL,
  `level_id` int(11)  DEFAULT '0',
  `type_id` int(11) DEFAULT '0',
  `salary` decimal(16,2) NULL,
  `currency` char(3) NULL,
  `email` text NOT NULL,
  `ip` varbinary(16),
  PRIMARY KEY (`alert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_mailtemplates`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_mailtemplates` (
  `mailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `vars` varchar(255) NOT NULL,
  PRIMARY KEY (`mailtemplate_id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


INSERT IGNORE INTO `engine4_ynjobposting_mailtemplates` (`mailtemplate_id`, `type`, `vars`) VALUES
(2, 'ynjobposting_jobalert', '');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_follows`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_follows` (
  `follow_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `active` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (`follow_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_orders` (
  `order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `gateway_id` int(11) unsigned NOT NULL,
  `gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
  `creation_date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `package_id` int(11) unsigned NOT NULL DEFAULT '0',
  `item_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` text NOT NULL,
  `price` decimal(16,2) NOT NULL DEFAULT '0',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `number_day` int(11) unsigned NOT NULL DEFAULT '0',
  `currency` char(3),
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_id` (`gateway_id`),
  KEY `state` (`status`),
  KEY `package_id` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_companyinfos`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_companyinfos` (
  `companyinfo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `header` text  NULL,
  `content` text  NULL,
  `company_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`companyinfo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_company_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_company_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynjobposting_company_fields_maps`
--

INSERT IGNORE INTO `engine4_ynjobposting_company_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_company_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_company_fields_meta` (
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
-- Dumping data for table `engine4_ynjobposting_company_fields_meta`
--

INSERT IGNORE INTO `engine4_ynjobposting_company_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_company_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_company_fields_search` (
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
-- Table structure for table `engine4_ynjobposting_company_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_company_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_company_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_company_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynjobposting_industries`
--
CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_industries` (
  `industry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `pleft` int(11) unsigned NOT NULL,
  `pright` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '0',
  `option_id` int(11) NOT NULL,
  PRIMARY KEY (`industry_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `engine4_ynjobposting_industries`
--

INSERT IGNORE INTO `engine4_ynjobposting_industries` (`industry_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Industries','0');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_companies`
--
CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_companies` (
  `company_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `location` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `website` text DEFAULT NULL,
  `from_employee` int DEFAULT NULL,
  `to_employee` int DEFAULT NULL,
  `contact_name` text NOT NULL,
  `contact_email` text NOT NULL,
  `contact_phone` text NOT NULL,
  `contact_fax` text DEFAULT NULL,
  `cover_photo` int(11) UNSIGNED DEFAULT NULL,
  `photo_id` int(11) UNSIGNED DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `deleted` boolean NOT NULL DEFAULT 0,
  `status` enum('published', 'closed', 'deleted') NOT NULL DEFAULT 'published', 
  `sponsored` boolean NOT NULL DEFAULT 0,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_transactions` (
`transaction_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`payment_transaction_id` varchar(128),
`creation_date` datetime NOT NULL,
`status` enum('initialized','expired','pending','completed','canceled') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`type` text NOT NULL,
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
-- Table structure for table `engine4_ynjobposting_packages`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_packages` (
  `package_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `price` decimal(16,2) unsigned NOT NULL,
  `currency` char(3),
  `valid_amount` int(11) unsigned,
  `valid_period` ENUM('day') NOT NULL,
  `description` text,
  `show` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_jobs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_jobs` (
`job_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`company_id` int(11) NOT NULL,
`industry_id` int(11) NOT NULL, 
`user_id` int(11) NOT NULL,
`level` int(11) NOT NULL DEFAULT '0',
`type` int(11) NOT NULL DEFAULT '0',
`title` text NOT NULL,
`description` text NOT NULL,
`skill_experience` text NOT NULL,
`language_prefer` text NULL,
`education_prefer` enum('highschool', 'associated', 'bachelor', 'master', 'doctorate') NOT NULL,
`salary_from` decimal(16,2) NULL,
`salary_to` decimal(16,2) NULL,
`salary_currency` CHAR( 3 ) NULL,
`working_place` text NOT NULL,
`longitude` varchar(64) NOT NULL,
`latitude` varchar(64) NOT NULL,
`working_time` text NOT NULL,  
`creation_date` datetime NOT NULL,
`view_count` int(11) DEFAULT '0',
`candidate_count` int(11) DEFAULT '0',
`status` enum('draft', 'pending', 'denied', 'published', 'ended', 'expired', 'deleted') NOT NULL,
`expiration_date` datetime DEFAULT NULL,
`approved_date` datetime DEFAULT NULL,
`number_day` int(11) NOT NULL DEFAULT '0',
`featured` boolean NOT NULL DEFAULT 0,
`share_count` int(11) NOT NULL DEFAULT 0,
`click_count` int(11) NOT NULL DEFAULT 0,
PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_faqs` (
`faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
`order` int(11) NOT NULL,
`created_date` datetime NOT NULL,
PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_industry_company_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_industry_company_maps` (
`industrymap_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`company_id` int(11) NOT NULL,
`industry_id` int(11) NOT NULL,
`main` boolean NOT NULL DEFAULT 0,
PRIMARY KEY (`industrymap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynjobposting_features`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_features` (
  `feature_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `period` INT(11) NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`feature_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynjobposting_sponsors`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_sponsors` (
  `sponsor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `period` INT(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`sponsor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynjobposting_jobinfos`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_jobinfos` (
  `jobinfo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `header` text  NULL,
  `content` text  NULL,
  `job_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`jobinfo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Submission customed fields
CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_submissions` (
  `submission_id` INT(11) NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) UNSIGNED NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `form_title` TEXT COLLATE utf8_unicode_ci,
  `form_description` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `show_company_logo` TINYINT(1) NOT NULL DEFAULT '1',
  `show_job_title` TINYINT(1) NOT NULL DEFAULT '1',
  `show_company_name` TINYINT(1) NOT NULL DEFAULT '1',
  `show_job_location` TINYINT(1) NOT NULL DEFAULT '1',
  `allow_video` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`submission_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_submission_fields_meta` (
  `field_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) UNSIGNED NOT NULL,
  `type` VARCHAR(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `required` TINYINT(1) NOT NULL DEFAULT '0',
  `enabled` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`field_id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_submission_fields_options` (
  `option_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` INT(11) UNSIGNED NOT NULL,
  `label` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_submission_fields_values` (
  `item_id` INT(11) UNSIGNED NOT NULL,
  `field_id` INT(11) UNSIGNED NOT NULL,
  `value` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `privacy` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynjobposting_jobapplies`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_jobapplies` (
  `jobapply_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `video_id` int(11) unsigned NULL,
  `video_link` text NULL,
  `status` enum('pending','passed','rejected') default 'pending' NULL,
  `owner_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `resume` tinyint(1) NOT NULL DEFAULT '0',   
  PRIMARY KEY (`jobapply_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynjobposting_resumefiles`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_resumefiles` (
  `resumefile_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jobapply_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `file_name` text NOT NULL,
  PRIMARY KEY (`resumefile_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynjobposting_savejobs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_savejobs` (
  `savejob_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `job_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`savejob_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynjobposting_applynotes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_applynotes` (
  `applynote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jobapply_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `content` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `creation_date` datetime,
  PRIMARY KEY (`applynote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynjobposting_main', 'standard', 'YN Job Posting Main Navigation Menu', 999),
('ynjobposting_profile_company', 'standard', 'YN Job Posting Profile Company Options Menu', 999);

-- insert back-end menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynjobposting', 'ynjobposting', 'YN - Job Posting', '', '{"route":"admin_default","module":"ynjobposting","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynjobposting_admin_settings_global', 'ynjobposting', 'Global Settings', '', '{"route":"admin_default","module":"ynjobposting","controller":"settings", "action":"global"}', 'ynjobposting_admin_main', '', 1),
('ynjobposting_admin_settings_level', 'ynjobposting', 'Member Level Settings', '', '{"route":"admin_default","module":"ynjobposting","controller":"settings", "action":"level"}', 'ynjobposting_admin_main', '', 2),
('ynjobposting_admin_manage_industries', 'ynjobposting', 'Industries', '', '{"route":"admin_default","module":"ynjobposting","controller":"industries", "action":"index"}', 'ynjobposting_admin_main', '', 3),
('ynjobposting_admin_manage_packages', 'ynjobposting', 'Packages', '', '{"route":"admin_default","module":"ynjobposting","controller":"packages", "action":"index"}', 'ynjobposting_admin_main', '', 4),
('ynjobposting_admin_manage_jobs', 'ynjobposting', 'Manage Jobs', '', '{"route":"admin_default","module":"ynjobposting","controller":"jobs", "action":"index"}', 'ynjobposting_admin_main', '', 5),
('ynjobposting_admin_manage_companies', 'ynjobposting', 'Manage Companies', '', '{"route":"admin_default","module":"ynjobposting","controller":"companies", "action":"index"}', 'ynjobposting_admin_main', '', 6),
('ynjobposting_admin_manage_jobtypes', 'ynjobposting', 'Manage Job Types', '', '{"route":"admin_default","module":"ynjobposting","controller":"jobtypes", "action":"index"}', 'ynjobposting_admin_main', '', 7),
('ynjobposting_admin_manage_joblevels', 'ynjobposting', 'Manage Job Levels', '', '{"route":"admin_default","module":"ynjobposting","controller":"joblevels", "action":"index"}', 'ynjobposting_admin_main', '', 8),
('ynjobposting_admin_main_transactions', 'ynjobposting', 'Manage Transactions', '', '{"route":"admin_default","module":"ynjobposting","controller":"transactions", "action":"index"}', 'ynjobposting_admin_main', '', 9),
('ynjobposting_admin_main_faqs', 'ynjobposting', 'Manage FAQs', '', '{"route":"admin_default","module":"ynjobposting","controller":"faqs", "action":"index"}', 'ynjobposting_admin_main', '', 10),
('core_main_ynjobposting', 'ynjobposting', 'Job Posting', '', '{"route":"ynjobposting_general"}', 'core_main', '', 999);

-- insert front-end menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynjobposting_main_browse_job', 'ynjobposting', 'Browse Jobs', '', '{"route":"ynjobposting_general"}', 'ynjobposting_main', '', 1, 0, 1),
('ynjobposting_main_browse_company', 'ynjobposting', 'Browse Companies', '', '{"route":"ynjobposting_extended","controller":"company"}', 'ynjobposting_main', '', 1, 0, 2),
('ynjobposting_main_manage_job', 'ynjobposting', 'My Jobs', 'Ynjobposting_Plugin_Menus', '{"route":"ynjobposting_job","controller":"jobs","action":"manage"}', 'ynjobposting_main', '', 1, 0, 3),
('ynjobposting_main_manage_company', 'ynjobposting', 'My Companies', 'Ynjobposting_Plugin_Menus', '{"route":"ynjobposting_extended","controller":"company","action":"manage"}', 'ynjobposting_main', '', 1, 0, 4),
('ynjobposting_main_manage_follow_company', 'ynjobposting', 'My Following Companies', 'Ynjobposting_Plugin_Menus', '{"route":"ynjobposting_extended","controller":"company","action":"manage-follow"}', 'ynjobposting_main', '', 1, 0, 5),
('ynjobposting_main_create_job', 'ynjobposting', 'Create New Job', 'Ynjobposting_Plugin_Menus', '{"route":"ynjobposting_job","controller":"jobs","action":"create"}', 'ynjobposting_main', '', 1, 0, 6),
('ynjobposting_main_create_company', 'ynjobposting', 'Create New Company', 'Ynjobposting_Plugin_Menus', '{"route":"ynjobposting_extended","controller":"company","action":"create"}', 'ynjobposting_main', '', 1, 0, 7),
('ynjobposting_main_faqs', 'ynjobposting', 'FAQs', '', '{"route":"ynjobposting_extended","controller":"faqs","action":"index"}', 'ynjobposting_main', '', 1, 0, 8);

-- insert company-profile menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynjobposting_profile_company_sponsor','ynjobposting','Sponsor','Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 1),
('ynjobposting_profile_company_edit', 'ynjobposting', 'Edit Company Info', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 2),
('ynjobposting_profile_company_edit_submission_form', 'ynjobposting', 'Edit Submission Form', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 3),
('ynjobposting_profile_company_manage_posted_job', 'ynjobposting', 'Manage Posted Jobs', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 4),
('ynjobposting_profile_company_close', 'ynjobposting', 'Close This Company', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 5),
('ynjobposting_profile_company_delete', 'ynjobposting', 'Delete This Company', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 6),
('ynjobposting_profile_company_share', 'ynjobposting', 'Share', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 7),
('ynjobposting_profile_company_report', 'ynjobposting', 'Report', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 8),
('ynjobposting_profile_company_follow', 'ynjobposting', 'Follow', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 9),
('ynjobposting_profile_company_contact', 'ynjobposting', 'Contact', 'Ynjobposting_Plugin_Company_Menus', '', 'ynjobposting_profile_company', '', 10);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynjobposting_company_transaction', 'ynjobposting', ' A new transaction has been made for the company {item:$object}.', 0, ''),
('ynjobposting_company_follow', 'ynjobposting', '{item:$subject} starts to follow your company {item:$object}.', 0, ''),
('ynjobposting_company_create', 'ynjobposting', 'The company {item:$object} has just been created in Job Postings.', 0, ''),
('ynjobposting_company_edited', 'ynjobposting', 'The company {item:$object} has just been edited.', 0, ''),
('ynjobposting_company_closed', 'ynjobposting', 'The company {item:$object} has just been closed.', 0, ''),
('ynjobposting_company_published', 'ynjobposting', 'The company {item:$object} has just been published.', 0, ''),
('ynjobposting_company_deleted', 'ynjobposting', 'The company {item:$object} has just been deleted.', 0, ''),
('ynjobposting_company_sponsored', 'ynjobposting', 'The company {item:$object} has just been sponsored.', 0, ''),
('ynjobposting_company_unsponsored', 'ynjobposting', 'The company {item:$object} has just been unsponsored.', 0, ''),
('ynjobposting_job_transaction', 'ynjobposting', ' A new transaction has been made for the job {item:$object}.', 0, ''),
('ynjobposting_job_edited', 'ynjobposting', 'The job {item:$object} has just been edited.', 0, ''),
('ynjobposting_job_published', 'ynjobposting', 'The job {item:$object} has just been published.', 0, ''),
('ynjobposting_job_ended', 'ynjobposting', 'The job {item:$object} has just been ended.', 0, ''),
('ynjobposting_job_deleted', 'ynjobposting', 'The job {item:$object} has just been deleted.', 0, ''),
('ynjobposting_job_expired', 'ynjobposting', 'The job {item:$object} has just been expired.', 0, ''),
('ynjobposting_job_create', 'ynjobposting', 'The job {item:$object} has just been created under the company {item:$subject}.', 0, ''),
('ynjobposting_job_follow', 'ynjobposting', '{item:$subject} has create a new {item:$object:job}.', 0, ''),
('ynjobposting_job_approve', 'ynjobposting', 'The job {item:$object} has just been approved and published.', 0, ''),
('ynjobposting_job_deny', 'ynjobposting', 'The job {item:$object} has just been denied. Please edit that job info and re-submit for approval.', 0, ''),
('ynjobposting_job_featured', 'ynjobposting', 'The job {item:$object} has just been featured.', 0, ''),
('ynjobposting_job_unfeatured', 'ynjobposting', 'The job {item:$object} has just been unfeatured.', 0, ''),
('ynjobposting_job_applied', 'ynjobposting', 'There is a new applicant has just been applied for the job {item:$object}.', 0, '');

--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynjobposting_company_transaction', 'ynjobposting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynjobposting_job_transaction', 'ynjobposting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynjobposting_job_follow', 'ynjobposting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynjobposting_job_approve', 'ynjobposting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynjobposting_job_deny', 'ynjobposting', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');


--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynjobposting_job_create', 'ynjobposting', '{item:$subject} add a new job:', 1, 5, 1, 1, 1, 1),
('ynjobposting_company_create', 'ynjobposting', '{item:$subject} add a new company:', 1, 5, 1, 1, 1, 1);


-- default db for permission

-- ynjobposting
-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting' as `type`,
    'use_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting' as `type`,
    'max_company' as `name`,
    5 as `value`,
    3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting' as `type`,
    'max_job' as `name`,
    5 as `value`,
    3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting' as `type`,
    'use_credit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting' as `type`,
    'max_company' as `name`,
    5 as `value`,
    3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting' as `type`,
    'max_job' as `name`,
    5 as `value`,
    3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- ynjobposting_company
-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'close' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'sponsor' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'close' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'sponsor' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_company' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- ynjobposting_job
-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","registered","network","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'end' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'apply' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'autoapprove' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'end' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'apply' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'autoapprove' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- PACKAGE
-- ADMIN - MOD
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_package' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_package' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_package' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- permissions for share jobs
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'share' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');

-- permissions for print jobs
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'print' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user', 'public');

-- permissions for report jobs
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynjobposting_job' as `type`,
    'report' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user', 'public');

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Ynjobposting Job Alert', 'ynjobposting', 'Ynjobposting_Plugin_Task_JobAlert', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);


INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Ynjobposting Check Featured Status', 'ynjobposting', 'Ynjobposting_Plugin_Task_CheckFeaturedStatus', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);