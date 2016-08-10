UPDATE `engine4_core_modules` SET `version` = '4.06' WHERE `name` = 'ynmobile';

INSERT IGNORE INTO `engine4_ynmobile_menuitems` (`name`, `module`, `enabled`, `order`, `label`, `layout`, `icon`, `uri`, `menu`) VALUES
('poll', 'poll', 1, 999, 'Polls', 'poll', 'icon-sidebar-poll', 'polls', 1),
('group', 'core', 1, 999, 'Groups', 'group', 'icon-sidebar-group', 'groups', 1);