INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynmediaimporter', 'Social Media Importer', 'Social Media Importer is a right tool for your members if you want to enrich your site. It imports Photo from other top social networks like Facebook, Flickr, Picasa, etc.', '4.03', 1, 'extra') ;

-- Core menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('ynmediaimporter_main', 'standard', 'Media Importer Main Navigation Menu');

DROP TABLE IF  EXISTS engine4_ynmediaimporter_schedulers;

DROP TABLE IF  EXISTS engine4_ynmediaimporter_nodes;

CREATE TABLE IF NOT EXISTS `engine4_ynmediaimporter_nodes` (
  `node_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(64) NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_aid` int(10) unsigned NOT NULL DEFAULT '0',
  `scheduler_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_type` varchar(32) NOT NULL DEFAULT 'user',
  `id` varchar(64) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `aid` varchar(64) NOT NULL,
  `media` varchar(32) NOT NULL,
  `provider` varchar(32) NOT NULL,
  `photo_count` varchar(32) NOT NULL,
  `status` smallint(8) unsigned NOT NULL DEFAULT '0',
  `title` varchar(256) NOT NULL,
  `src_thumb` tinytext,
  `src_small` tinytext,
  `src_medium` tinytext,
  `src_big` tinytext,
  `description` text NOT NULL,
  PRIMARY KEY (`node_id`),
  UNIQUE KEY `nid` (`nid`,`user_id`),
  KEY `nid_user_id` (`nid`,`user_id`)
) ENGINE=InnoDB;


CREATE TABLE  `engine4_ynmediaimporter_schedulers` (
  `scheduler_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `last_run` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_type` varchar(32) NOT NULL DEFAULT 'user',
  `params` longtext NOT NULL,
  PRIMARY KEY (`scheduler_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=107 ;