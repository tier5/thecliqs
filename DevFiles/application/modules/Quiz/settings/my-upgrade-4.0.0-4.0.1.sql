
UPDATE `engine4_core_modules` SET `version` = '4.0.1'  WHERE `name` = 'quiz';

ALTER TABLE `engine4_quiz_results`  ADD COLUMN `search` TINYINT(2) NOT NULL DEFAULT '0' AFTER `photo_id`;

DELETE FROM `engine4_core_search` WHERE `type` = 'quiz_result';

UPDATE `engine4_activity_actiontypes` SET `body`='{item:$subject} commented on {item:$owner}\'s {item:$object:quiz}' WHERE `type`='comment_quiz';