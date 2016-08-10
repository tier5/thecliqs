UPDATE `engine4_core_modules` SET `version` = '4.0.2'  WHERE `name` = 'page';

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'page' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'page' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'page' as `type`,
    'posting' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

CREATE TABLE IF NOT EXISTS `engine4_page_search` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`page_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`title` VARCHAR(255) NULL DEFAULT NULL,
	`body` TEXT NULL,
	`object` VARCHAR(255) NULL DEFAULT NULL,
	`object_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`photo_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `page_id` (`page_id`),
	INDEX `title` (`title`),
	INDEX `object` (`object`),
	INDEX `object_id` (`object_id`)
)COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;

CREATE TABLE IF NOT EXISTS `engine4_page_tagmaps` (
	`tagmap_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`page_id` INT(11) UNSIGNED NOT NULL,
	`resource_type` VARCHAR(24) NOT NULL COLLATE 'utf8_unicode_ci',
	`resource_id` INT(11) UNSIGNED NOT NULL,
	`tagger_type` VARCHAR(24) NOT NULL COLLATE 'utf8_unicode_ci',
	`tagger_id` INT(11) UNSIGNED NOT NULL,
	`tag_id` INT(11) UNSIGNED NOT NULL,
	PRIMARY KEY (`tagmap_id`),
	INDEX `resource_type` (`resource_type`, `resource_id`),
	INDEX `tagger_type` (`tagger_type`, `tagger_id`),
	INDEX `tag_type` (`tag_id`)
)COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;

CREATE TABLE IF NOT EXISTS `engine4_page_tags` (
	`tag_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`text` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`tag_id`),
	UNIQUE INDEX `text` (`text`)
)COLLATE='utf8_unicode_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;