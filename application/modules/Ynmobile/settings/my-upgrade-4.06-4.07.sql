UPDATE `engine4_core_modules` SET `version` = '4.07' WHERE `name` = 'ynmobile';

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_storekitpurchases` (
    `storekitpurchase_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`storekitpurchase_key` VARCHAR(75) COMMENT 'which has been created manually on Apple Dev' ,
	`storekitpurchase_module_id` VARCHAR(75),
	`storekitpurchase_type` VARCHAR(255) DEFAULT 'purchase_product' COMMENT 'purchase product/sponsor/feature/...',
	`storekitpurchase_item_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'some modules need item id' ,
	PRIMARY KEY (`storekitpurchase_id`) 
);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynmobile_admin_main_subscription', 'ynmobile', 'Subscription Products','', '{"route":"admin_default","module":"ynmobile","controller":"subscription"}', 'ynmobile_admin_main', '', 4);

INSERT IGNORE INTO `engine4_ynmobile_menuitems` (`name`, `module`, `enabled`, `order`, `label`, `layout`, `icon`, `uri`, `menu`) VALUES
('blog', 'Core', 1, 999, 'Blogs', 'blog', 'icon-blog', 'blogs', 1),
('classified', 'Core', 1, 999, 'Classifieds', 'classified', 'icon-sidebar-classified', 'classifieds', 1),
('subscribe', 'Core', 1, 999, 'Memberships', 'subscribe', '', 'subscribe', 1);