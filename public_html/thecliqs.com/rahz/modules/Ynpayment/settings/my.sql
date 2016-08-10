INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynpayment', 'Advanced Payment Gateway', 'Advanced Payment Gateway', '4.04', 1, 'extra') ;
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(100, 'Authorize.Net', NULL, '0', 'Ynpayment_Plugin_Gateway_Authorizenet',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(101, 'iTransact', NULL, '0', 'Ynpayment_Plugin_Gateway_ITransact', NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(102, 'CCBill', NULL, '0', 'Ynpayment_Plugin_Gateway_CCBill',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(103, 'Skrill', NULL, '0', 'Ynpayment_Plugin_Gateway_Skrill',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(104, 'WebMoney', NULL, '0', 'Ynpayment_Plugin_Gateway_WebMoney',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(105, 'BitPay', NULL, '0', 'Ynpayment_Plugin_Gateway_BitPay',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(106, 'Stripe', NULL, '0', 'Ynpayment_Plugin_Gateway_Stripe',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(107, 'HeidelPay', NULL, '0', 'Ynpayment_Plugin_Gateway_HeidelPay',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(108, 'Braintree', NULL, '0', 'Ynpayment_Plugin_Gateway_Braintree',NULL, '0');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynpayment', 'ynpayment', 'Advanced Payment Gateway', '', '{"route":"ynpayment_admin_transaction"}', 'core_admin_main_plugins', '', 999),
('ynpayment_admin_main_transactions', 'ynpayment', 'Transactions', '', '{"route":"ynpayment_admin_transaction"}', 'ynpayment_admin_main', '', 1),
('ynpayment_admin_main_settings', 'ynpayment', 'Settings', '', '{"route":"ynpayment_admin_setting"}', 'ynpayment_admin_main', '', 2),
('ynpayment_admin_main_getways', 'ynpayment', 'Gateways', '', '{"route":"ynpayment_admin_gateway"}', 'ynpayment_admin_main', '', 3),
('ynpayment_admin_main_packages', 'ynpayment', 'Plans', '', '{"route":"ynpayment_admin_plan"}', 'ynpayment_admin_main', '', 4),
('ynpayment_admin_main_subscription', 'ynpayment', 'Subscriptions', '', '{"route":"ynpayment_admin_subscription"}', 'ynpayment_admin_main', '', 5);

DROP TABLE IF EXISTS `engine4_ynpayment_subscriptions`;
CREATE TABLE IF NOT EXISTS `engine4_ynpayment_subscriptions` (
  `subscription_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `getaway_subscription_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime default NULL,
  `gateway_id` int(10) unsigned default NULL,
  `package_id` int(10) unsigned default NULL,
  `order_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;