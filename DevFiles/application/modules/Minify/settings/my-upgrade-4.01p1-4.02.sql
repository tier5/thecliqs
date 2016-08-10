INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('minify', 'Minify', 'Minify', '4.02', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_minify', 'minify', 'Minify', '','{"route":"admin_default","module":"minify","controller":"settings","action":"index"}','core_admin_main_plugins', '', 999),
('minify_admin_main_settings', 'minify', 'Global Settings', '', '{"route":"admin_default","module":"minify","controller":"settings"}', 'minify_admin_main', '', 1);


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`, `flag_unique`) VALUES('minify_admin_main_groups', 'minify', 'Groups Setting', '', '{"route":"admin_default","module":"minify","controller":"settings","action":"groups"}', 'minify_admin_main', '', 1, 0, 2, 0);