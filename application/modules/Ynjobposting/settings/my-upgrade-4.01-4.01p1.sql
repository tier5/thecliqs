ALTER TABLE `engine4_ynjobposting_jobs` MODIFY `type` INT( 11 ) NOT NULL DEFAULT '0';  
ALTER TABLE `engine4_ynjobposting_jobs` MODIFY `level` INT( 11 ) NOT NULL DEFAULT '0'; 

ALTER TABLE `engine4_ynjobposting_alerts` MODIFY `type` INT( 11 ) NOT NULL DEFAULT '0';  
ALTER TABLE `engine4_ynjobposting_alerts` MODIFY `level` INT( 11 ) NOT NULL DEFAULT '0'; 

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynjobposting_admin_manage_jobtypes', 'ynjobposting', 'Manage Job Types', '', '{"route":"admin_default","module":"ynjobposting","controller":"jobtypes", "action":"index"}', 'ynjobposting_admin_main', '', 6),
('ynjobposting_admin_manage_joblevels', 'ynjobposting', 'Manage Job Levels', '', '{"route":"admin_default","module":"ynjobposting","controller":"joblevels", "action":"index"}', 'ynjobposting_admin_main', '', 6);


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_jobtypes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_jobtypes` (
  `jobtype_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`jobtype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynjobposting_jobtypes` (`title`) VALUES
('Full-time'),
('Part-time'),
('Unpaid'),
('Internship'),
('Contractor'),
('Freelancer');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynjobposting_joblevels`
--

CREATE TABLE IF NOT EXISTS `engine4_ynjobposting_joblevels` (
  `joblevel_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`joblevel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynjobposting_joblevels` (`title`) VALUES
('New Grad/Entry Level'),
('Experienced (Non-Manager)'),
('Team Leader/Supervisor'),
('Manager'),
('Vice Director'),
('Director'),
('CEO'),
('Vice President'),
('President');

