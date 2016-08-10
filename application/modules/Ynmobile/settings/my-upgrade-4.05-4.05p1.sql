
DROP TABLE IF EXISTS `engine4_ynmobile_menuitems`;
CREATE TABLE IF NOT EXISTS `engine4_ynmobile_menuitems` (
  `menuitem_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'Core',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `order` smallint(6) NOT NULL DEFAULT '999',
  `label` varchar(50) NOT NULL,
  `layout` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `uri` varchar(50) NOT NULL,
  `menu` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`menuitem_id`),
  UNIQUE KEY `name` (`name`),
  KEY `LOOKUP` (`name`,`order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `engine4_ynmobile_menuitems`
--

INSERT INTO `engine4_ynmobile_menuitems` (`menuitem_id`, `name`, `module`, `enabled`, `order`, `label`, `layout`, `icon`, `uri`, `menu`) VALUES
(29, 'new_feeds', 'core', 1, 1, 'News Feed', 'new_feed', 'icon-rss', 'home', 1),
(30, 'event', 'event', 1, 999, 'Events', 'event', 'icon-calendar', 'upcommingevent', 1),
(31, 'ynevent', 'ynevent', 1, 5, 'Events', 'event', 'icon-calendar', 'upcommingevent', 1),
(32, 'friends', 'core', 1, 2, 'Friends', 'friend', 'icon-group', 'friend', 1),
(33, 'album', 'album', 1, 999, 'Albums', 'photo', 'icon-picture', 'albumLanding', 1),
(34, 'advalbum', 'advalbum', 1, 6, 'Albums', 'photo', 'icon-picture', 'photoList', 1),
(35, 'music', 'music', 1, 3, 'Music', 'music', 'icon-music', 'music', 1),
(36, 'video', 'video', 1, 7, 'Videos', 'video', 'icon-facetime-video', 'video', 1),
(37, 'ynvideo', 'ynvideo', 1, 999, 'Videos', 'video', 'icon-facetime-video', 'video', 1),
(38, 'mail', 'core', 1, 4, 'Mails', 'mail', 'icon-envelope-alt', 'maillist', 1),
(41, 'forum', 'Core', 1, 999, 'Forums', 'forum', 'fa-comments', 'forums', 1);