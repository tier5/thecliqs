UPDATE `engine4_core_modules` SET `version` = '4.02p4' where 'name' = 'yncontest';
UPDATE  `engine4_core_tasks` SET  `module` =  'yncontest' WHERE  `plugin` =  'Yncontest_Plugin_Task_Timeout';