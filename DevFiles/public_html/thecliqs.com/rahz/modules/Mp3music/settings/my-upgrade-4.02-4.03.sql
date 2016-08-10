INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'user_profile_subscribeMp3', 'mp3music', 'Subscribe Mp3 Music', 'Mp3music_Plugin_Menus', '', 'user_profile', '', 1, 0, 99);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('mp3music_subscribed_new', 'mp3music', '{item:$subject} has created a new album: {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_mp3music_subscribed_new', 'mp3music', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');


DROP TABLE IF EXISTS `engine4_mp3music_subscriptions`;
CREATE TABLE IF NOT EXISTS `engine4_mp3music_subscriptions` (
  `subscription_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `subscriber_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`subscription_id`),
  UNIQUE KEY `user_id` (`user_id`,`subscriber_user_id`),
  KEY `subscriber_user_id` (`subscriber_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;