UPDATE `engine4_core_modules` SET `version` = '4.02p5' where 'name' = 'yncontest';

DELETE FROM `engine4_core_menuitems` WHERE `name` = 'yncontest_admin_main_gateway' LIMIT 1;