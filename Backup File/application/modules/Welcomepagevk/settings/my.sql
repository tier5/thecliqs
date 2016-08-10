
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('welcomepagevk_adminsettings_main', 'welcomepagevk', 'Welcome VK Page ', '', '{"route":"welcomepagevk_admin"}', 'core_admin_main_plugins', '', 999);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('welcomepagevk', 'Welcome VK Page ', 'Welcome VK Page ', '4.1.1', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_settings`
--

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('welcomepagevk.enable',	'1');

-- --------------------------------------------------------


---- Delete row Theme `engine4_core_modules`--DELETE FROM `engine4_core_modules` WHERE name = 'Theme';