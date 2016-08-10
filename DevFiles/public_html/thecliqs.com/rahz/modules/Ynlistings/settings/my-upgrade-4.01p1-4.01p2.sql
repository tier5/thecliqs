ALTER TABLE `engine4_ynlistings_categories` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE `engine4_ynlistings_orders` ADD `feature_day_number` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `engine4_ynlistings_listings` ADD `feature_day_number` INT(11) NOT NULL DEFAULT '0' ;
ALTER TABLE `engine4_ynlistings_listings` ADD `feature_expiration_date` datetime DEFAULT NULL ;