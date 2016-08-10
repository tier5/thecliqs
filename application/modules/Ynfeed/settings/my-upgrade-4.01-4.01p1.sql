UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `name` = 'ynfeed';
ALTER TABLE  `engine4_ynfeed_maps` ADD  `business_id` int(11) UNSIGNED NOT NULL AFTER  `user_id` ;
