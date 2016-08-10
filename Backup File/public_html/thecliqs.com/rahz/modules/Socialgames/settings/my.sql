INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'socialgames_game' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'socialgames_game' as `type`,
    'activity' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
  
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('socialgames_new', 'socialgames', '{item:$subject} started play a game:', 1, 5, 1, 3, 1, 1);

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'socialgames_game' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
  
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'socialgames_game' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
  
  CREATE TABLE IF NOT EXISTS `engine4_socialgames_games` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `launch` text NOT NULL,
  `photo_id` text NOT NULL,
  `flash` text NOT NULL,
  `instruction` text NOT NULL,
  `total_members` int(11) NOT NULL,
  `is_active` int(11) NOT NULL,
  `is_featured` int(11) NOT NULL,
  `category` text NOT NULL,
  `total_views` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image` text NOT NULL,
  PRIMARY KEY (`game_id`),
  UNIQUE KEY `game_id` (`game_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `engine4_socialgames_favourite` (
  `favourite_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  PRIMARY KEY (`favourite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `engine4_socialgames_play` (
  `play_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  PRIMARY KEY (`play_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


