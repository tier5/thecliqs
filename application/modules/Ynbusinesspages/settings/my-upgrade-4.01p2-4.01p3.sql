UPDATE `engine4_activity_notificationtypes` SET `body` = 'The claim request for business {item:$object} has just been claimed successfully. Awaiting approval.' WHERE `type` = 'ynbusinesspages_claim_success';
UPDATE `engine4_core_menuitems` SET  `label` =  'YN - Business ' WHERE `engine4_core_menuitems`.`name` = 'core_admin_main_plugins_ynbusinesspages';
UPDATE  `engine4_core_menuitems` SET  `label` =  'Business' WHERE `engine4_core_menuitems`.`name` = 'core_main_ynbusinesspages';
DELETE FROM  `engine4_core_content` WHERE  `type` =  'widget' AND  `name` LIKE 'ynbusinesspages.business-profile-options';

INSERT IGNORE INTO `engine4_ynbusinesspages_modules` (`title`, `item_type`) VALUES
('Social Music', 'ynmusic_song');

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynbusinesspages_ynmusic_album_create', 'ynbusinesspages', '{item:$subject} add a new social music album.', 1, 3, 1, 1, 1, 1),
('ynbusinesspages_ynmusic_songs_create', 'ynbusinesspages', '{item:$subject} add some social music songs.', 1, 3, 1, 1, 1, 1);

UPDATE `engine4_core_modules` SET `version` = '4.01p3' WHERE `name` = 'ynbusinesspages';

-- auth view for business
INSERT IGNORE INTO `engine4_authorization_permissions`
	SELECT
	level_id as `level_id`,
	'ynbusinesspages_business' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');
