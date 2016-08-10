INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) 
VALUES  ('ynprofilestyler', 'Profile Styler', 'Profile Styler', '4.01', 1, 'extra') ;

-- Dumping structure for table se4.2.1.engine4_ynprofilestyler_images
CREATE TABLE IF NOT EXISTS `engine4_ynprofilestyler_images` (
  `image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creation_date` datetime NOT NULL,
  `url` tinytext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `creation_date` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table se4.2.1.engine4_ynprofilestyler_layoutrules
CREATE TABLE IF NOT EXISTS `engine4_ynprofilestyler_layoutrules` (
  `layoutrule_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `layout_id` int(11) unsigned NOT NULL,
  `rule_id` int(11) unsigned NOT NULL,
  `value` tinytext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`layoutrule_id`),
  KEY `layout_id` (`layout_id`),
  KEY `rule_id` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table se4.2.1.engine4_ynprofilestyler_layouts
CREATE TABLE IF NOT EXISTS `engine4_ynprofilestyler_layouts` (
  `layout_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` smallint(5) unsigned NOT NULL DEFAULT '999',
  `is_active` tinyint(1) unsigned DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cache_style` text COLLATE utf8_unicode_ci,
  `thumbnail` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`layout_id`),
  KEY `user_id` (`user_id`),
  KEY `publish_ordering` (`publish`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.


-- Dumping structure for table se4.2.1.engine4_ynprofilestyler_rulegroups
CREATE TABLE IF NOT EXISTS `engine4_ynprofilestyler_rulegroups` (
  `rulegroup_id` int(11) unsigned NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` smallint(6) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rulegroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table se4.2.1.engine4_ynprofilestyler_ruleoptions
CREATE TABLE IF NOT EXISTS `engine4_ynprofilestyler_ruleoptions` (
  `ruleoption_id` int(11) unsigned NOT NULL,
  `rule_id` int(11) unsigned NOT NULL DEFAULT '0',
  `option_label` varchar(50) NOT NULL,
  `option_value` tinytext NOT NULL,
  `ordering` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`ruleoption_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Dumping structure for table se4.2.1.engine4_ynprofilestyler_rules
CREATE TABLE IF NOT EXISTS `engine4_ynprofilestyler_rules` (
  `rule_id` int(11) unsigned NOT NULL,
  `rulegroup_id` int(11) unsigned NOT NULL DEFAULT '0',
  `label` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `dompath` text COLLATE utf8_unicode_ci NOT NULL,
  `rule_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `control_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `default` tinytext COLLATE utf8_unicode_ci,
  `preview` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rule_id`),
  KEY `rulegroup_id` (`rulegroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table se4.2.1.engine4_ynprofilestyler_users
CREATE TABLE IF NOT EXISTS `engine4_ynprofilestyler_users` (
  `user_id` int(11) unsigned NOT NULL,
  `is_allowed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `layout_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Insert a temporary layout
INSERT INTO `engine4_ynprofilestyler_layouts` (`layout_id`, `user_id`, `publish`, `ordering`, `is_active`, `description`, `title`, `cache_style`, `thumbnail`, `creation_date`) VALUES (0, 1, 0, 999, 1, '', '', NULL, NULL, '2012-04-13 07:09:10');
UPDATE `engine4_ynprofilestyler_layouts` 
SET `layout_id` = '0' 
WHERE `engine4_ynprofilestyler_layouts`.`layout_id` = 1;

-- Dumping data for table se4.2.1.engine4_ynprofilestyler_rulegroups
INSERT IGNORE INTO `engine4_ynprofilestyler_rulegroups` (`rulegroup_id`, `group_name`, `enabled`, `ordering`, `title`, `published`) VALUES
	(1, 'background', 1, 1, 'Background', 1),
	(2, 'widget', 1, 2, 'Widget', 1),
	(3, 'menu-bar', 1, 3, 'Menu Bar', 1),
	(4, 'tab-bar', 1, 4, 'Tab Bar', 1),
	(6, 'text', 1, 6, 'Text', 1),
	(7, 'link', 1, 7, 'Link', 0),
	(8, 'username', 1, 8, 'Username', 0),
	(9, 'widget-header', 1, 3, 'Widget', 0),
	(10, 'widget-text', 1, 4, 'Widget', 0),
	(11, 'widget-link', 1, 5, 'Widget', 0);

INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (1, 1, 'Background color', 'background-color', 'body', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (2, 1, 'Background image', 'background-image', 'body', 'image', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (3, 1, 'Background position', 'background-position', 'body', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (4, 1, 'Background repeat', 'background-repeat', 'body', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (5, 2, 'Background color', 'background-color', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (6, 2, 'Background image', 'background-image', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'image', 'image', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (7, 2, 'Border style', 'border-style', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (8, 2, 'Border width', 'border-width', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (9, 2, 'Border color', 'border-color', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (10, 2, 'Opacity', 'opacity', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'text', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (12, 3, 'Background color', 'background-color', '#global_header div.layout_core_menu_main, #global_header div.layout_advmenusystem_advmenu_main', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (13, 3, 'Background image', 'background-image', '#global_header div.layout_core_menu_main, #global_header div.layout_advmenusystem_advmenu_main', 'image', 'image', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (14, 3, 'Font size', 'font-size', '#global_header div.layout_core_menu_main ul > li > a, #global_header div.layout_advmenusystem_advmenu_main ul > li a', 'size', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (15, 3, 'Font family', 'font-family', '#global_header div.layout_core_menu_main > ul.navigation > li > a, \r\n#global_header div.layout_advmenusystem_advmenu_main > ul.navigation > li a', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (16, 3, 'Font weight', 'font-weight', '#global_header div.layout_core_menu_main > ul.navigation > li > a, #global_header div.layout_advmenusystem_advmenu_main > ul.navigation > li > a', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (17, 3, 'Text decoration', 'text-decoration', '#global_header div.layout_core_menu_main > ul.navigation > li > a, #global_header div.layout_advmenusystem_advmenu_main > ul.navigation > li > a', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (18, 3, 'Font style', 'font-style', '#global_header div.layout_core_menu_main > ul.navigation > li > a, #global_header div.layout_advmenusystem_advmenu_main > ul.navigation > li > a', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (19, 3, 'Text color', 'color', '#global_header div.layout_core_menu_main > ul.navigation > li > a, \r\n#global_header div.layout_core_menu_main > ul.navigation > li > a:link, \r\n#global_header div.layout_advmenusystem_advmenu_main > ul.navigation > li a, \r\n#global_header div.layout_advmenusystem_advmenu_main > ul.navigation > li a:link', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (21, 4, 'Background color', 'background-color', '#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents ul > li', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (22, 4, 'Background image', 'background-image', '#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents ul > li', 'image', 'image', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (23, 4, 'Font size', 'font-size', '#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents > ul > li', 'size', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (24, 4, 'Font family', 'font-family', '#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents > ul > li', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (25, 4, 'Font weight', 'font-weight', '#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents > ul > li', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (26, 4, 'Font style', 'font-style', '#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents > ul > li', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (27, 4, 'Text decoration', 'text-decoration', '#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents > ul > li', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (28, 4, 'Text color', 'color', '#global_wrapper .layout_main div.tabs_alt > ul > li > a, \r\n#global_wrapper .layout_main div.tabs_alt > ul > li, \r\n#global_wrapper .layout_main div.tabs_alt > ul > li div.tab_pulldown_contents > ul > li', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (29, 9, 'Font size', 'font-size', '.layout_right > div > h3, .layout_left > div > h3', 'size', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (30, 9, 'Font family', 'font-family', '.layout_right > div > h3, .layout_left > div > h3', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (31, 9, 'Font weight', 'font-weight', '.layout_right > div > h3, .layout_left > div > h3', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (32, 9, 'Font style', 'font-style', '.layout_right > div > h3, .layout_left > div > h3', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (33, 9, 'Text decoration', 'text-decoration', '.layout_right > div > h3, .layout_left > div > h3', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (34, 9, 'Text color', 'color', '.layout_right > div.generic_layout_container > h3, .layout_left > div.generic_layout_container > h3', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (35, 6, 'Font size', 'font-size', '#global_wrapper div.layout_main > div.layout_middle p, \r\n#global_wrapper div.layout_main > div.layout_middle div, \r\n#global_wrapper div.layout_main > div.layout_middle span', 'size', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (36, 6, 'Font family', 'font-family', '#global_wrapper div.layout_main > div.layout_middle p, \r\n#global_wrapper div.layout_main > div.layout_middle div, \r\n#global_wrapper div.layout_main > div.layout_middle span', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (37, 6, 'Font weight', 'font-weight', '#global_wrapper div.layout_main > div.layout_middle p, \r\n#global_wrapper div.layout_main > div.layout_middle div, \r\n#global_wrapper div.layout_main > div.layout_middle span', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (38, 6, 'Font style', 'font-style', '#global_wrapper div.layout_main > div.layout_middle p, \r\n#global_wrapper div.layout_main > div.layout_middle div, \r\n#global_wrapper div.layout_main > div.layout_middle span', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (39, 6, 'Text decoration', 'text-decoration', '#global_wrapper div.layout_main > div.layout_middle p, \r\n#global_wrapper div.layout_main > div.layout_middle div, \r\n#global_wrapper div.layout_main > div.layout_middle span', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (40, 6, 'Text color', 'color', '#global_wrapper div.layout_main > div.layout_middle p, \r\n#global_wrapper div.layout_main > div.layout_middle div, \r\n#global_wrapper div.layout_main > div.layout_middle span', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (41, 7, 'Link color', 'color', '#global_wrapper div.layout_main > div.layout_middle a', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (42, 8, 'Font size', 'font-size', '#profile_status > h2', 'size', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (43, 8, 'Font family', 'font-family', '#profile_status > h2', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (44, 8, 'Font weight', 'font-weight', '#profile_status > h2', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (45, 8, 'Font style', 'font-style', '#profile_status > h2', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (46, 8, 'Text decoration', 'text-decoration', '#profile_status > h2', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (47, 8, 'Text color', 'color', '#profile_status > h2', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (48, 10, 'Font size', 'font-size', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'size', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (49, 10, 'Font family', 'font-family', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, \r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container ul,\r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container li,\r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container p,\r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container span,\r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container form,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container ul,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container li,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container p,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container span,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container form', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (50, 10, 'Font weight', 'font-weight', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (51, 10, 'Font style', 'font-style', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (52, 10, 'Text decoration', 'text-decoration', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (53, 11, 'Link color', 'color', '#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container a, #global_wrapper div.layout_main > div.layout_left > div.generic_layout_container a', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (54, 10, 'Text color', 'color', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, \r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container span,\r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container div,\r\n#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container p,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container span,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container div,\r\n#global_wrapper div.layout_main > div.layout_right > div.generic_layout_container p', 'color', 'text', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (55, 1, 'Background attachment', 'background-attachment', 'body', 'text', 'select', NULL, 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (56, 3, 'Background repeat', 'background-repeat', '#global_header div.layout_core_menu_main, \r\n#global_header div.layout_advmenusystem_advmenu_main', 'text', 'hidden', 'repeat', 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (57, 1, 'Background header color', 'background', '#global_header > .layout_page_header, #global_wrapper, #global_footer', 'text', 'hidden', 'none', 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (58, 3, 'No background', 'background', '#global_header div.layout_core_menu_main, #global_header div.layout_advmenusystem_advmenu_main, #global_header div.layout_advmenusystem_advmenu_main > ul > li *', 'text', 'hidden', 'none', 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (59, 4, 'Background image', 'background', '#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents ul > li', 'text', 'hidden', 'none', 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (60, 2, 'No background', 'background', '#global_wrapper div.layout_main > div.layout_left > div.generic_layout_container, #global_wrapper div.layout_main > div.layout_right > div.generic_layout_container', 'text', 'hidden', 'none', 0);
INSERT INTO `engine4_ynprofilestyler_rules` (`rule_id`, `rulegroup_id`, `label`, `name`, `dompath`, `rule_type`, `control_type`, `default`, `preview`) VALUES (61, 4, 'No background', 'background', '#global_wrapper div.tabs_alt > ul > li, \r\n#global_wrapper div.tabs_alt > ul > li > a, \r\n#global_wrapper div.tabs_alt > ul > li div.tab_pulldown_contents ul > li', 'image', 'image', NULL, 0);
	
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (41, 3, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (42, 3, 'Left Top', 'left top', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (43, 3, 'Left Middle', 'left center', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (44, 3, 'Left Bottom', 'left bottom', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (45, 3, 'Center Top', 'center top', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (46, 3, 'Center Middle', 'center center', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (47, 3, 'Center Bottom', 'center bottom', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (48, 3, 'Right Top', 'right top', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (49, 3, 'Right Middle', 'right center', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (50, 3, 'Right Bottom', 'right bottom', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (51, 4, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (52, 4, 'No-repeat', 'no-repeat', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (53, 4, 'Repeate X', 'repeat-x', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (54, 4, 'Repeate Y', 'repeat-y', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (55, 4, 'Repeate X and Y', 'repeat', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (56, 7, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (57, 7, 'None', 'none', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (58, 7, 'Hidden', 'hidden', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (59, 7, 'Dotted', 'dotted', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (60, 7, 'Dashed', 'dashed', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (61, 7, 'Solid', 'solid', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (62, 7, 'Double', 'double', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (63, 7, 'Groove', 'groove', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (64, 7, 'Ridge', 'ridge', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (65, 7, 'Inset', 'inset', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (66, 7, 'Outset', 'outset', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (67, 8, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (68, 8, '1px', '1px', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (69, 8, '2px', '2px', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (70, 8, '3px', '3px', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (71, 8, '4px', '4px', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (72, 8, '5px', '5px', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (73, 14, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (74, 14, '8pt', '8', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (75, 14, '10pt', '10', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (76, 14, '12pt', '12', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (77, 14, '14pt', '14', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (78, 14, '18pt', '18', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (79, 14, '24pt', '24', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (80, 14, '36pt', '36', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (81, 15, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (82, 15, 'Andale Mono', 'andale mono,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (83, 15, 'Arial', 'arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (84, 15, 'Arial Black', 'arial black,avant garde', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (85, 15, 'Book Antiqua', 'book antiqua,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (86, 15, 'Comic Sans MS', 'comic sans ms,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (87, 15, 'Courier', 'courier', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (88, 15, 'Georgia', 'georgia,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (89, 15, 'Helvetica', 'helvetica', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (90, 15, 'Impact', 'impact,chicago', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (92, 15, 'Tahoma', 'tahoma,arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (93, 15, 'Terminal', 'terminal,monaco', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (94, 15, 'Times New Roman', 'times new roman,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (95, 15, 'Trebuchet MS', 'trebuchet ms,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (96, 15, 'Verdana', 'verdana,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (99, 16, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (100, 16, 'Bold', 'bold', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (101, 16, 'Bolder', 'bolder', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (102, 16, 'Lighter', 'lighter', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (103, 17, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (104, 17, 'None', 'none', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (105, 17, 'Underline', 'underline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (106, 17, 'Overline', 'overline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (107, 17, 'Line through', 'line-through', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (109, 18, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (110, 18, 'Normal', 'normal', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (111, 18, 'Italic', 'italic', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (112, 18, 'Oblique', 'oblique', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (113, 23, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (114, 23, '8pt', '8', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (115, 23, '10pt', '10', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (116, 23, '12pt', '12', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (117, 23, '14pt', '14', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (118, 23, '18pt', '18', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (119, 23, '24pt', '24', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (120, 23, '36pt', '36', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (121, 24, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (122, 24, 'Andale Mono', 'andale mono,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (123, 24, 'Arial', 'arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (124, 24, 'Arial Black', 'arial black,avant garde', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (125, 24, 'Book Antiqua', 'book antiqua,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (126, 24, 'Comic Sans MS', 'comic sans ms,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (127, 24, 'Courier', 'courier', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (128, 24, 'Georgia', 'georgia,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (129, 24, 'Helvetica', 'helvetica', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (130, 24, 'Impact', 'impact,chicago', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (132, 24, 'Tahoma', 'tahoma,arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (133, 24, 'Terminal', 'terminal,monaco', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (134, 24, 'Times New Roman', 'times new roman,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (135, 24, 'Trebuchet MS', 'trebuchet ms,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (136, 24, 'Verdana', 'verdana,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (139, 25, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (140, 25, 'Bold', 'bold', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (141, 25, 'Bolder', 'bolder', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (142, 25, 'Lighter', 'lighter', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (143, 26, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (144, 26, 'Normal', 'normal', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (145, 26, 'Italic', 'italic', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (146, 26, 'Oblique', 'oblique', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (147, 27, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (148, 27, 'None', 'none', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (149, 27, 'Underline', 'underline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (150, 27, 'Overline', 'overline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (151, 27, 'Line through', 'line-through', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (153, 29, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (154, 29, '8pt', '8', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (155, 29, '10pt', '10', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (156, 29, '12pt', '12', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (157, 29, '14pt', '14', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (158, 29, '18pt', '18', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (159, 29, '24pt', '24', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (160, 29, '36pt', '36', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (161, 30, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (162, 30, 'Andale Mono', 'andale mono,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (163, 30, 'Arial', 'arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (164, 30, 'Arial Black', 'arial black,avant garde', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (165, 30, 'Book Antiqua', 'book antiqua,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (166, 30, 'Comic Sans MS', 'comic sans ms,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (167, 30, 'Courier', 'courier', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (168, 30, 'Georgia', 'georgia,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (169, 30, 'Helvetica', 'helvetica', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (170, 30, 'Impact', 'impact,chicago', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (172, 30, 'Tahoma', 'tahoma,arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (173, 30, 'Terminal', 'terminal,monaco', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (174, 30, 'Times New Roman', 'times new roman,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (175, 30, 'Trebuchet MS', 'trebuchet ms,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (176, 30, 'Verdana', 'verdana,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (179, 31, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (180, 31, 'Bold', 'bold', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (181, 31, 'Bolder', 'bolder', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (182, 31, 'Lighter', 'lighter', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (183, 32, 'Default', '', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (184, 32, 'Normal', 'normal', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (185, 32, 'Italic', 'italic', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (186, 32, 'Oblique', 'oblique', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (187, 33, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (188, 33, 'None', 'none', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (189, 33, 'Underline', 'underline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (190, 33, 'Overline', 'overline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (191, 33, 'Line through', 'line-through', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (193, 35, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (194, 35, '8pt', '8', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (195, 35, '10pt', '10', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (196, 35, '12pt', '12', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (197, 35, '14pt', '14', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (198, 35, '18pt', '18', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (199, 35, '24pt', '24', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (200, 35, '36pt', '36', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (201, 36, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (202, 36, 'Andale Mono', 'andale mono,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (203, 36, 'Arial', 'arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (204, 36, 'Arial Black', 'arial black,avant garde', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (205, 36, 'Book Antiqua', 'book antiqua,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (206, 36, 'Comic Sans MS', 'comic sans ms,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (207, 36, 'Courier', 'courier', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (208, 36, 'Georgia', 'georgia,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (209, 36, 'Helvetica', 'helvetica', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (210, 36, 'Impact', 'impact,chicago', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (212, 36, 'Tahoma', 'tahoma,arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (213, 36, 'Terminal', 'terminal,monaco', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (214, 36, 'Times New Roman', 'times new roman,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (215, 36, 'Trebuchet MS', 'trebuchet ms,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (216, 36, 'Verdana', 'verdana,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (219, 37, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (220, 37, 'Bold', 'bold', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (221, 37, 'Bolder', 'bolder', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (222, 37, 'Lighter', 'lighter', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (223, 38, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (224, 38, 'Normal', 'normal', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (225, 38, 'Italic', 'italic', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (226, 38, 'Oblique', 'oblique', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (227, 39, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (228, 39, 'None', 'none', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (229, 39, 'Underline', 'underline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (230, 39, 'Overline', 'overline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (231, 39, 'Line through', 'line-through', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (233, 42, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (234, 42, '8pt', '8', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (235, 42, '10pt', '10', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (236, 42, '12pt', '12', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (237, 42, '14pt', '14', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (238, 42, '18pt', '18', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (239, 42, '24pt', '24', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (240, 42, '36pt', '36', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (241, 43, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (242, 43, 'Andale Mono', 'andale mono,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (243, 43, 'Arial', 'arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (244, 43, 'Arial Black', 'arial black,avant garde', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (245, 43, 'Book Antiqua', 'book antiqua,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (246, 43, 'Comic Sans MS', 'comic sans ms,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (247, 43, 'Courier', 'courier', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (248, 43, 'Georgia', 'georgia,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (249, 43, 'Helvetica', 'helvetica', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (250, 43, 'Impact', 'impact,chicago', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (252, 43, 'Tahoma', 'tahoma,arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (253, 43, 'Terminal', 'terminal,monaco', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (254, 43, 'Times New Roman', 'times new roman,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (255, 43, 'Trebuchet MS', 'trebuchet ms,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (256, 43, 'Verdana', 'verdana,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (259, 44, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (260, 44, 'Bold', 'bold', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (261, 44, 'Bolder', 'bolder', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (262, 44, 'Lighter', 'lighter', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (263, 45, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (264, 45, 'Normal', 'normal', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (265, 45, 'Italic', 'italic', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (266, 45, 'Oblique', 'oblique', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (267, 46, 'Default', '', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (268, 46, 'None', 'none', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (269, 46, 'Underline', 'underline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (270, 46, 'Overline', 'overline', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (271, 46, 'Line through', 'line-through', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (273, 49, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (274, 49, 'Andale Mono', 'andale mono,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (275, 49, 'Arial', 'arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (276, 49, 'Arial Black', 'arial black,avant garde', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (277, 49, 'Book Antiqua', 'book antiqua,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (278, 49, 'Comic Sans MS', 'comic sans ms,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (279, 49, 'Courier', 'courier', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (280, 49, 'Georgia', 'georgia,palatino', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (281, 49, 'Helvetica', 'helvetica', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (282, 49, 'Impact', 'impact,chicago', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (284, 49, 'Tahoma', 'tahoma,arial,helvetica,sans-serif', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (285, 49, 'Terminal', 'terminal,monaco', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (286, 49, 'Times New Roman', 'times new roman,times', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (287, 49, 'Trebuchet MS', 'trebuchet ms,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (288, 49, 'Verdana', 'verdana,geneva', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (291, 50, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (292, 50, 'Bold', 'bold', 2);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (293, 50, 'Bolder', 'bolder', 3);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (294, 50, 'Lighter', 'lighter', 4);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (295, 51, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (296, 51, 'Normal', 'normal', 2);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (297, 51, 'Italic', 'italic', 3);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (298, 51, 'Oblique', 'oblique', 4);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (299, 52, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (300, 52, 'None', 'none', 2);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (301, 52, 'Underline', 'underline', 3);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (302, 52, 'Overline', 'overline', 4);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (303, 52, 'Line through', 'line-through', 5);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (305, 48, 'Default', '', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (306, 48, '10pt', '10', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (307, 48, '12pt', '12', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (308, 48, '14pt', '14', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (309, 48, '18pt', '18', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (310, 48, '24pt', '24', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (311, 48, '36pt', '36', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (312, 55, 'Default', '\'\'', 1);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (315, 48, '8pt', '8', 999);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (317, 55, 'Scroll', 'scroll', 3);
INSERT INTO `engine4_ynprofilestyler_ruleoptions` (`ruleoption_id`, `rule_id`, `option_label`, `option_value`, `ordering`) VALUES (318, 55, 'Fixed', 'fixed', 2);


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynprofilestyler_profile_edit', 'ynprofilestyler', 'Edit Profile Style', 'Ynprofilestyler_Plugin_Menus', '', 'user_profile', '', 1, 0, 4);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynprofilestyler_admin_main_managelayout', 'ynprofilestyler', 'Manage Themes', '', '{"route":"admin_default","module":"ynprofilestyler","controller":"manage","action":"layout"}', 'ynprofilestyler_admin_main', '', 1, 0, 997);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynprofilestyler_admin_main_manageimage', 'ynprofilestyler', 'Manage Images', '', '{"route":"admin_default","module":"ynprofilestyler","controller":"manage","action":"image"}', 'ynprofilestyler_admin_main', '', 1, 0, 998);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynprofilestyler_admin_main_managelevel', 'ynprofilestyler', 'Member Level Settings', '', '{"route":"admin_default","module":"ynprofilestyler","controller":"manage","action":"level"}', 'ynprofilestyler_admin_main', '', 1, 0, 999);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_admin_plugins_ynprofilestyler', 'ynprofilestyler', 'Profile Styler', '', '{"route":"admin_default","module":"ynprofilestyler","controller":"manage","action":"layout"}', 'core_admin_main_plugins', '', 1, 0, 1);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynprofilestyler_apply_layout', 'ynprofilestyler', 'Apply Theme', 'Ynprofilestyler_Plugin_Menus', '', 'user_profile', '', 1, 0, 4);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'theme' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
	