ALTER TABLE `engine4_mp3music_album_songs` ADD `artist_id` INT( 11 ) NOT NULL DEFAULT '0' AFTER `other_singer_title_url`;
CREATE TABLE IF NOT EXISTS `engine4_mp3music_artists` (
  `artist_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`artist_id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `engine4_mp3music_artists` (`artist_id`, `title`, `photo_id`) VALUES
(1, 'Black Eyed Peas', 0),
(2, 'Aaron Carter', 0),
(3, 'Avril Lavigne', 0),
(4, 'Celine Dion', 0);
