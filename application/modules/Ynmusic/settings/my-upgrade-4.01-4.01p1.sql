UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `engine4_core_modules`.`name` = 'ynmusic' LIMIT 1 ;

-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynmusic_main', 'standard', 'YN - Social Music - Main Navigation Menu', 999);

UPDATE `engine4_core_menuitems` SET  `label` =  'Migrate From Mp3 Music' WHERE `engine4_core_menuitems`.`name` = 'ynmusic_admin_main_migration';