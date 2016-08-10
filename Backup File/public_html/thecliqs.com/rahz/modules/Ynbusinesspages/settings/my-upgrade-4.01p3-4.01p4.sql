UPDATE `engine4_core_modules` SET `version` = '4.01p4' WHERE `name` = 'ynbusinesspages';
-- auth view for business
INSERT IGNORE INTO `engine4_authorization_permissions`
	SELECT
	level_id as `level_id`,
	'ynbusinesspages_business' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');
