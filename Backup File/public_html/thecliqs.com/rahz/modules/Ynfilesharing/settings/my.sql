INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  
('ynfilesharing', 'YN - File Sharing', 'YN - File Sharing', '4.02p2', 1, 'extra') ;

--
-- Table structure for table `engine4_ynfilesharing_documents`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfilesharing_documents` (
  `document_id` int(11) NOT NULL,
  `doc_id` bigint(20) NOT NULL,
  `access_key` text COLLATE utf8_unicode_ci NOT NULL,
  `secret_password` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfilesharing_files`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfilesharing_files` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT,
  `folder_id` int(10) DEFAULT '0',
  `parent_type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `user_id` int(10) DEFAULT '0',
  `name` varchar(256) COLLATE utf8_unicode_ci DEFAULT '0',
  `size` int(10) DEFAULT '0',
  `ext` char(16) COLLATE utf8_unicode_ci DEFAULT '0',
  `creation_date` datetime DEFAULT NULL,
  `view_count` int(10) DEFAULT '0',
  `download_count` int(11) DEFAULT '0',
  `share_code` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  KEY `folder_id` (`folder_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfilesharing_folders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfilesharing_folders` (
  `folder_id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_type` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `parent_folder_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `view_count` int(10) DEFAULT '0',
  `size` int(10) DEFAULT '0',
  `user_id` int(10) DEFAULT '0',
  `creation_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `share_code` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`folder_id`),
  KEY `parent_type_parent_id` (`parent_type`,`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

ALTER TABLE `engine4_ynfilesharing_files` ADD COLUMN `status` INT(1) DEFAULT '0' NULL AFTER `share_code`;

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('YN FileSharing Upload To Scribd', 'ynfilesharing_scribd_uploader', 'ynfilesharing', 'Ynfilesharing_Plugin_Job_Upload', NULL, 1, 75, 1);

-- default auths for settings in member level settings
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'auth_create' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'auth_edit' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'auth_delete' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'folder' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');