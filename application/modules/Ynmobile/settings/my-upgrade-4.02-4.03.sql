UPDATE `engine4_ynmobile_menuitems` SET `uri` = 'albumLanding' WHERE (`module` = 'album') OR (`module` = 'advalbum');
UPDATE `engine4_ynmobile_menuitems` SET `uri` = 'upcommingevent' WHERE (`module` = 'event') OR (`module` = 'ynevent');
DELETE FROM `engine4_ynmobile_menuitems` WHERE (`name` = 'profile') OR (`name` = 'Profile');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobile_admin_main_notifications', 'ynmobile', 'Manage Notifications','', '{"route":"admin_default","module":"ynmobile","controller":"notifications"}', 'ynmobile_admin_main', '', 3);

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_maps` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci,
  `latitude` varchar(64),
  `longitude` varchar(64),
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('ynmobile_checkin', 'ynmobile', '{item:$subject} - {var:$status} at {var:$location}', 1, 5, 1, 1, 1, 1);

-- ALL
-- auth_view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
 
-- USER
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');


-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynmobile_map' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');

