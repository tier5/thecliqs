UPDATE `engine4_core_modules` SET `version` = '4.01p3' WHERE `name` = 'ynfilesharing';

ALTER TABLE `engine4_ynfilesharing_files` ADD COLUMN `status` INT(1) DEFAULT '0' NULL AFTER `share_code`;

INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('YN FileSharing Upload To Scribd', 'ynfilesharing_scribd_uploader', 'ynfilesharing', 'Ynfilesharing_Plugin_Job_Upload', NULL, 1, 75, 1);


