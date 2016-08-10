UPDATE `engine4_core_modules` SET `version` = '4.02p6' where 'name' = 'yncontest';
ALTER TABLE  `engine4_yncontest_contests` ADD  `location` varchar(256) collate utf8_unicode_ci default NULL AFTER `share_count`;
ALTER TABLE  `engine4_yncontest_contests` ADD  `longitude` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `share_count`;
ALTER TABLE  `engine4_yncontest_contests` ADD  `latitude` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `share_count`;