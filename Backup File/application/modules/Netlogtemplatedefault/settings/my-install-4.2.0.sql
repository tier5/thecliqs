CREATE TABLE IF NOT EXISTS `engine4_netlogtemplatedefault_userstatus` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) NOT NULL DEFAULT '0',
	`status` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id`)
);