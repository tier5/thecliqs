INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynresponsiveclean', 'YN - Responsive Clean Template', 'Responsive Clean Template', '4.01p6', 1, 'extra') ;

UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.mini-menu' where `name` = 'ynresponsive1.mini-menu';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.menu-main' where `name` = 'ynresponsive1.menu-main';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.sliderfull' where `name` = 'ynresponsive1.sliderfull';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.gridslide' where `name` = 'ynresponsive1.gridslide';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.slider' where `name` = 'ynresponsive1.slider';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.grid' where `name` = 'ynresponsive1.grid';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.join-now' where `name` = 'ynresponsive1.join-now';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveclean.latest-shots' where `name` = 'ynresponsive1.latest-shots';
