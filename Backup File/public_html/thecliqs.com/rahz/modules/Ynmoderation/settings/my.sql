INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynmoderation', 'Moderation', 'Moderation', '4.01', 1, 'extra') ;

--
-- Table structure for table `engine4_ynmoderation_modules`
--

CREATE TABLE IF NOT EXISTS `engine4_ynmoderation_modules` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enabled` int(1) DEFAULT '0',
  `moderation_query` varchar(256) COLLATE utf8_unicode_ci DEFAULT '',
  `code_name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `report_query` varchar(256) COLLATE utf8_unicode_ci DEFAULT '',
  `report_object_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `report_field` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynmoderation_modules` 
(`name`, `object_type`, `enabled`, `moderation_query`, `code_name`, `report_query`, `report_object_type`, `report_field`)  
VALUES  
('Blogs', 'blog', '1', 'SELECT `blog_id` AS `id`, `title` AS `title`, `owner_id` AS `creator`, `creation_date` AS `creation_date`, \'Blogs\' AS `module_name` , \'blog\' AS `type` FROM engine4_blog_blogs', 'blog', 'SELECT report_id, user_id, category, description, subject_type, subject_id, creation_date, \'core_report\' AS `report_type` FROM engine4_core_reports WHERE subject_type=\'blog\'', 'core_report', 'subject_id'),
('Events', 'event', '1', 'SELECT `event_id` AS `id`, `title` AS `title`, `user_id` AS `creator`, `creation_date` AS `creation_date`, \'Events\' AS `module_name`, \'event\' AS `type` FROM engine4_event_events', 'event', 'SELECT report_id, user_id, category, description, subject_type, subject_id, creation_date, \'core_report\' AS `report_type` FROM engine4_core_reports WHERE subject_type=\'event\'', 'core_report', 'subject_id'),
('Polls', 'poll', '1', 'SELECT `poll_id` AS `id`, `title` AS `title`, `user_id` AS `creator`, `creation_date` AS `creation_date`, \'Polls\' AS `module_name`, \'poll\' AS `type` FROM engine4_poll_polls', 'poll', 'SELECT report_id, user_id, category, description, subject_type, subject_id, creation_date, \'core_report\' AS `report_type` FROM engine4_core_reports WHERE subject_type=\'poll\'', 'core_report', 'subject_id'),
('Groups', 'group', '1', 'SELECT `group_id` AS `id`, `title` AS `title`, `user_id` AS `creator`, `creation_date` AS `creation_date`, \'Groups\' AS `module_name`, \'group\' AS `type` FROM engine4_group_groups', 'group', 'SELECT report_id, user_id, category, description, subject_type, subject_id, creation_date, \'core_report\' AS `report_type` FROM engine4_core_reports WHERE subject_type=\'group\'', 'core_report', 'subject_id'),
('Forums', 'forum_topic', '1', 'SELECT `topic_id` AS `id`, `title` AS `title`, `user_id` AS `creator`, `creation_date` AS `creation_date`, \'Forums\' AS `module_name`, \'forum_topic\' AS `type` FROM engine4_forum_topics', 'forum', 'SELECT report_id, user_id, category, description, subject_type, subject_id, creation_date, \'core_report\' AS `report_type` FROM engine4_core_reports WHERE subject_type=\'forum_post\'', 'core_report', 'subject_id'),
('Forums', 'forum_post', '1', '', 'forum', '', 'core_report', 'subject_id'),
('File Sharing', 'ynfilesharing_folder', '1', 'SELECT `folder_id` AS `id`, `title` AS `title`, `user_id` AS `creator`, `creation_date` AS `creation_date`, \'File Sharing\' AS `module_name`, \'ynfilesharing_folder\' AS `type` FROM engine4_ynfilesharing_folders WHERE `parent_folder_id` = 0', 'ynfilesharing', '', '', ''),
('Wiki', 'ynwiki_page', '1', 'SELECT `page_id` AS `id`, `title` AS `title`, `user_id` AS `creator`, `creation_date` AS `creation_date`, \'Wiki\' AS `module_name`, \'ynwiki_page\' AS `type` FROM engine4_ynwiki_pages', 'ynwiki', 'SELECT report_id, user_id, `type` AS `category`, `content` AS `description`, \'ynwiki_page\' AS `subject_type`, `page_id` AS `subject_id`, creation_date, \'ynwiki_report\' AS `report_type` FROM engine4_ynwiki_reports', 'ynwiki_report', 'page_id'),
('Ideas', 'ynidea_idea', '1', 'SELECT `idea_id` AS `id`, `title` AS `title`, `user_id` AS `creator`, `creation_date` AS `creation_date`, \'Ideas\' AS `module_name`, \'ynidea_idea\' AS `type` FROM engine4_ynidea_ideas', 'ynidea', 'SELECT report_id, user_id, `type` AS `category`, `content` AS `description`, \'ynidea_idea\' AS `subject_type`, `idea_id` AS `subject_id`, creation_date, \'ynidea_report\' AS `report_type` FROM engine4_ynidea_reports', 'ynidea_report', 'idea_id');
