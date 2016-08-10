UPDATE `engine4_core_modules` SET `version` = '4.04' WHERE `name` = 'ynpayment';

INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(103, 'Skrill', NULL, '0', 'Ynpayment_Plugin_Gateway_Skrill',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(104, 'WebMoney', NULL, '0', 'Ynpayment_Plugin_Gateway_WebMoney',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(105, 'BitPay', NULL, '0', 'Ynpayment_Plugin_Gateway_BitPay',NULL, '0');
INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(106, 'Stripe', NULL, '0', 'Ynpayment_Plugin_Gateway_Stripe',NULL, '0');