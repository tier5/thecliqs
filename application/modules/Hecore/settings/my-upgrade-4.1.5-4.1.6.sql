--
-- Update module Hecore
--

UPDATE `engine4_core_modules` SET `version` = '4.1.6'  WHERE `name` = 'hecore';

DELETE FROM `engine4_core_menuitems` WHERE `name` = 'hecore_admin_main_plugins';