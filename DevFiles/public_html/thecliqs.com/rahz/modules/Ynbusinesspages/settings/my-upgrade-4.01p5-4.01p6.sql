UPDATE `engine4_core_modules` SET `version` = '4.01p6' WHERE `name` = 'ynbusinesspages';

ALTER TABLE `engine4_authorization_permissions` MODIFY `name` VARCHAR(64);

INSERT IGNORE INTO `engine4_ynbusinesspages_modules` (`title`, `item_type`) VALUES
('Ultimate Video', 'ynultimatevideo_video');

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynbusinesspages_ynultimatevideo_video_create', 'ynbusinesspages', '{item:$subject} add a video.', 1, 3, 1, 1, 1, 1);

-- auth video for business
INSERT IGNORE INTO `engine4_authorization_permissions`
	SELECT
	level_id as `level_id`,
	'ynbusinesspages_business' as `type`,
	'ynultimatevideo_video' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin', 'user');