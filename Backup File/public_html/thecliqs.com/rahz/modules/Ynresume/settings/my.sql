-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_importedlog`
--

DROP TABLE IF EXISTS `engine4_ynresume_importedlog`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_importedlog` (
  `importedlog_id` int(11) unsigned NOT NULL auto_increment,
  `resume_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`importedlog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_endorsenotify`
--

DROP TABLE IF EXISTS `engine4_ynresume_endorsenotify`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_endorsenotify` (
  `endorsenotify_id` int(11) unsigned NOT NULL auto_increment,
  `resume_id` int(11) unsigned NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`endorsenotify_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_skips`
--

DROP TABLE IF EXISTS `engine4_ynresume_skips`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_skips` (
  `skip_id` int(11) unsigned NOT NULL auto_increment,
  `resume_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`skip_id`),
  KEY `resume_user` (`resume_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_industrymaps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_industrymaps` (
`industrymap_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`industry_id` int(11) NOT NULL,
`job_industry_id` int(11) NOT NULL,
PRIMARY KEY (`industrymap_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_skills`
--

DROP TABLE IF EXISTS `engine4_ynresume_skills`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_skills` (
  `skill_id` int(11) unsigned NOT NULL auto_increment,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY  (`skill_id`),
  UNIQUE KEY `text` (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_skillmaps`
--

DROP TABLE IF EXISTS `engine4_ynresume_skillmaps`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_skillmaps` (
  `skillmap_id` int(11) unsigned NOT NULL auto_increment,
  `resume_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `skill_id` int(11) unsigned NOT NULL,
  `creation_date` datetime default NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`skillmap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_languages`
--

DROP TABLE IF EXISTS `engine4_ynresume_languages`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_languages` (
	`language_id` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(128) NOT NULL,
	`resume_id` int(11) unsigned NOT NULL,
	`proficiency` enum('elementary', 'limited working', 'professional working', 'fill working', 'native or bilingual') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY  (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

-- Table structure for table `engine4_ynresume_experiences`
--
DROP TABLE IF EXISTS `engine4_ynresume_experiences`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_experiences` (
`experience_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`resume_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`title` varchar(255) NOT NULL,
`business_id` int(11) unsigned NULL,
`company` varchar(255) NULL,
`description` text NULL,
`location` varchar(255) NULL,
`longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
`start_month` int(11) NULL,
`start_year` YEAR NOT NULL,
`end_month` int(11) NULL,
`end_year` YEAR NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime DEFAULT NULL,
PRIMARY KEY (`experience_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- Table structure for table `engine4_ynresume_projects`
--
DROP TABLE IF EXISTS `engine4_ynresume_projects`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_projects` (
`project_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL,
`resume_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`occupation_type` varchar(64) NOT NULL,
`occupation_id` int(11) unsigned NOT NULL,
`start_month` int(11) NULL,
`start_year` YEAR NOT NULL,
`end_month` int(11) NULL,
`end_year` YEAR NULL,
`url` text NULL,
`description` text NULL,
`on_going` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Table structure for table `engine4_ynresume_projectmembers`
--
DROP TABLE IF EXISTS `engine4_ynresume_projectmembers`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_projectmembers` (
`projectmember_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`project_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`name` varchar(64) NOT NULL,
`order` int(11) unsigned NOT NULL,
PRIMARY KEY (`projectmember_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Table structure for table `engine4_ynresume_publications`
--
DROP TABLE IF EXISTS `engine4_ynresume_publications`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_publications` (
`publication_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`resume_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`title` varchar(128) NOT NULL,
`publisher` varchar(128) NOT NULL,
`publication_date` datetime NULL,
`creation_date` datetime NOT NULL,
`url` text NULL,
`description` text NULL,
PRIMARY KEY (`publication_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Table structure for table `engine4_ynresume_publicationauthors`
--
DROP TABLE IF EXISTS `engine4_ynresume_publicationauthors`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_publicationauthors` (
`publicationauthor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`publication_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`name` varchar(128) NOT NULL,
`order` int(11) unsigned NOT NULL,
PRIMARY KEY (`publicationauthor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- Table structure for table `engine4_ynresume_courses`
--
DROP TABLE IF EXISTS `engine4_ynresume_courses`;
CREATE TABLE IF NOT EXISTS `engine4_ynresume_courses` (
`course_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`resume_id` int(11) unsigned NOT NULL,
`name` varchar(128) NOT NULL,
`number` varchar(30) NOT NULL DEFAULT '',
`associated_type` varchar(64) NOT NULL,
`associated_id` int(11) unsigned NOT NULL,
PRIMARY KEY (`course_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_transactions`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_transactions` (
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
-- Table structure for table `engine4_ynresume_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_orders` (
`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`gateway_id` int(11) unsigned NOT NULL,
`gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
`status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
`creation_date` datetime NOT NULL,
`payment_date` datetime DEFAULT NULL,
`item_id` int(11) unsigned NOT NULL DEFAULT '0',
`price` decimal(16,2) NOT NULL DEFAULT '0',
`service_day_number` int(11) unsigned NOT NULL DEFAULT '0',
`feature_day_number` int(11) unsigned NOT NULL DEFAULT '0',
`currency` char(3),
PRIMARY KEY (`order_id`),
KEY `user_id` (`user_id`),
KEY `gateway_id` (`gateway_id`),
KEY `state` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

-- Table structure for table `engine4_ynresume_certifications`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_certifications` (
`certification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`resume_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NOT NULL,
`name` varchar(255) NOT NULL,
`authority` text NULL,
`license_number` text NULL,
`url` varchar(255) NULL,
`start_month` int(11) NULL,
`start_year` YEAR NOT NULL,
`end_month` int(11) NULL,
`end_year` YEAR NULL,
`expired` tinyint(1) NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
`modified_date` datetime DEFAULT NULL,
PRIMARY KEY (`certification_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynresume_degrees`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_degrees` (
`degree_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(255) NOT NULL ,
PRIMARY KEY (`degree_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `engine4_ynresume_degrees` (`name`) VALUES
('High School'),
('Associated Degree'),
('Bachelor Degree'),
('Master of Business Administration (M.B.A)'),
('Juris Doctor (J.D.)'),
('Doctor of Medicine (M.D.)'),
('Doctor of Philosophy (Ph.D.)'),
('Engineer Degree'),
('Other')
;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynresume_saves`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_saves` (
`save_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`resume_id` int(11) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
PRIMARY KEY (`save_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynresume_views`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_views` (
`view_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`resume_id` int(11) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
`modified_date` datetime DEFAULT NULL,
PRIMARY KEY (`view_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynresume_favourites`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_favourites` (
`favourite_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`resume_id` int(11) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
PRIMARY KEY (`favourite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynresume_resumes`
--
CREATE TABLE IF NOT EXISTS `engine4_ynresume_resumes` (
  `resume_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
  `name` varchar(128) NOT NULL,
  `summary` text DEFAULT NULL,
  `headline` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `company` text DEFAULT NULL,
  `industry_id` int(11) unsigned NOT NULL DEFAULT '0',
  `birth_day` datetime DEFAULT NULL,
  `marial_status` tinyint(1) NOT NULL default '1',
  `gender` tinyint(1) NOT NULL default '1',
  `nationality` varchar(255) NULL,
  `phone` varchar(255) NULL,
  `email` varchar(255) NULL,
  `location` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `contact_location` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_longitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `contact_latitude` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `theme` enum('theme_1','theme_2', 'theme_3','theme_4') NOT NULL DEFAULT 'theme_1',
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `featured` tinyint(1) NOT NULL default '0',
  `serviced` tinyint(1) NOT NULL default '0',
  `feature_expiration_date` datetime DEFAULT NULL,
  `service_expiration_date` datetime DEFAULT NULL,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `endorse_count` int(11) NOT NULL DEFAULT 0,
  `favourite_count` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NULL,
  PRIMARY KEY (`resume_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_resume_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_resume_fields_maps` (
  `field_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynresume_resume_fields_maps`
--

INSERT IGNORE INTO `engine4_ynresume_resume_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_resume_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_resume_fields_meta` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `photo_id` int(11) UNSIGNED DEFAULT NULL,
  `color` text COLLATE utf8_unicode_ci NOT NULL,
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
-- Dumping data for table `engine4_ynresume_resume_fields_meta`
--

INSERT IGNORE INTO `engine4_ynresume_resume_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_resume_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_resume_fields_search` (
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
-- Table structure for table `engine4_ynresume_resume_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_resume_fields_values` (
  `item_id` int(11) unsigned NOT NULL,
  `field_id` int(11) unsigned NOT NULL,
  `index` smallint(3) unsigned NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynresume_resume_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_resume_fields_options` (
  `option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(11) unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynresume_industries`
--
CREATE TABLE IF NOT EXISTS `engine4_ynresume_industries` (
  `industry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `pleft` int(11) unsigned NOT NULL,
  `pright` int(11) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '0',
  `option_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`industry_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynresume_industries`
--

INSERT IGNORE INTO `engine4_ynresume_industries` (`industry_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`,`option_id`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Industries','1');

--
-- Table structure for table `engine4_ynresume_educations`
--
CREATE TABLE IF NOT EXISTS `engine4_ynresume_educations` (
  `education_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `attend_from` YEAR NULL,
  `attend_to` YEAR NULL,
  `study_field` text COLLATE utf8_unicode_ci NULL,
  `degree_id` int(11) NOT NULL,
  `grade` varchar(128) COLLATE utf8_unicode_ci NULL,
  `activity` text COLLATE utf8_unicode_ci NULL,
  `description` text COLLATE utf8_unicode_ci NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`education_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynresume_recommendations`
--
CREATE TABLE IF NOT EXISTS `engine4_ynresume_recommendations` (
  `recommendation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `receiver_id` int(11) unsigned NOT NULL,
  `giver_id` int(11) unsigned NOT NULL,
  `relationship` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `receiver_position_type` enum('experience','education') NOT NULL,
  `receiver_position_id` int(11) NOT NULL,
  `giver_position_type` enum('experience','education') NULL,
  `giver_position_id` int(11) NULL,
  `ask_subject` varchar(128) NULL,
  `ask_message` text NULL,
  `given_message` text NULL,
  `content` text NULL,
  `status` enum('ask','given') NOT NULL,
  `ask_date` datetime NULL,
  `given_date` datetime NULL,
  `show` boolean NOT NULL DEFAULT TRUE,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`recommendation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynresume_badges`
--
CREATE TABLE IF NOT EXISTS `engine4_ynresume_badges` (
  `badge_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `photo_id` int(11) NOT NULL,
  `condition` enum('view', 'completeness', 'endorsements', 'recommendations') NOT NULL,
  `value` text NOT NULL,
  `order` int(11) NOT NULL DEFAULT 999,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`badge_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynresume_awards`
--
CREATE TABLE IF NOT EXISTS `engine4_ynresume_awards` (
  `award_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `occupation_type` enum('experience','education') NULL,
  `occupation_id` int(11) NULL,
  `issuer` varchar(128) COLLATE utf8_unicode_ci NULL,
  `date_month` int(11) NULL,
  `date_year` YEAR NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`award_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynresume_photos`
--
CREATE TABLE IF NOT EXISTS `engine4_ynresume_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_type` varchar(128) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NULL,
  `file_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynresume_faqs`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_faqs` (
  `faq_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('show','hide') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynresume_resumeorder`
--

CREATE TABLE IF NOT EXISTS `engine4_ynresume_resumeorder` (
`resumeorder_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`resume_id` int(11) unsigned NOT NULL,
`order` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
PRIMARY KEY (`resumeorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- change length of column type
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_authorization_permissions` MODIFY `name` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynresume_main', 'standard', 'YN Resume Main Navigation Menu', 999),
('ynresume_recommendation', 'standard', 'YN Resume Recommendations Menu', 999);

-- insert front-end menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_main_ynresume', 'ynresume', 'Resume', '', '{"route":"ynresume_general"}', 'core_main', '', 1, 0, 999),
('ynresume_main_browse_resume', 'ynresume', 'Browse Resume', '', '{"route":"ynresume_general"}', 'ynresume_main', '', 1, 0, 1),
('ynresume_main_create_resume', 'ynresume', 'My Resume', 'Ynresume_Plugin_Menus', '{"route":"ynresume_general","action":"manage"}', 'ynresume_main', '', 1, 0, 2),
('ynresume_main_who_viewed_me', 'ynresume', 'Who viewed me', 'Ynresume_Plugin_Menus', '{"route":"ynresume_general","action":"who-viewed-me"}', 'ynresume_main', '', 1, 0, 3),
('ynresume_main_recommendations', 'ynresume', 'Manage Recommendations', 'Ynresume_Plugin_Menus', '{"route":"ynresume_recommend","action":"received"}', 'ynresume_main', '', 1, 0, 4),
('ynresume_main_import_resume', 'ynresume', 'Import/Export Resume', 'Ynresume_Plugin_Menus', '{"route":"ynresume_general","action":"import"}', 'ynresume_main', '', 1, 0, 5),
('ynresume_main_manage_favourite', 'ynresume', 'Favourite Resumes', 'Ynresume_Plugin_Menus', '{"route":"ynresume_general","action":"my-favourite"}', 'ynresume_main', '', 1, 0, 6),
('ynresume_main_saved_resume', 'ynresume', 'Saved Resumes', 'Ynresume_Plugin_Menus', '{"route":"ynresume_general","action":"my-saved"}', 'ynresume_main', '', 1, 0, 7),
('ynresume_main_faqs', 'ynresume', 'FAQs', '', '{"route":"ynresume_extended","controller":"faqs","action":"index"}', 'ynresume_main', '', 1, 0, 8),

('ynresume_recommendation_received', 'ynresume', 'Received', 'Ynresume_Plugin_Menus', '{"route":"ynresume_recommend","action":"received"}', 'ynresume_recommendation', '', 1, 0, 1),
('ynresume_recommendation_given', 'ynresume', 'Given', 'Ynresume_Plugin_Menus', '{"route":"ynresume_recommend","action":"given"}', 'ynresume_recommendation', '', 1, 0, 2),
('ynresume_recommendation_ask', 'ynresume', 'Ask for recommendations', 'Ynresume_Plugin_Menus', '{"route":"ynresume_recommend","action":"ask"}', 'ynresume_recommendation', '', 1, 0, 3),
('ynresume_recommendation_give', 'ynresume', 'Give recommendations', 'Ynresume_Plugin_Menus', '{"route":"ynresume_recommend","action":"give"}', 'ynresume_recommendation', '', 1, 0, 4);

-- insert back-end menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynresume', 'ynresume', 'YN - Resume', '', '{"route":"admin_default","module":"ynresume","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynresume_admin_settings_global', 'ynresume', 'Global Settings', '', '{"route":"admin_default","module":"ynresume","controller":"settings", "action":"global"}', 'ynresume_admin_main', '', 1),
('ynresume_admin_settings_level', 'ynresume', 'Member Level Settings', '', '{"route":"admin_default","module":"ynresume","controller":"settings", "action":"level"}', 'ynresume_admin_main', '', 2),
('ynresume_admin_main_resumes', 'ynresume', 'Manage Resumes', '', '{"route":"admin_default","module":"ynresume","controller":"resumes", "action":"index"}', 'ynresume_admin_main', '', 3),
('ynresume_admin_main_industries', 'ynresume', 'Industries', '', '{"route":"admin_default","module":"ynresume","controller":"industries", "action":"index"}', 'ynresume_admin_main', '', 4),
('ynresume_admin_main_fields', 'ynresume', 'Manage Custom Fields', '', '{"route":"admin_default","module":"ynresume","controller":"industry-fields", "action":"index"}', 'ynresume_admin_main', '', 5),
('ynresume_admin_main_badge', 'ynresume', 'Badge Management', '', '{"route":"admin_default","module":"ynresume","controller":"badge", "action":"index"}', 'ynresume_admin_main', '', 6),
('ynresume_admin_main_degree', 'ynresume', 'Manage Education Degree', '', '{"route":"admin_default","module":"ynresume","controller":"degree", "action":"index"}', 'ynresume_admin_main', '', 7),
('ynresume_admin_main_color', 'ynresume', 'Color Settings', '', '{"route":"admin_default","module":"ynresume","controller":"color", "action":"index"}', 'ynresume_admin_main', '', 8),
('ynresume_admin_main_transactions', 'ynresume', 'Manage Transactions', '', '{"route":"admin_default","module":"ynresume","controller":"transactions", "action":"index"}', 'ynresume_admin_main', '', 9),
('ynresume_admin_main_faqs', 'ynresume', 'Manage FAQs', '', '{"route":"admin_default","module":"ynresume","controller":"faqs", "action":"index"}', 'ynresume_admin_main', '', 10);

UPDATE `engine4_core_menuitems` SET `menu` = 'ynresume_main_more' where `name` = 'ynresume_main_manage_favourite';
UPDATE `engine4_core_menuitems` SET `menu` = 'ynresume_main_more' where `name` = 'ynresume_main_saved_resume';
UPDATE `engine4_core_menuitems` SET `menu` = 'ynresume_main_more' where `name` = 'ynresume_main_faqs';
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynresume_main_more', 'ynresume', 'More +', '', '{"uri":"javascript:void(0);"}', 'ynresume_main', 'ynresume_main_more', 1, 0, 7);

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Ynresume Check Feature And Service', 'ynresume', 'Ynresume_Plugin_Task_CheckFeatureAndService', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);

-- add  View Resume menu on user profile
INSERT IGNORE INTO  `engine4_core_menuitems` (`name` ,`module` ,`label` ,`plugin` ,`params` ,`menu` ,`submenu` ,`enabled` ,`custom` ,`order`)VALUES 
('user_profile_resume',  'ynresume',  'View Resume',  'Ynresume_Plugin_Menus',  '',  'user_profile', '' ,  '1',  '0',  '999');


--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynresume_resume_create', 'ynresume', '{item:$subject} create the resume {item:$object}', 1, 5, 1, 1, 1, 1);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynresume_asked_recommendation', 'ynresume', '{item:$subject} has sent you a {item:$object:request} for a recommendation.', 0, ''),
('ynresume_given_recommendation', 'ynresume', '{item:$subject} has written a recommendation for your {item:$object:resume}.', 0, ''),
('ynresume_edited_recommendation', 'ynresume', '{item:$subject} has updated a recommendation on your {item:$object:resume}.', 0, '');

--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynresume_asked_recommendation', 'ynresume', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[ask_subject],[ask_message],[position]'),
('notify_ynresume_given_recommendation', 'ynresume', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[given_message],[place]'),
('notify_ynresume_edited_recommendation', 'ynresume', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[message],[place]');

-- insert default permissions of member level settings
-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_general_info' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_summary' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_experience' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_education' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_skill' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_project' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_language' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_course' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_honor_award' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_publication' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_certification' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_contact' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynresume_resume' as `type`,
    'auth_recommendation' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'max_skill' as `name`,
	3 as `value`,
	20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'max_friend' as `name`,
	3 as `value`,
	3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'max_photo' as `name`,
	3 as `value`,
	6 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'use_credit' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'edit' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'delete' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'endorse' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'recommend' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'service' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'general_info' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'summary' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'experience' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'education' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'skill' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'project' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'language' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'course' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'honor_award' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'publication' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'certification' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'contact' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'recommendation' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'max_skill' as `name`,
	3 as `value`,
	20 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'max_friend' as `name`,
	3 as `value`,
	3 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'max_photo' as `name`,
	3 as `value`,
	6 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'use_credit' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'edit' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'delete' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'endorse' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'recommend' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'service' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'general_info' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'summary' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'experience' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'education' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'skill' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'project' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'language' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'course' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'honor_award' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'publication' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'certification' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'contact' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'recommendation' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'general_info' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'summary' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'experience' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'education' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'skill' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'project' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'language' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'course' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'honor_award' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'publication' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'certification' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'contact' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_resume' as `type`,
	'recommendation' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

-- auth for view recommendation
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynresume_recommendation' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public','user','moderator','admin');