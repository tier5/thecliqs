UPDATE `engine4_core_modules` SET `version` = '4.01p3' WHERE `engine4_core_modules`.`name` = 'ynmusic' LIMIT 1 ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmusic_admin_main_migrationsemusic', 'ynmusic', 'Migrate From SE Music', 'Ynmusic_Plugin_Menus::canMigrateSEMusic', '{"route":"admin_default","module":"ynmusic","controller":"migration-semusic"}', 'ynmusic_admin_main', '', 9);

ALTER TABLE  `engine4_ynmusic_songs` ADD  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `import_id` ;
ALTER TABLE  `engine4_ynmusic_playlists` ADD  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `import_id` ;
ALTER TABLE  `engine4_ynmusic_albums` ADD  `import_type` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `import_id` ;