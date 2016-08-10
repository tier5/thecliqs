
UPDATE `engine4_core_modules` SET `version` = '4.0.0p7' WHERE `name` = 'timeline';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('user_edit_timeline', 'timeline', 'Timeline', 'Timeline_Plugin_Menus', '{"route":"timeline_user_edit"}', 'user_edit', NULL, 1, 0, 3);