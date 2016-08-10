--
-- Update module Weather
--

UPDATE `engine4_core_modules` SET `version` = '4.1.3p2'  WHERE `name` = 'weather';

UPDATE `engine4_core_menuitems` SET `label` = 'HE - Weather', `order` = 888 WHERE `name` = 'core_admin_main_plugins_weather';