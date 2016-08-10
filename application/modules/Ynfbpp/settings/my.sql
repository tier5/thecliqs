--
-- INSERT MODULE
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynfbpp', 'Facebook Profile Popup', 'Profile Popup', '4.01p2', 1, 'extra') ;

-- 
-- INSERT MENU - MENU ITEMS
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('core_admin_main_plugins_ynfbpp', 'ynfbpp', 'Profile Popup', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"index"}', 'core_admin_main_plugins', '', 1, 0, 999),
('ynfbpp_admin_main_user', 'ynfbpp', 'User Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"user"}', 'ynfbpp_admin_main', '', 1, 0, 2),
('ynfbpp_admin_main_fields', 'ynfbpp', 'User Fields Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"fields"}', 'ynfbpp_admin_main', '', 1, 0, 3),
('ynfbpp_admin_main_group', 'ynfbpp', 'Group Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"group"}', 'ynfbpp_admin_main', '', 1, 0, 4),
('ynfbpp_admin_main_event', 'ynfbpp', 'Event Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"event"}', 'ynfbpp_admin_main', '', 1, 0, 5),
('ynfbpp_admin_main_settings', 'ynfbpp', 'Global Settings', '', '{"route":"admin_default","module":"ynfbpp","controller":"settings","action":"index"}', 'ynfbpp_admin_main', '', 1, 0, 1)
;

--
-- CREATE TABLE
CREATE TABLE `engine4_ynfbpp_popup` (
  `field_id` int(11) unsigned NOT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'user',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

