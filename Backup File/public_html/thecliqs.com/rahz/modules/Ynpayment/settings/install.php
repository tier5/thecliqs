<?php

/**
 * @category   Application_Extensions
 * @package    Ynpayment
 */
class Ynpayment_Installer extends Engine_Package_Installer_Module
{
	public function onInstall()
	{
		parent::onInstall();
	}
	public function onEnable()
	{
		parent::onEnable();
		$db = $this -> getDb();
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(100, 'Authorize.Net', NULL, '0', 'Ynpayment_Plugin_Gateway_Authorizenet',NULL, '0');");
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(101, 'iTransact', NULL, '0', 'Ynpayment_Plugin_Gateway_ITransact',NULL, '0');");
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(102, 'CCBill', NULL, '0', 'Ynpayment_Plugin_Gateway_CCBill',NULL, '0');");
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(103, 'Skrill', NULL, '0', 'Ynpayment_Plugin_Gateway_Skrill',NULL, '0');");
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(104, 'WebMoney', NULL, '0', 'Ynpayment_Plugin_Gateway_WebMoney',NULL, '0');");
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(105, 'BitPay', NULL, '0', 'Ynpayment_Plugin_Gateway_BitPay',NULL, '0');");	
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(106, 'Stripe', NULL, '0', 'Ynpayment_Plugin_Gateway_Stripe',NULL, '0');");
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(107, 'HeidelPay', NULL, '0', 'Ynpayment_Plugin_Gateway_HeidelPay',NULL, '0');");
		$db -> query("INSERT IGNORE INTO `engine4_payment_gateways` (`gateway_id`, `title`, `description`, `enabled`, `plugin`, `config`, `test_mode`) VALUES 
			(108, 'Braintree', NULL, '0', 'Ynpayment_Plugin_Gateway_Braintree',NULL, '0');");			
	}

	public function onDisable()
	{
		parent::onDisable();
		$db = $this -> getDb();
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 100;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 101;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 102;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 103;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 104;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 105;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 106;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 107;");
		$db -> query("DELETE FROM `engine4_payment_gateways` WHERE `engine4_payment_gateways`.`gateway_id` = 108;");
	}
}
