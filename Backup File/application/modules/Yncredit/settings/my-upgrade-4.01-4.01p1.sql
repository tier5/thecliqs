UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `name` = 'yncredit';

CREATE TABLE IF NOT EXISTS `engine4_yncredit_modules` (
  `module_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `level_id` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

