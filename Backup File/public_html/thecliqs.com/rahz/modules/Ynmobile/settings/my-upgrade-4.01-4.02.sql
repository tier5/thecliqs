INSERT IGNORE INTO `engine4_ynmobile_menuitems` (`name`, `module`, `enabled`, `order`, `label`, `layout`, `icon`, `uri`, `menu`) VALUES
('ynevent', 'ynevent', 1, 3, 'Events', 'event', 'icon-calendar', 'upcommingevent/0', 1),
('advalbum', 'advalbum', 1, 5, 'Albums', 'photo', 'icon-picture', 'photoList', 1),
('ynvideo', 'ynvideo', 1, 7, 'Videos', 'video', 'icon-facetime-video', 'video', 1);

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('SE Mobile Video Encode', 'ynmobile_encode', 'ynmobile', 'Ynmobile_Plugin_Job_Encode', NULL, 1, 1, 1);
