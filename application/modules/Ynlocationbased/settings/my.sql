INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynlocationbased', 'YN - Location-based System', 'This is location-bases system.', '4.01', 1, 'extra') ;
CREATE TABLE IF NOT EXISTS `engine4_ynlocationbased_modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynlocationbased', 'ynlocationbased', 'YN - Location-based System', '', '{"route":"admin_default","module":"ynlocationbased","controller":"manage-module"}', 'core_admin_main_plugins', '', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynlocationbased_admin_main_manage_module', 'ynlocationbased', 'Manage Modules', '', '{"route":"admin_default","module":"ynlocationbased","controller":"manage-module"}', 'ynlocationbased_admin_main', '', 1, 0, 1);

--
-- Dumping data for table `engine4_ynlocationbased_modules`
--

INSERT IGNORE INTO `engine4_ynlocationbased_modules` (`module_id`, `module_name`, `enabled`) VALUES
(1, 'ynevent', 1),
(2, 'ynfundraising', 1),
(3, 'advgroup', 1),
(4, 'ynjobposting', 1),
(5, 'ynresume', 1),
(6, 'ynlistings', 1),
(7, 'ynmultilisting', 1),
(8, 'ynmember', 1),
(9, 'ynbusinesspages', 1),
(10, 'yncontest', 1),
(11, 'groupbuy', 1);