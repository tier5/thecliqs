
--
-- Table structure for table `engine4_ynauction_orders`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_orders` (
`order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`gateway_id` int(11) unsigned NOT NULL,
`gateway_transaction_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
`status` enum('pending','completed','cancelled','failed') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'pending',
`creation_date` datetime NOT NULL,
`payment_date` datetime DEFAULT NULL,
`item_id` int(11) unsigned NOT NULL DEFAULT '0',
`price` decimal(16,2) NOT NULL DEFAULT '0',
`currency` char(3),
`security_code` text NOT NULL,
`invoice_code` text NOT NULL,
PRIMARY KEY (`order_id`),
KEY `user_id` (`user_id`),
KEY `gateway_id` (`gateway_id`),
KEY `state` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynauction_bids`
--
CREATE TABLE IF NOT EXISTS `engine4_ynauction_bids` (
  `bid_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ynauction_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ip` bigint(11) NOT NULL,
  `product_price` double(20,2) DEFAULT '0.00',
  `bid_time` datetime NOT NULL,
  `type` enum('single','bidbutler') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'single',
  `noti_status` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`bid_id`),
  KEY `item_id` (`product_id`),
  KEY `ynauction_user_id` (`ynauction_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynauction_bids`
--
CREATE TABLE IF NOT EXISTS `engine4_ynauction_proposals` (
  `proposal_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ynauction_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `proposal_price` double(20,2) DEFAULT '0.00',
  `proposal_time` datetime NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`proposal_id`),
  KEY `item_id` (`product_id`),
  KEY `ynauction_user_id` (`ynauction_user_id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;  

--
-- Table structure for table `engine4_ynauction_bills`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_bills` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice` varchar(70) NOT NULL,
  `sercurity` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `finance_account_id` int(11) DEFAULT NULL,
  `emal_receiver` varchar(255) NOT NULL,
  `payment_receiver_id` int(11) NOT NULL,
  `date_bill` int(11) NOT NULL,
  `bill_status` int(3) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL,
  `amount` double(20,2) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `currency` VARCHAR( 10 ) NULL DEFAULT NULL,
  `type` INT( 11 ) NOT NULL DEFAULT '1',
  `auto_approve` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bill_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

--
-- Table structure for table `engine4_ynauction_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_categories` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `photo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynauction_categories`
--

INSERT INTO `engine4_ynauction_categories` (`category_id`, `user_id`, `title`, `parent`, `photo_id`) VALUES
(1, 1, 'Real Estate', 0, 0),
(2, 1, 'Electronics', 0, 0),
(3, 1, 'Digital Cameras & Camcorders', 0, 0),
(4, 1, 'Computers & Laptops', 0, 0),
(5, 1, 'Mobiles & PDA''s', 0, 0),
(6, 1, 'Home & Garden', 0, 0),
(7, 1, 'Mp3 Players & Audio', 0, 0),
(9, 1, 'Travels', 0, 0),
(10, 1, 'Television & Home Cinema', 0, 0),
(11, 1, 'Lifestyle & Experience', 0, 0),
(12, 1, 'Gift Certificates', 0, 0),
(27, 1, 'CONSOLES', 2, 0),
(28, 1, 'VIDEO GAMES', 2, 0),
(29, 1, 'MICROSOFT', 27, 0),
(30, 1, 'NINTENDO', 27, 0),
(31, 1, 'SONY', 27, 0),
(32, 1, 'MICROSOFT', 28, 0),
(33, 1, 'NINTENDO', 28, 0),
(34, 1, 'SONY', 28, 0),
(35, 1, 'XBOX 360', 29, 0),
(36, 1, 'DS LITE', 30, 0),
(37, 1, 'Wii', 30, 0),
(38, 1, 'PLAYSTATION 3', 31, 0),
(39, 1, 'PSP SLIM', 31, 0),
(40, 1, '20 GB', 35, 0),
(41, 1, '60 GB', 35, 0),
(42, 1, 'ELITE', 35, 0),
(43, 1, 'BUNDLES', 35, 0),
(44, 1, 'BLACK', 36, 0),
(45, 1, 'WHITE', 36, 0),
(46, 1, 'PINK', 36, 0),
(47, 1, 'BUNDLES', 36, 0),
(48, 1, 'BUNDLES', 37, 0),
(49, 1, '40 GB', 38, 0),
(50, 1, '60 GB', 38, 0),
(51, 1, 'BUNDLES', 38, 0),
(52, 1, 'BLACK', 39, 0),
(53, 1, 'WHITE', 39, 0),
(54, 1, 'BUNDLES', 39, 0),
(55, 1, 'XBOX 360', 32, 0),
(56, 1, 'DS LITE', 33, 0),
(57, 1, 'Wii', 33, 0),
(58, 1, 'PLAYSTATION 3', 34, 0),
(59, 1, 'PSP SLIM', 34, 0),
(60, 1, 'SONY', 3, 0),
(61, 1, 'JVC', 3, 0),
(62, 1, 'CANON', 3, 0),
(63, 1, 'PANASONIC', 3, 0),
(64, 1, 'COMPUTERS', 4, 0),
(65, 1, 'APPLE', 64, 0),
(66, 1, 'DELL', 64, 0),
(67, 1, 'HP', 64, 0),
(68, 1, 'ACER', 64, 0),
(69, 1, 'PACKARD BELL', 64, 0),
(70, 1, 'IMAC', 65, 0),
(71, 1, 'INSPIRON', 66, 0),
(72, 1, 'XPS', 66, 0),
(73, 1, 'STUDIO HYBRID', 66, 0),
(74, 1, 'LAPTOPS', 4, 0),
(75, 1, 'SONY', 74, 0),
(76, 1, 'DELL', 74, 0),
(77, 1, 'ACER', 74, 0),
(78, 1, 'TOSHIBA', 74, 0),
(79, 1, 'MACBOOK', 74, 0),
(80, 1, 'AIR', 79, 0),
(81, 1, 'APPLE', 5, 0),
(82, 1, 'SAMSUNG', 5, 0),
(83, 1, 'NOKIA', 5, 0),
(84, 1, 'SONY ERICSSON', 5, 0),
(85, 1, 'HTC', 5, 0),
(86, 1, 'IPHONE', 81, 0),
(87, 1, 'i8510', 82, 0),
(88, 1, 'OMNIA', 82, 0),
(89, 1, 'N96', 83, 0),
(90, 1, 'W902', 84, 0),
(91, 1, 'TOUCH DIAMOND P3700', 85, 0),
(93, 1, 'APPLE', 7, 0),
(94, 1, 'NINTENDO', 7, 0),
(95, 1, 'SONY', 7, 0),
(96, 1, 'IPOD', 93, 0),
(97, 1, 'DS LITE', 94, 0),
(98, 1, 'PSP SLIM', 95, 0),
(99, 1, 'SHUFFLE', 96, 0),
(100, 1, 'CLASSIC', 96, 0),
(101, 1, 'NANO', 96, 0),
(102, 1, 'TOUCH', 96, 0),
(103, 1, '1GB', 99, 0),
(104, 1, '2GB', 99, 0),
(105, 1, '120GB', 100, 0),
(106, 1, '8GB', 101, 0),
(107, 1, '16GB', 101, 0),
(108, 1, '8GB', 102, 0),
(109, 1, '16GB', 102, 0),
(110, 1, '32GB', 102, 0),
(111, 1, 'WHITE', 103, 0),
(112, 1, 'BLUE', 103, 0),
(113, 1, 'GREEN', 103, 0),
(114, 1, 'RED', 103, 0),
(115, 1, 'PINK', 103, 0),
(116, 1, 'WHITE', 104, 0),
(117, 1, 'BLUE', 104, 0),
(118, 1, 'GREEN', 104, 0),
(119, 1, 'RED', 104, 0),
(120, 1, 'PINK', 104, 0),
(123, 1, 'BLACK', 106, 0),
(124, 1, 'PURPLE', 106, 0),
(125, 1, 'BLUE', 106, 0),
(126, 1, 'GREEN', 106, 0),
(127, 1, 'YELLOW', 106, 0),
(128, 1, 'ORANGE', 106, 0),
(129, 1, 'RED', 106, 0),
(130, 1, 'PINK', 106, 0),
(131, 1, 'WHITE', 107, 0),
(132, 1, 'BLACK', 107, 0),
(133, 1, 'PURPLE', 107, 0),
(134, 1, 'BLUE', 107, 0),
(135, 1, 'GREEN', 107, 0),
(136, 1, 'YELLOW', 107, 0),
(137, 1, 'ORANGE', 107, 0),
(138, 1, 'RED', 107, 0),
(139, 1, 'PINK', 107, 0),
(140, 1, 'BLACK', 97, 0),
(141, 1, 'WHITE', 97, 0),
(142, 1, 'PINK', 97, 0),
(143, 1, 'BUNDLES', 97, 0),
(144, 1, 'BLACK', 98, 0),
(146, 1, 'WHITE', 98, 0),
(147, 1, 'BUNDLES', 98, 0),
(155, 1, 'LCD', 10, 0),
(156, 1, 'PLASMA', 10, 0),
(157, 1, 'LG', 155, 0),
(158, 1, 'PANASONIC', 155, 0),
(159, 1, 'PHILLIPS', 155, 0),
(160, 1, 'PIONEER', 155, 0),
(161, 1, 'SAMSUNG', 155, 0),
(162, 1, 'SONY', 155, 0),
(163, 1, 'LG', 156, 0),
(164, 1, 'PANASONIC', 156, 0),
(165, 1, 'PHILLIPS', 156, 0),
(166, 1, 'PIONEER', 156, 0),
(167, 1, 'SAMSUNG', 156, 0),
(168, 1, 'SONY', 156, 0),
(175, 1, 'BMW', 174, 0),
(176, 1, '316', 175, 0),
(178, 1, 'Volvo', 174, 0),
(179, 1, 'Merceedes', 174, 0),
(180, 1, 'Ferrari', 174, 0),
(181, 1, 'Porsche', 174, 0),
(182, 1, 'S40', 178, 0),
(183, 1, 'S60', 178, 0),
(184, 1, 'V50', 178, 0),
(188, 1, 'Apple Mac', 64, 0),
(189, 1, 'Apple Mac', 74, 0),
(190, 1, 'Jewelry & Watches', 0, 0),
(191, 1, 'Tickets', 0, 0),
(192, 1, 'Web Domains', 0, 0),
(193, 1, 'Cash & Free Bids', 0, 0),
(194, 1, 'Cash', 193, 0),
(195, 1, 'Free Bids', 193, 0),
(196, 1, 'Silver', 107, 0),
(197, 1, 'Home Cinema', 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynauction_gateways`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_gateways` (
  `gateway_id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(70) NOT NULL,
  `admin_account` varchar(255) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`gateway_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

INSERT INTO `engine4_ynauction_gateways` (`gateway_id`, `gateway_name`, `admin_account`, `is_active`, `params`) VALUES
(1, 'Paypal', '', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynauction_payment_accounts`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_payment_accounts` (
  `paymentaccount_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(6) DEFAULT NULL,
  `account_username` varchar(255) DEFAULT NULL,
  `account_password` varchar(255) DEFAULT NULL,
  `payment_type` int(11) NOT NULL,
  `last_check_out` bigint(11) DEFAULT NULL,
  `account_status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`paymentaccount_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Store account payment.' ;


--
-- Table structure for table `engine4_ynauction_products`
--

  CREATE TABLE IF NOT EXISTS `engine4_ynauction_products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `rates` float NOT NULL DEFAULT '0',
  `description` text,
  `description1` text NOT NULL,
  `currency_symbol` varchar(100) NOT NULL,
  `price` double(20,2) DEFAULT NULL,
  `starting_bidprice` double(20,2) NOT NULL,
  `minimum_increment` double(20,2) NOT NULL DEFAULT '0.00',
  `maximum_increment` double(20,2) NOT NULL DEFAULT '0.00',
  `shipping_delivery` text NOT NULL,
  `local_only` tinyint(1) NOT NULL DEFAULT '0',
  `international` tinyint(1) NOT NULL DEFAULT '1',
  `payment_method` text NOT NULL,
  `photo_id` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `featured` enum('0','1') DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `bid_time` int(11) NOT NULL,
  `bid_price` double(20,2) NOT NULL,
  `bider_id` int(11) NOT NULL,
  `total_bids` int(11) NOT NULL,
  `delivery_cost` double(20,2) NOT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `creation_ip` bigint(11) NOT NULL,
  `is_delete` tinyint(4) NOT NULL DEFAULT '0',
  `stop` tinyint(1) NOT NULL DEFAULT '0',
  `display_home` tinyint(1) NOT NULL DEFAULT '0',
  `proposal` tinyint(1) NOT NULL DEFAULT '0',
  `total_fee` float(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`product_id`),
  KEY `index_uid` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `engine4_ynauction_rates`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_rates` (
  `rate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ynauction_id` int(11) unsigned NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `rate_number` int(11) unsigned NOT NULL,
  PRIMARY KEY (`rate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynauction_transaction_trackings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_transaction_trackings` (
  `transactiontracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_date` bigint(11) DEFAULT NULL,
  `user_seller` int(11) DEFAULT NULL,
  `user_buyer` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `amount` double(20,2) DEFAULT NULL,
  `account_seller_id` int(11) DEFAULT NULL,
  `method` VARCHAR(50) NOT NULL DEFAULT 'PayPal',
  `account_buyer_id` int(11) DEFAULT NULL,
  `transaction_status` int(11) DEFAULT NULL,
  `params` text NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',  
  `type` int(11) NOT NULL DEFAULT '1',  
  PRIMARY KEY (`transactiontracking_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='engine4_ynauction_transaction_tracking' ;


--
-- Table structure for table `engine4_ynauction_product_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_product_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynauction_product_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_product_fields_meta` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `alias` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NULL DEFAULT '0',
  `show` tinyint(1) unsigned NULL DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text COLLATE utf8_unicode_ci NOT NULL,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynauction_product_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_product_fields_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynauction_product_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_product_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynauction_product_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_product_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynauction_types`
--

CREATE TABLE IF NOT EXISTS `engine4_ynauction_confirms` (
  `confirm_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`confirm_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `engine4_ynauction_becomes` (
  `become_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0', 
  PRIMARY KEY (`become_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_ynauction_locations` (
  `location_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `pleft` int(11) NOT NULL,
  `pright` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `pleft` (`pleft`),
  KEY `pright` (`pright`),
  KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT IGNORE INTO `engine4_ynauction_locations` (`location_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES
(1, 0, NULL, 1, 12, 0, 'All Locations'),
(2, 0, 1, 2, 5, 1, 'USA');

CREATE TABLE IF NOT EXISTS `engine4_ynauction_albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `photo_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
  `collectible_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`),
  KEY `FK_engine4_groupbuy_albums_engine4_ynauction_products` (`product_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_ynauction_photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `album_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `image_title` varchar(128) NOT NULL,
  `image_description` varchar(255) NOT NULL,
  `collection_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `FK_engine4_ynauction_photos_engine4_ynauction_products` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


DROP TABLE IF EXISTS `engine4_ynauction_statics`;
CREATE TABLE IF NOT EXISTS `engine4_ynauction_statics` (
  `static_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `static_name` varchar(128) NOT NULL,
  `static_title` tinytext NOT NULL,
  `static_content` longtext NOT NULL,
  PRIMARY KEY (`static_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `engine4_ynauction_statics` (`static_id`, `static_name`, `static_title`, `static_content`) VALUES
    (1, 'terms', 'Terms of Service', '[Content Here]');

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynauction', ' Auction', 'This is  Auction module', '4.02', 1, 'extra') ;
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ynauction_main', 'standard', ' Auction Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynauction', 'ynauction', ' Auction', '', '{"route":"ynauction_general","action":"browse"}', 'core_main', '', 5),

('ynauction_main_browse', 'ynauction', 'Browse', '', '{"route":"ynauction_general","action":"browse"}', 'ynauction_main', '', 1),
('ynauction_main_become', 'ynauction', 'Become Auction Seller', 'Ynauction_Plugin_Menus::becomeSellerAuction', '{"route":"ynauction_general","action":"become"}', 'ynauction_main', '', 2),
('ynauction_main_participate', 'ynauction', 'Participate Auctions', 'Ynauction_Plugin_Menus::buyerConfirmAuction', '{"route":"ynauction_general","action":"participate"}', 'ynauction_main', '', 3),
('ynauction_main_manageauction', 'ynauction', 'My Auctions', 'Ynauction_Plugin_Menus::canManageAuctions', '{"route":"ynauction_general","action":"manageauction"}', 'ynauction_main', '', 4), 
('ynauction_main_account', 'ynauction', 'My Account', 'Ynauction_Plugin_Menus::canCreateAuctions', '{"route":"ynauction_account"}', 'ynauction_main', '', 5),
('ynauction_main_managewinning', 'ynauction', 'Winning', 'Ynauction_Plugin_Menus::buyerConfirmAuction', '{"route":"ynauction_winning"}', 'ynauction_main', '', 6),
('ynauction_main_boughts', 'ynauction', 'Buying', 'Ynauction_Plugin_Menus::buyerBoughtAuction', '{"route":"ynauction_proposal"}', 'ynauction_main', '', 6),
('ynauction_main_create', 'ynauction', 'Post Auction', 'Ynauction_Plugin_Menus::canCreateAuctions', '{"route":"ynauction_general","action":"create"}', 'ynauction_main', '', 7),

('core_admin_main_plugins_ynauction', 'ynauction', ' Auction', '', '{"route":"admin_default","module":"ynauction","controller":"manage"}', 'core_admin_main_plugins', '', 999),
('ynauction_admin_main_manage', 'ynauction', 'Manage Auctions','', '{"route":"admin_default","module":"ynauction","controller":"manage"}', 'ynauction_admin_main', '', 1),
('ynauction_admin_main_settings', 'ynauction', 'Global Settings', '', '{"route":"admin_default","module":"ynauction","controller":"settings"}', 'ynauction_admin_main', '', 2),
('ynauction_admin_main_level', 'ynauction', 'Member Level Settings', '', '{"route":"admin_default","module":"ynauction","controller":"level"}', 'ynauction_admin_main', '', 3),
('ynauction_admin_main_categories', 'ynauction', 'Categories', '', '{"route":"admin_default","module":"ynauction","controller":"category"}', 'ynauction_admin_main', '', 4),
('ynauction_admin_main_fields', 'ynauction', 'Questions', '', '{"route":"admin_default","module":"ynauction","controller":"fields"}', 'ynauction_admin_main', '', 5),
('ynauction_admin_main_accountmanage', 'ynauction', 'Accounts', '', '{"route":"admin_default","module":"ynauction","controller":"accountmanage"}', 'ynauction_admin_main', '', 6),
('ynauction_admin_main_statistics', 'ynauction', 'Transactions', '', '{"route":"admin_default","module":"ynauction","controller":"statistics"}', 'ynauction_admin_main', '', 8),
('ynauction_admin_main_paymentsettings', 'ynauction', 'Gateway', '', '{"route":"admin_default","module":"ynauction","controller":"paymentsettings"}', 'ynauction_admin_main', '', 9);

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynauction_new', 'ynauction', '{item:$subject} posted a new Auction:', 1, 5, 1, 3, 1, 1),
('comment_ynauction', 'ynauction', '{item:$subject} commented on {item:$owner}''s {item:$object:Auction}: {body:$body}', 1, 1, 1, 1, 1, 0);


  
-- Dumping data for table `engine4_core_pages`

INSERT IGNORE  INTO `engine4_core_pages`(`name`,displayname,`url`,`title`,`description`,`keywords`,`custom`,fragment,layout,view_count) 
VALUES 
('ynauction_index_browse','Auction Browse',NULL,'Auction Browse','This is the page Auctions','',0,0,'',0),
('ynauction_index_listing','Auction Search Listing',NULL,'Auction Search Listing','This is search auction page','',0,0,'',0),
('ynauction_index_detail','Auction Detail',NULL,'Auction Detail','This is the page detail Auction','',0,0,'',0);

--
-- Dumping data for table `engine4_core_content`
--

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'), 'container', 'top', NULL, '1', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.menu-ynauctions',(SELECT LAST_INSERT_ID() + 1),3,NULL,NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'), 'container', 'main', NULL, '2', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'), 'container', 'left', (SELECT LAST_INSERT_ID()) , '4', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'), 'container', 'right', (SELECT LAST_INSERT_ID()) , '5', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.latest-ynauctions',(SELECT LAST_INSERT_ID() + 1),1,'{\"title\":\"Latest Auctions\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.running-ynauctions',(SELECT LAST_INSERT_ID() + 1),2,'{\"title\":\"Running Auctions\"}',NULL), 
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.featured-ynauctions',(SELECT LAST_INSERT_ID() + 2),4,'{\"title\":\"Featured Auctions\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.listing-ynauctions',(SELECT LAST_INSERT_ID() + 2),5,'{\"title\":\"Listing Auctions\"}',NULL),  
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.search-ynauctions',(SELECT LAST_INSERT_ID() + 3),7,'{\"title\":\"Search Auctions\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.most-rated-ynauctions',(SELECT LAST_INSERT_ID() + 3),8,'{\"title\":\"Most Rated Auctions\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.most-liked-ynauctions',(SELECT LAST_INSERT_ID() + 3),9,'{\"title\":\"Most Liked Auctions\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.active-bidders-ynauctions',(SELECT LAST_INSERT_ID() + 3),10,'{\"title\":\"Active Bidder Auctions\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_browse'),'widget','ynauction.user-ynauctions',(SELECT LAST_INSERT_ID() + 3),11,'{\"title\":\"User Auctions\"}',NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'container', 'top', NULL, '1', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'),'widget','ynauction.menu-ynauctions',(SELECT LAST_INSERT_ID() + 1),3,NULL,NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'container', 'main', NULL, '2', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES 
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'container', 'right', (SELECT LAST_INSERT_ID()) , '5', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'),'widget','ynauction.detail-ynauctions',(SELECT LAST_INSERT_ID() + 1),1,'[""]',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'),'widget','core.container-tabs',(SELECT LAST_INSERT_ID() + 1),2,'{"max":6}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'),'widget','ynauction.other-user-ynauctions',(SELECT LAST_INSERT_ID() + 2),1,'{"title":"Other Auctions"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'),'widget','ynauction.related-ynauctions',(SELECT LAST_INSERT_ID() + 2),2,'{"title":"Related Auction"}',NULL)
;
INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES 
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'widget', 'ynauction.description-ynauctions', (SELECT LAST_INSERT_ID()+ 3) , '1', '{"title":"Description"}', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'widget', 'ynauction.bid-history-ynauctions', (SELECT LAST_INSERT_ID()+ 3) , '2', '{"title":"Bid History"}', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'widget', 'ynauction.proposal-history-ynauctions', (SELECT LAST_INSERT_ID()+ 3) , '3', '{"title":"Proposal History"}', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_detail'), 'widget', 'ynauction.shipping-payment-ynauctions', (SELECT LAST_INSERT_ID() + 3) , '4', '{"title":"Shipping & Payment"}', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'), 'container', 'top', NULL, '1', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'),'widget','ynauction.menu-ynauctions',(SELECT LAST_INSERT_ID() + 1),3,NULL,NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'), 'container', 'main', NULL, '2', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES 
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'), 'container', 'right', (SELECT LAST_INSERT_ID()) , '5', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'),'widget','ynauction.search-listing-ynauctions',(SELECT LAST_INSERT_ID() + 1),1,'[""]',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='ynauction_index_listing'),'widget','ynauction.search-ynauctions',(SELECT LAST_INSERT_ID() + 2),1,'[""]',NULL)
;

ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment, auth_html
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'auth_html' as `name`,
    3 as `value`,
    'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, delete, edit, view, comment, css, style, max, photo
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'max' as `name`,
    3 as `value`,
    1000 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, delete, edit, view, comment, css, style, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'max' as `name`,
    3 as `value`,
    50 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynauction_product' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');
 
CREATE TABLE `engine4_ynauction_faqs` (
    `faq_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('show','hide') NOT NULL DEFAULT 'hide',
    `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `owner_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `question` VARCHAR(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `creation_date` DATETIME NOT NULL,
    PRIMARY KEY (`faq_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=24;


CREATE TABLE `engine4_ynauction_helppages` (
    `helppage_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` ENUM('show','hide') NOT NULL,
    `ordering` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '999',
    `category_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `owner_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,                                                                                                                                                                                                                                         
    `creation_date` DATETIME NOT NULL,
    PRIMARY KEY (`helppage_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=33;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynauction_main_faqs', 'ynauction', 'FAQs', 'Ynauction_Plugin_Menus::canFaqs', '{"route":"ynauction_extended","controller":"faqs"}', 'ynauction_main', '', 1, 0, 9);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynauction_main_helps', 'ynauction', 'Help', 'Ynauction_Plugin_Menus::canHelp', '{"route":"ynauction_extended","controller":"help"}', 'ynauction_main', '', 1, 0, 8);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynauction_admin_main_locations', 'ynauction', 'Locations', '', '{"route":"admin_default","module":"ynauction","controller":"locations"}', 'ynauction_admin_main', '', 1, 0, 16);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynauction_admin_main_helps', 'ynauction', 'Help', '', '{"route":"admin_default","module":"ynauction","controller":"helps"}', 'ynauction_admin_main', '', 1, 0, 17);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynauction_admin_main_faqs', 'ynauction', 'FAQs', '', '{"route":"admin_default","module":"ynauction","controller":"faqs"}', 'ynauction_admin_main', '', 1, 0, 18);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynauction_admin_main_terms', 'ynauction', 'Terms', '', '{"route":"admin_default","module":"ynauction","controller":"terms"}', 'ynauction_admin_main', '', 1, 0, 19);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('ynauction_admin_main_sellers', 'ynauction', 'Manage Sellers', '', '{"route":"admin_default","module":"ynauction","controller":"sellers"}', 'ynauction_admin_main', '', 1, 0, 7);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynauction_approved_bought', 'ynauction', '{item:$subject} approved your buynow request of {item:$object:$label}.', 0, '', 1),
('ynauction_denied_bought', 'ynauction', '{item:$subject} denied approved your buynow request of {item:$object:$label}.', 0, '', 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynauction_approved_become', 'ynauction', 'Congratualtions. You are now an auction seller.', 0, '', 1),
('ynauction_denied_become', 'ynauction', 'Admin has denied your request. You can not become auction seller.', 0, '', 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynauction_approved_proposal', 'ynauction', '{item:$subject} approved your proposed price of {item:$object:$label}.', 0, '', 1),
('ynauction_denied_proposal', 'ynauction', '{item:$subject} denied your proposed price of {item:$object:$label}.', 0, '', 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynauction_bidded', 'ynauction', '{item:$subject} has bidded on your {item:$object:$label}.', 0, '', 1),
('ynauction_bidded_bidded', 'ynauction', '{item:$subject} bidded {item:$object:$label}.', 0, '', 1),
('ynauction_won', 'ynauction', '{item:$subject} has won on your {item:$object:$label}.', 0, '', 1),
('ynauction_won_bidded', 'ynauction', '{item:$subject} won {item:$object:$label}.', 0, '', 1),
('ynauction_stopped_bidded', 'ynauction', '{item:$subject} stopped {item:$object:$label}.', 0, '', 1),
('ynauction_deleted_bidded', 'ynauction', '{item:$subject} deleted {item:$object:$label}.', 0, '', 1)
;

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`,`module`, `vars`) VALUES
  ('notify_ynauction_bidded', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
  ('notify_ynauction_bidded_bidded', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
  ('notify_ynauction_won', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
  ('notify_ynauction_won_bidded', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
  ('notify_ynauction_deleted_bidded', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
  ('notify_ynauction_stopped_bidded', 'activity', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

  
INSERT DELAYED engine4_activity_notificationsettings(`user_id`,`type`,`email`)
SELECT user_id,'ynauction_bidded','0' FROM `engine4_users`; 
INSERT DELAYED engine4_activity_notificationsettings(`user_id`,`type`,`email`)
SELECT user_id,'ynauction_bidded_bidded','0' FROM `engine4_users`; 
INSERT DELAYED engine4_activity_notificationsettings(`user_id`,`type`,`email`)
SELECT user_id,'ynauction_deleted_bidded','0' FROM `engine4_users`; 
INSERT DELAYED engine4_activity_notificationsettings(`user_id`,`type`,`email`)
SELECT user_id,'ynauction_stopped_bidded','0' FROM `engine4_users`; 
INSERT DELAYED engine4_activity_notificationsettings(`user_id`,`type`,`email`)
SELECT user_id,'ynauction_won','0' FROM `engine4_users`; 
INSERT DELAYED engine4_activity_notificationsettings(`user_id`,`type`,`email`)
SELECT user_id,'ynauction_won_bidded','0' FROM `engine4_users`; 