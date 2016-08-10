UPDATE `engine4_core_modules` SET `version` = '4.02' where 'name' = 'yncontest';

ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `start_date_submit_entries` datetime  NULL;
ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `end_date_submit_entries` datetime  NULL;

ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `start_date_vote_entries` datetime  NULL;
ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `end_date_vote_entries` datetime  NULL

/* add category contest start*/
INSERT INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('yncontest_admin_main_categories', 'yncontest', 'Categories', 'Yncontest_Plugin_Menus::canAdminCategory', '{"route":"admin_default","module":"yncontest","controller":"categories"}', 'yncontest_admin_main', '', 1, 0, 5);


ALTER TABLE `engine4_yncontest_contests`
ADD COLUMN `category_id` int(11) unsigned NOT NULL;

CREATE TABLE IF NOT EXISTS `engine4_yncontest_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `parent_category_id` int(11) unsigned NOT NULL default '0', 
  `level` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;
/* add category contest end*/
