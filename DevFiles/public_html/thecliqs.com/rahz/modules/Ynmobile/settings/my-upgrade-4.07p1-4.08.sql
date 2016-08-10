--
-- profile cover table
--

CREATE TABLE IF NOT EXISTS `engine4_ynmobile_profilecovers` (
  `profilecover_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) DEFAULT 'iphone',
  `owner_type` varchar(64) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `photo_id` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`profilecover_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;