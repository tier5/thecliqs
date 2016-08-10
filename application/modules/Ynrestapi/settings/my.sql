INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynrestapi', 'YN - SE API', 'YN - SE API', '4.01', 1, 'extra') ;

-- --------------------------------------------------------

--
-- Oauth tables
--

DROP TABLE IF EXISTS `engine4_ynrestapi_oauth_clients`;
CREATE TABLE IF NOT EXISTS `engine4_ynrestapi_oauth_clients` (
	`client_id` VARCHAR(80) NOT NULL, 
	`client_secret` VARCHAR(80), 
	`redirect_uri` VARCHAR(2000) NOT NULL, 
	`grant_types` VARCHAR(80), 
	`scope` VARCHAR(100), 
	`user_id` VARCHAR(80), 
	`title` VARCHAR(255), 
	`timestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0', 
	CONSTRAINT `clients_client_id_pk` PRIMARY KEY (`client_id`)
);

DROP TABLE IF EXISTS `engine4_ynrestapi_oauth_access_tokens`;
CREATE TABLE IF NOT EXISTS `engine4_ynrestapi_oauth_access_tokens` (
	`access_token` VARCHAR(40) NOT NULL, 
	`client_id` VARCHAR(80) NOT NULL, 
	`user_id` VARCHAR(255), 
	`expires` TIMESTAMP NOT NULL, 
	`scope` VARCHAR(2000), 
	CONSTRAINT `access_token_pk` PRIMARY KEY (`access_token`)
);

DROP TABLE IF EXISTS `engine4_ynrestapi_oauth_authorization_codes`;
CREATE TABLE IF NOT EXISTS `engine4_ynrestapi_oauth_authorization_codes` (
	`authorization_code` VARCHAR(40) NOT NULL, 
	`client_id` VARCHAR(80) NOT NULL, 
	`user_id` VARCHAR(255), 
	`redirect_uri` VARCHAR(2000), 
	`expires` TIMESTAMP NOT NULL, 
	`scope` VARCHAR(2000), 
	CONSTRAINT `auth_code_pk` PRIMARY KEY (`authorization_code`)
);

DROP TABLE IF EXISTS `engine4_ynrestapi_oauth_refresh_tokens`;
CREATE TABLE IF NOT EXISTS `engine4_ynrestapi_oauth_refresh_tokens` (
	`refresh_token` VARCHAR(40) NOT NULL, 
	`client_id` VARCHAR(80) NOT NULL, 
	`user_id` VARCHAR(255), 
	`expires` TIMESTAMP NOT NULL, 
	`scope` VARCHAR(2000), 
	CONSTRAINT `refresh_token_pk` PRIMARY KEY (`refresh_token`)
);

DROP TABLE IF EXISTS `engine4_ynrestapi_oauth_scopes`;
CREATE TABLE IF NOT EXISTS `engine4_ynrestapi_oauth_scopes` (
	`scope_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`scope` TEXT, 
	`is_default` BOOLEAN,
	PRIMARY KEY (`scope_id`)
);

DROP TABLE IF EXISTS `engine4_ynrestapi_oauth_jwt`;
CREATE TABLE IF NOT EXISTS `engine4_ynrestapi_oauth_jwt` (`client_id` VARCHAR(80) NOT NULL, 
	`subject` VARCHAR(80), 
	`public_key` VARCHAR(2000), 
	CONSTRAINT `jwt_client_id_pk` PRIMARY KEY (`client_id`)
);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_ynrestapi_oauth_scopes`
--

INSERT IGNORE INTO `engine4_ynrestapi_oauth_scopes` (`scope_id`, `scope`, `is_default`) VALUES
(1, 'basic', 1),
(2, 'settings', 0),
(3, 'activities', 0),
(4, 'albums', 0),
(5, 'friends', 0),
(6, 'messages', 0),
(7, 'videos', 0),
(8, 'blogs', 0),
(9, 'groups', 0),
(10, 'music', 0),
(11, 'classifieds', 0),
(12, 'events', 0);

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynrestapi', 'ynrestapi', 'YN SE API', '', '{"route":"admin_default","module":"ynrestapi","controller":"manage"}', 'core_admin_main_plugins', '', 999),

('ynrestapi_admin_main_categories', 'ynrestapi', 'Manage Clients', '', '{"route":"admin_default","module":"ynrestapi","controller":"manage"}', 'ynrestapi_admin_main', '', 1);
