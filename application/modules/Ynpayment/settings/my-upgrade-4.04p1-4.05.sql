UPDATE `engine4_core_modules` SET `version` = '4.05' WHERE `name` = 'ynpayment';

INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(107, 'HeidelPay', NULL, '0', 'Ynpayment_Plugin_Gateway_HeidelPay',NULL, '0');

INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
(108, 'Braintree', NULL, '0', 'Ynpayment_Plugin_Gateway_Braintree',NULL, '0');