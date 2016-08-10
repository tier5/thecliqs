--
-- Update module Checkin
--

UPDATE `engine4_core_modules` SET `version` = '4.1.2'  WHERE `name` = 'checkin';

UPDATE `engine4_core_menuitems` SET `label`='HE - Checkin', `order` = '888' WHERE `name`='core_admin_main_plugins_checkin';
