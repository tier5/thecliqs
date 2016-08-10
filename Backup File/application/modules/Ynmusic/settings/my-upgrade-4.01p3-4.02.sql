UPDATE `engine4_core_modules` SET `version` = '4.02' WHERE `engine4_core_modules`.`name` = 'ynmusic' LIMIT 1 ;

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_album' as `type`,
    'auth_download' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'download' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_album' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynmusic_song' as `type`,
    'auth_download' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'download' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynmusic_song' as `type`,
	'download' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

