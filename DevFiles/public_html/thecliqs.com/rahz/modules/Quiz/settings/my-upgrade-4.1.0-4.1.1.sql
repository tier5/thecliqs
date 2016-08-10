UPDATE `engine4_core_modules` SET `version` = '4.1.1'  WHERE `name` = 'quiz';

ALTER TABLE `engine4_quiz_quizs` CHANGE `approved` `approved` TINYINT( 1 ) NOT NULL DEFAULT '1';

UPDATE `engine4_quiz_quizs` SET `approved` = 1;