INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('viewed', 'Who Viewed Me', 'This package will show the list of users who viewed your profile.', '4.8.6', 1, 'extra') ;
CREATE TABLE IF NOT EXISTS `engine4_viewed_viewmes` (
`viewme_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                       `user_id` int(11) NOT NULL default '0',     
                       `profile_id` int(11) NOT NULL default '0',  
                       `ip` varchar(100) default NULL,             
                       `datetime` datetime default NULL,
                       `count` INT(10) NOT NULL DEFAULT '0',        
                       PRIMARY KEY  (`viewme_id`)       
                     );

CREATE TABLE IF NOT EXISTS `engine4_viewed_membercounts` (
  `membercount_id` int(10) NOT NULL AUTO_INCREMENT,
  `level_id` int(10) NOT NULL,
  `view_count` int(10) NOT NULL,
  PRIMARY KEY (`membercount_id`)
);

ALTER TABLE `engine4_viewed_membercounts`  ADD `package_id` INT(10) NOT NULL;

INSERT INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES ('profile_view', 'user', '{item:$subject} has viewed your profile.', '0', '', '1');

INSERT INTO `engine4_core_mailtemplates` (`mailtemplate_id`, `type`, `module`, `vars`) VALUES
(null, 'notify_profile_view', 'user', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

INSERT INTO `engine4_core_menuitems` (`id`, `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES (NULL, 'core_admin_main_whoviewedme_manage', 'viewed', 'Who Viewed Me', '', '{"route":"admin_default","module":"viewed","controller":"index","action":"setting"}', 'core_admin_main_plugins', '', '1', '0', '1');

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES ('whoviewedme_admin_main', 'standard', ' Who Viewed Me Navigation Menu', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('whoviewedme_admin_main_settings', 'viewed', 'Settings', '', '{"route":"admin_default","module":"viewed","controller":"index","action":"setting"}', 'whoviewedme_admin_main', '', 1, 0, 1);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('whoviewedme_admin_main_settings', 'viewed', 'Plans', '', '{"route":"admin_default","module":"viewed","controller":"package"}', 'whoviewedme_admin_main', '', 1, 0, 1);

ALTER TABLE `engine4_viewed_membercounts`  ADD `test_mode` TINYINT NULL;

ALTER TABLE  `engine4_viewed_membercounts` ADD  `exclude` VARCHAR( 50 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `engine4_viewed_viewmes` ADD  `flag` INT( 3 ) NULL DEFAULT NULL ;

INSERT INTO `engine4_core_tasks` (`task_id`, `title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES(null, 'sendemail', 'Viewed', 'Viewed_Plugin_Task_Sendemail',43200, 1,0,0,0,0,0,0,0,0,0);
