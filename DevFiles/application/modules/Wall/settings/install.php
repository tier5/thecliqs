<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Installer extends Engine_Package_Installer_Module
{
  public function onPreInstall()
  {
    parent::onPreInstall();

    $db = $this->getDb();
    $translate = Zend_Registry::get('Zend_Translate');

    $select = $db->select()
        ->from('engine4_core_modules')
        ->where('name = ?', 'hecore')
        ->where('enabled = ?', 1);

    $hecore = $db->fetchRow($select);

    if (!$hecore) {
      $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
      return $this->_error($error_message);
    }

    if (version_compare($hecore['version'], '4.2.0p1') < 0) {
      $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
      return $this->_error($error_message);
    }

    $operation = $this->_databaseOperationType;
    $module_name = $this->getOperation()->getTargetPackage()->getName();
	$package = $this->_operation->getPrimaryPackage();
	
    // Keygen by TrioxX
    // This one does NOT generate valid keys
    // It's just to make the key look legit ;)
    $licenseKey = strtoupper(substr(md5(md5($package->getName()) . md5($_SERVER['HTTP_HOST'])), 0, 16));

    $select = $db->select()
        ->from('engine4_hecore_modules')
        ->where('name = ?', $module_name);

    $module = $db->fetchRow($select);

    if ($module && isset($module['installed']) && $module['installed']
        && isset($module['version']) && $module['version'] == $this->_targetVersion
        && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
    ) {
      return;
    }

    // unistall Advanced Wall
    $result = $db->query("SELECT * FROM engine4_core_modules WHERE name = 'wall' AND title = 'Advanced Wall'")->fetchAll();
    if (!empty($result[0])){
      $operation = 'install';
    }

    if ($operation == 'install') {

      if ($module && $module['installed']) {
        return;
      }

		$db = Engine_Db_Table::getDefaultAdapter();

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_fbpages` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `fbpage_id` varchar(11) DEFAULT NULL,
		  `user_id` int(200) DEFAULT NULL,
		  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_listitems` (
		`item_id` int(11) NOT NULL AUTO_INCREMENT,
		`object_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		`object_id` int(11) NOT NULL,
		`list_id` int(11) NOT NULL,
		PRIMARY KEY (`item_id`),
		UNIQUE KEY `object_type` (`object_type`,`object_id`,`list_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_lists` (
		`list_id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
		`label` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (`list_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_mute` (
		`mute_id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL DEFAULT '0',
		`action_id` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`mute_id`),
		UNIQUE KEY `user_id` (`user_id`,`action_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_privacy` (
		`action_id` int(11) NOT NULL,
		`privacy` varchar(30) NOT NULL,
		PRIMARY KEY (`action_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_smiles` (
		`smile_id` int(11) NOT NULL AUTO_INCREMENT,
		`tag` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
		`title` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
		`file_id` int(11) DEFAULT NULL,
		`file_src` varchar(90) COLLATE utf8_unicode_ci DEFAULT NULL,
		`default` tinyint(1) DEFAULT '0',
		`enabled` tinyint(1) DEFAULT '0',
		PRIMARY KEY (`smile_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_tags` (
		`tag_id` int(11) NOT NULL AUTO_INCREMENT,
		`action_id` int(11) NOT NULL DEFAULT '0',
		`object_id` int(11) NOT NULL DEFAULT '0',
		`object_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		`user_id` int(11) NOT NULL DEFAULT '0',
		`is_people` tinyint(1) NOT NULL DEFAULT '0',
		`value` varchar(90) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		PRIMARY KEY (`tag_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_tokens` (
		`token_id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
		`object_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`object_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`oauth_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`oauth_token_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		`creation_date` datetime NOT NULL,
		PRIMARY KEY (`token_id`),
		UNIQUE KEY `user_id` (`user_id`,`object_id`,`provider`)
		) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_wall_usersettings` (
		  `user_id` int(11) NOT NULL,
		  `share_facebook_enabled` tinyint(1) NOT NULL,
		  `share_twitter_enabled` tinyint(1) NOT NULL,
		  `share_linkedin_enabled` tinyint(1) DEFAULT '0',
		  `mode` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		  `type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		  `list_id` int(11) NOT NULL,
		  `privacy_user` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		  `privacy_page` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

		$db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
		('wall_tag', 'wall', 'WALL_NOTIFICATION_TAG', '0', '', '1')");

		$db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
		('notify_wall_tag', 'wall', '[host],[email],[recipient_title],[object_title],[object_link]')");

		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
		('wall_admin_main_setting', 'wall', 'WALL_ADMIN_MAIN_SETTING', '', '{\"route\":\"admin_default\", \"module\": \"wall\", \"controller\": \"setting\", \"action\": \"index\"}', 'wall_admin_main', NULL, '1', '0', '2'),
		('wall_admin_main_activity', 'wall', 'WALL_ADMIN_MAIN_ACTIVITY', '', '{\"route\":\"admin_default\",\"module\":\"activity\",\"controller\":\"settings\",\"action\":\"index\"}', 'wall_admin_main', NULL, '1', '0', '1'),
		('wall_admin_main_plugins_wall', 'wall', 'WALL_ADMIN_MAIN_PLUGINS_WALL', '', '{\"route\":\"admin_default\", \"module\": \"wall\", \"controller\": \"setting\", \"action\": \"index\"}', 'core_admin_main_plugins', NULL, '1', '0', '888')");

		$db->query("
		INSERT IGNORE INTO `engine4_wall_smiles` (`smile_id`, `tag`, `title`, `file_id`, `file_src`, `default`, `enabled`) VALUES
		(1, ' :) ', 'Smile', NULL, 'application/modules/Wall/externals/images/smiles/smile.png', 1, 1),
		(2, ' :)) ', 'Big Smile', NULL, 'application/modules/Wall/externals/images/smiles/smile-big.png', 1, 1),
		(3, ' :D ', 'Grin', NULL, 'application/modules/Wall/externals/images/smiles/grin.png', 1, 1),
		(4, ' :laugh: ', 'Laugh', NULL, 'application/modules/Wall/externals/images/smiles/laugh.png', 1, 1),
		(5, ' :-( ', 'Frown', NULL, 'application/modules/Wall/externals/images/smiles/frown.png', 1, 1),
		(6, ' :-(( ', 'Big Frown', NULL, 'application/modules/Wall/externals/images/smiles/frown-big.png', 1, 1),
		(7, ' :( ', 'Cry', NULL, 'application/modules/Wall/externals/images/smiles/crying.png', 1, 1),
		(8, ' :| ', 'Neutral', NULL, 'application/modules/Wall/externals/images/smiles/neutral.png', 1, 1),
		(9, ' ;) ', 'Wink', NULL, 'application/modules/Wall/externals/images/smiles/wink.png', 1, 1),
		(10, ' :-* ', 'Kiss', NULL, 'application/modules/Wall/externals/images/smiles/kiss.png', 1, 1),
		(11, ' :P ', 'Razz', NULL, 'application/modules/Wall/externals/images/smiles/razz.png', 1, 1),
		(12, ' :chic: ', 'Chic', NULL, 'application/modules/Wall/externals/images/smiles/chic.png', 1, 1),
		(13, ' 8-) ', 'Cool', NULL, 'application/modules/Wall/externals/images/smiles/cool.png', 1, 1),
		(14, ' :-X ', 'Angry', NULL, 'application/modules/Wall/externals/images/smiles/angry.png', 1, 1),
		(15, ' :reallyangry: ', 'Really Angry', NULL, 'application/modules/Wall/externals/images/smiles/really-angry.png', 1, 1),
		(16, ' :-? ', 'Confused', NULL, 'application/modules/Wall/externals/images/smiles/confused.png', 1, 1),
		(17, ' ?:-) ', 'Question', NULL, 'application/modules/Wall/externals/images/smiles/question.png', 1, 1),
		(18, ' :-/ ', 'Thinking', NULL, 'application/modules/Wall/externals/images/smiles/thinking.png', 1, 1),
		(19, ' :pain: ', 'Pain', NULL, 'application/modules/Wall/externals/images/smiles/pain.png', 1, 1),
		(20, ' :shock: ', 'Shock', NULL, 'application/modules/Wall/externals/images/smiles/shock.png', 1, 1),
		(21, ' :yes: ', 'Yes', NULL, 'application/modules/Wall/externals/images/smiles/thumbs-up.png', 1, 1),
		(22, ' :no: ', 'No', NULL, 'application/modules/Wall/externals/images/smiles/thumbs-down.png', 1, 1),
		(23, ' :rotfl: ', 'LOL', NULL, 'application/modules/Wall/externals/images/smiles/rotfl.png', 1, 1),
		(24, ' :silly: ', 'Silly', NULL, 'application/modules/Wall/externals/images/smiles/silly.png', 1, 1),
		(25, ' :beauty: ', 'Beauty', NULL, 'application/modules/Wall/externals/images/smiles/beauty.png', 1, 1),
		(26, ' :lashes: ', 'Lashes', NULL, 'application/modules/Wall/externals/images/smiles/lashes.png', 1, 1),
		(27, ' :cute: ', 'Cute', NULL, 'application/modules/Wall/externals/images/smiles/cute.png', 1, 1),
		(28, ' :shy: ', 'Shy', NULL, 'application/modules/Wall/externals/images/smiles/bashful.png', 1, 1),
		(29, ' :blush: ', 'Blush', NULL, 'application/modules/Wall/externals/images/smiles/blush.png', 1, 1),
		(30, ' :kissed: ', 'Kissed', NULL, 'application/modules/Wall/externals/images/smiles/kissed.png', 1, 1),
		(31, ' :inlove: ', 'In Love', NULL, 'application/modules/Wall/externals/images/smiles/in-love.png', 1, 1),
		(32, ' :drool: ', 'Drool', NULL, 'application/modules/Wall/externals/images/smiles/drool.png', 1, 1),
		(33, ' :giggle: ', 'Giggle', NULL, 'application/modules/Wall/externals/images/smiles/giggle.png', 1, 1),
		(34, ' :snicker: ', 'Snicker', NULL, 'application/modules/Wall/externals/images/smiles/snicker.png', 1, 1),
		(35, ' :heh: ', 'Heh!', NULL, 'application/modules/Wall/externals/images/smiles/curl-lip.png', 1, 1),
		(36, ' :smirk: ', 'Smirk', NULL, 'application/modules/Wall/externals/images/smiles/smirk.png', 1, 1),
		(37, ' :wilt: ', 'Wilt', NULL, 'application/modules/Wall/externals/images/smiles/wilt.png', 1, 1),
		(38, ' :weep: ', 'Weep', NULL, 'application/modules/Wall/externals/images/smiles/weep.png', 1, 1),
		(39, ' :idk: ', 'IDK', NULL, 'application/modules/Wall/externals/images/smiles/dont-know.png', 1, 1),
		(40, ' :struggle: ', 'Struggle', NULL, 'application/modules/Wall/externals/images/smiles/struggle.png', 1, 1),
		(41, ' :sidefrown: ', 'Side Frown', NULL, 'application/modules/Wall/externals/images/smiles/sidefrown.png', 1, 1),
		(42, ' :dazed: ', 'Dazed', NULL, 'application/modules/Wall/externals/images/smiles/dazed.png', 1, 1),
		(43, ' :hypnotized: ', 'Hypnotized', NULL, 'application/modules/Wall/externals/images/smiles/hypnotized.png', 1, 1),
		(44, ' :sweat: ', 'Sweat', NULL, 'application/modules/Wall/externals/images/smiles/sweat.png', 1, 1),
		(45, ' :eek: ', 'Eek!', NULL, 'application/modules/Wall/externals/images/smiles/bug-eyes.png', 1, 1),
		(46, ' :roll: ', 'Roll Eyes', NULL, 'application/modules/Wall/externals/images/smiles/eyeroll.png', 1, 1),
		(47, ' :sarcasm: ', 'Sarcasm', NULL, 'application/modules/Wall/externals/images/smiles/sarcastic.png', 1, 1),
		(48, ' :disdain: ', 'Disdain', NULL, 'application/modules/Wall/externals/images/smiles/disdain.png', 1, 1),
		(49, ' :smug: ', 'Smug', NULL, 'application/modules/Wall/externals/images/smiles/arrogant.png', 1, 1),
		(50, ' :-$ ', 'Money Mouth', NULL, 'application/modules/Wall/externals/images/smiles/moneymouth.png', 1, 1),
		(51, ' :footmouth: ', 'Foot in Mouth', NULL, 'application/modules/Wall/externals/images/smiles/foot-in-mouth.png', 1, 1),
		(52, ' :shutmouth: ', 'Shut Mouth', NULL, 'application/modules/Wall/externals/images/smiles/shut-mouth.png', 1, 1),
		(53, ' :quiet: ', 'Quiet', NULL, 'application/modules/Wall/externals/images/smiles/quiet.png', 1, 1),
		(54, ' :shame: ', 'Shame', NULL, 'application/modules/Wall/externals/images/smiles/shame.png', 1, 1),
		(55, ' :beatup: ', 'Beat Up', NULL, 'application/modules/Wall/externals/images/smiles/beat-up.png', 1, 1),
		(56, ' :mean: ', 'Mean', NULL, 'application/modules/Wall/externals/images/smiles/mean.png', 1, 1),
		(57, ' :evilgrin: ', 'Evil Grin', NULL, 'application/modules/Wall/externals/images/smiles/evil-grin.png', 1, 1),
		(58, ' :teeth: ', 'Grit Teeth', NULL, 'application/modules/Wall/externals/images/smiles/teeth.png', 1, 1),
		(59, ' :shout: ', 'Shout', NULL, 'application/modules/Wall/externals/images/smiles/shout.png', 1, 1),
		(60, ' :pissedoff: ', 'Pissed Off', NULL, 'application/modules/Wall/externals/images/smiles/pissed-off.png', 1, 1),
		(61, ' :reallypissed: ', 'Really Pissed', NULL, 'application/modules/Wall/externals/images/smiles/really-pissed.png', 1, 1),
		(62, ' :razzmad: ', 'Mad Razz', NULL, 'application/modules/Wall/externals/images/smiles/razz-mad.png', 1, 1),
		(63, ' :X-P: ', 'Drunken Razz', NULL, 'application/modules/Wall/externals/images/smiles/razz-drunk.png', 1, 1),
		(64, ' :sick: ', 'Sick', NULL, 'application/modules/Wall/externals/images/smiles/sick.png', 1, 1),
		(65, ' :yawn: ', 'Yawn', NULL, 'application/modules/Wall/externals/images/smiles/yawn.png', 1, 1),
		(66, ' :ZZZ: ', 'Sleepy', NULL, 'application/modules/Wall/externals/images/smiles/sleepy.png', 1, 1),
		(67, ' :dance: ', 'Dance', NULL, 'application/modules/Wall/externals/images/smiles/dance.png', 1, 1),
		(68, ' :clap: ', 'Clap', NULL, 'application/modules/Wall/externals/images/smiles/clap.png', 1, 1),
		(69, ' :jump: ', 'Jump', NULL, 'application/modules/Wall/externals/images/smiles/jump.png', 1, 1),
		(70, ' :handshake: ', 'Handshake', NULL, 'application/modules/Wall/externals/images/smiles/handshake.png', 1, 1),
		(71, ' :highfive: ', 'High Five', NULL, 'application/modules/Wall/externals/images/smiles/highfive.png', 1, 1),
		(72, ' :hugleft: ', 'Hug Left', NULL, 'application/modules/Wall/externals/images/smiles/hug-left.png', 1, 1),
		(73, ' :hugright: ', 'Hug Right', NULL, 'application/modules/Wall/externals/images/smiles/hug-right.png', 1, 1),
		(74, ' :kissblow: ', 'Kiss Blow', NULL, 'application/modules/Wall/externals/images/smiles/kiss-blow.png', 1, 1),
		(75, ' :kissing: ', 'Kissing', NULL, 'application/modules/Wall/externals/images/smiles/kissing.png', 1, 1),
		(76, ' :bye: ', 'Bye', NULL, 'application/modules/Wall/externals/images/smiles/bye.png', 1, 1),
		(77, ' :goaway: ', 'Go Away', NULL, 'application/modules/Wall/externals/images/smiles/go-away.png', 1, 1),
		(78, ' :callme: ', 'Call Me', NULL, 'application/modules/Wall/externals/images/smiles/call-me.png', 1, 1),
		(79, ' :onthephone: ', 'On the Phone', NULL, 'application/modules/Wall/externals/images/smiles/on-the-phone.png', 1, 1),
		(80, ' :secret: ', 'Secret', NULL, 'application/modules/Wall/externals/images/smiles/secret.png', 1, 1),
		(81, ' :meeting: ', 'Meeting', NULL, 'application/modules/Wall/externals/images/smiles/meeting.png', 1, 1),
		(82, ' :waving: ', 'Waving', NULL, 'application/modules/Wall/externals/images/smiles/waving.png', 1, 1),
		(83, ' :stop: ', 'Stop', NULL, 'application/modules/Wall/externals/images/smiles/stop.png', 1, 1),
		(84, ' :timeout: ', 'Time Out', NULL, 'application/modules/Wall/externals/images/smiles/time-out.png', 1, 1),
		(85, ' :talktothehand: ', 'Talk to the Hand', NULL, 'application/modules/Wall/externals/images/smiles/talktohand.png', 1, 1),
		(86, ' :loser: ', 'Loser', NULL, 'application/modules/Wall/externals/images/smiles/loser.png', 1, 1),
		(87, ' :lying: ', 'Lying', NULL, 'application/modules/Wall/externals/images/smiles/lying.png', 1, 1),
		(88, ' :doh: ', 'DOH!', NULL, 'application/modules/Wall/externals/images/smiles/doh.png', 1, 1),
		(89, ' :fingersxd: ', 'Fingers Crossed', NULL, 'application/modules/Wall/externals/images/smiles/fingers-xd.png', 1, 1),
		(90, ' :waiting: ', 'Waiting', NULL, 'application/modules/Wall/externals/images/smiles/waiting.png', 1, 1),
		(91, ' :suspense: ', 'Suspense', NULL, 'application/modules/Wall/externals/images/smiles/nailbiting.png', 1, 1),
		(92, ' :tremble: ', 'Tremble', NULL, 'application/modules/Wall/externals/images/smiles/tremble.png', 1, 1),
		(93, ' :pray: ', 'Pray', NULL, 'application/modules/Wall/externals/images/smiles/pray.png', 1, 1),
		(94, ' :worship: ', 'Worship', NULL, 'application/modules/Wall/externals/images/smiles/worship.png', 1, 1),
		(95, ' :starving: ', 'Starving', NULL, 'application/modules/Wall/externals/images/smiles/starving.png', 1, 1),
		(96, ' :eat: ', 'Eat', NULL, 'application/modules/Wall/externals/images/smiles/eat.png', 1, 1),
		(97, ' :victory: ', 'Victory', NULL, 'application/modules/Wall/externals/images/smiles/victory.png', 1, 1),
		(98, ' :curse: ', 'Curse', NULL, 'application/modules/Wall/externals/images/smiles/curse.png', 1, 1),
		(99, ' :alien: ', 'Alien', NULL, 'application/modules/Wall/externals/images/smiles/alien.png', 1, 1),
		(100, ' O:-) ', 'Angel', NULL, 'application/modules/Wall/externals/images/smiles/angel.png', 1, 1),
		(101, ' :clown: ', 'Clown', NULL, 'application/modules/Wall/externals/images/smiles/clown.png', 1, 1),
		(102, ' :cowboy: ', 'Cowboy', NULL, 'application/modules/Wall/externals/images/smiles/cowboy.png', 1, 1),
		(103, ' :cyclops: ', 'Cyclops', NULL, 'application/modules/Wall/externals/images/smiles/cyclops.png', 1, 1),
		(104, ' :devil: ', 'Devil', NULL, 'application/modules/Wall/externals/images/smiles/devil.png', 1, 1),
		(105, ' :doctor: ', 'Doctor', NULL, 'application/modules/Wall/externals/images/smiles/doctor.png', 1, 1),
		(106, ' :fighterf: ', 'Female Fighter', NULL, 'application/modules/Wall/externals/images/smiles/fighter-f.png', 1, 1),
		(107, ' :fighterm: ', 'Male Fighter', NULL, 'application/modules/Wall/externals/images/smiles/fighter-m.png', 1, 1),
		(108, ' :mohawk: ', 'Mohawk', NULL, 'application/modules/Wall/externals/images/smiles/mohawk.png', 1, 1),
		(109, ' :music: ', 'Music', NULL, 'application/modules/Wall/externals/images/smiles/music.png', 1, 1),
		(110, ' :nerd: ', 'Nerd', NULL, 'application/modules/Wall/externals/images/smiles/nerd.png', 1, 1),
		(111, ' :party: ', 'Party', NULL, 'application/modules/Wall/externals/images/smiles/party.png', 1, 1),
		(112, ' :pirate: ', 'Pirate', NULL, 'application/modules/Wall/externals/images/smiles/pirate.png', 1, 1),
		(113, ' :skywalker: ', 'Skywalker', NULL, 'application/modules/Wall/externals/images/smiles/skywalker.png', 1, 1),
		(114, ' :snowman: ', 'Snowman', NULL, 'application/modules/Wall/externals/images/smiles/snowman.png', 1, 1),
		(115, ' :soldier: ', 'Soldier', NULL, 'application/modules/Wall/externals/images/smiles/soldier.png', 1, 1),
		(116, ' :vampire: ', 'Vampire', NULL, 'application/modules/Wall/externals/images/smiles/vampire.png', 1, 1),
		(117, ' :zombiekiller: ', 'Zombie Killer', NULL, 'application/modules/Wall/externals/images/smiles/zombie-killer.png', 1, 1),
		(118, ' :ghost: ', 'Ghost', NULL, 'application/modules/Wall/externals/images/smiles/ghost.png', 1, 1),
		(119, ' :skeleton: ', 'Skeleton', NULL, 'application/modules/Wall/externals/images/smiles/skeleton.png', 1, 1),
		(120, ' :bunny: ', 'Bunny', NULL, 'application/modules/Wall/externals/images/smiles/bunny.png', 1, 1),
		(121, ' :cat: ', 'Cat', NULL, 'application/modules/Wall/externals/images/smiles/cat.png', 1, 1),
		(122, ' :cat2: ', 'Cat 2', NULL, 'application/modules/Wall/externals/images/smiles/cat2.png', 1, 1),
		(123, ' :chick: ', 'Chick', NULL, 'application/modules/Wall/externals/images/smiles/chick.png', 1, 1),
		(124, ' :chicken: ', 'Chicken', NULL, 'application/modules/Wall/externals/images/smiles/chicken.png', 1, 1),
		(125, ' :chicken2: ', 'Chicken 2', NULL, 'application/modules/Wall/externals/images/smiles/chicken2.png', 1, 1),
		(126, ' :cow: ', 'Cow', NULL, 'application/modules/Wall/externals/images/smiles/cow.png', 1, 1),
		(127, ' :cow2: ', 'Cow 2', NULL, 'application/modules/Wall/externals/images/smiles/cow2.png', 1, 1),
		(128, ' :dog: ', 'Dog', NULL, 'application/modules/Wall/externals/images/smiles/dog.png', 1, 1),
		(129, ' :dog2: ', 'Dog 2', NULL, 'application/modules/Wall/externals/images/smiles/dog2.png', 1, 1),
		(130, ' :duck: ', 'Duck', NULL, 'application/modules/Wall/externals/images/smiles/duck.png', 1, 1),
		(131, ' :goat: ', 'Goat', NULL, 'application/modules/Wall/externals/images/smiles/goat.png', 1, 1),
		(132, ' :hippo: ', 'Hippo', NULL, 'application/modules/Wall/externals/images/smiles/hippo.png', 1, 1),
		(133, ' :koala: ', 'Koala', NULL, 'application/modules/Wall/externals/images/smiles/koala.png', 1, 1),
		(134, ' :lion: ', 'Lion', NULL, 'application/modules/Wall/externals/images/smiles/lion.png', 1, 1),
		(135, ' :monkey: ', 'Monkey', NULL, 'application/modules/Wall/externals/images/smiles/monkey.png', 1, 1),
		(136, ' :monkey2: ', 'Monkey 2', NULL, 'application/modules/Wall/externals/images/smiles/monkey2.png', 1, 1),
		(137, ' :mouse: ', 'Mouse', NULL, 'application/modules/Wall/externals/images/smiles/mouse.png', 1, 1),
		(138, ' :panda: ', 'Panda', NULL, 'application/modules/Wall/externals/images/smiles/panda.png', 1, 1),
		(139, ' :pig: ', 'Pig', NULL, 'application/modules/Wall/externals/images/smiles/pig.png', 1, 1),
		(140, ' :pig2: ', 'Pig 2', NULL, 'application/modules/Wall/externals/images/smiles/pig2.png', 1, 1),
		(141, ' :sheep: ', 'Sheep', NULL, 'application/modules/Wall/externals/images/smiles/sheep.png', 1, 1),
		(142, ' :sheep2: ', 'Sheep 2', NULL, 'application/modules/Wall/externals/images/smiles/sheep2.png', 1, 1),
		(143, ' :reindeer: ', 'Reindeer', NULL, 'application/modules/Wall/externals/images/smiles/reindeer.png', 1, 1),
		(144, ' :snail: ', 'Snail', NULL, 'application/modules/Wall/externals/images/smiles/snail.png', 1, 1),
		(145, ' :tiger: ', 'Tiger', NULL, 'application/modules/Wall/externals/images/smiles/tiger.png', 1, 1),
		(146, ' :turtle: ', 'Turtle', NULL, 'application/modules/Wall/externals/images/smiles/turtle.png', 1, 1),
		(147, ' :beer: ', 'Beer', NULL, 'application/modules/Wall/externals/images/smiles/beer.png', 1, 1),
		(148, ' :drink: ', 'Drink', NULL, 'application/modules/Wall/externals/images/smiles/drink.png', 1, 1),
		(149, ' :liquor: ', 'Liquor', NULL, 'application/modules/Wall/externals/images/smiles/liquor.png', 1, 1),
		(150, ' :coffee: ', 'Coffee', NULL, 'application/modules/Wall/externals/images/smiles/coffee.png', 1, 1),
		(151, ' :cake: ', 'Cake', NULL, 'application/modules/Wall/externals/images/smiles/cake.png', 1, 1),
		(152, ' :pizza: ', 'Pizza', NULL, 'application/modules/Wall/externals/images/smiles/pizza.png', 1, 1),
		(153, ' :watermelon: ', 'Watermelon', NULL, 'application/modules/Wall/externals/images/smiles/watermelon.png', 1, 1),
		(154, ' :bowl: ', 'Bowl', NULL, 'application/modules/Wall/externals/images/smiles/bowl.png', 1, 1),
		(155, ' :plate: ', 'Plate', NULL, 'application/modules/Wall/externals/images/smiles/plate.png', 1, 1),
		(156, ' :can: ', 'Can', NULL, 'application/modules/Wall/externals/images/smiles/can.png', 1, 1),
		(157, ' :female: ', 'Female', NULL, 'application/modules/Wall/externals/images/smiles/female.png', 1, 1),
		(158, ' :male: ', 'Male', NULL, 'application/modules/Wall/externals/images/smiles/male.png', 1, 1),
		(159, ' :heart: ', 'Heart', NULL, 'application/modules/Wall/externals/images/smiles/heart.png', 1, 1),
		(160, ' :brokenheart: ', 'Broken Heart', NULL, 'application/modules/Wall/externals/images/smiles/heart-broken.png', 1, 1),
		(161, ' :rose: ', 'Rose', NULL, 'application/modules/Wall/externals/images/smiles/rose.png', 1, 1),
		(162, ' :deadrose: ', 'Dead Rose', NULL, 'application/modules/Wall/externals/images/smiles/rose-dead.png', 1, 1),
		(163, ' :peace: ', 'Peace', NULL, 'application/modules/Wall/externals/images/smiles/peace.png', 1, 1),
		(165, ' :flagus: ', 'US Flag', NULL, 'application/modules/Wall/externals/images/smiles/flag-us.png', 1, 1),
		(166, ' :moon: ', 'Moon', NULL, 'application/modules/Wall/externals/images/smiles/moon.png', 1, 1),
		(167, ' :star: ', 'Star', NULL, 'application/modules/Wall/externals/images/smiles/star.png', 1, 1),
		(168, ' :sun: ', 'Sun', NULL, 'application/modules/Wall/externals/images/smiles/sun.png', 1, 1),
		(169, ' :cloudy: ', 'Cloudy', NULL, 'application/modules/Wall/externals/images/smiles/cloudy.png', 1, 1),
		(170, ' :rain: ', 'Rain', NULL, 'application/modules/Wall/externals/images/smiles/rain.png', 1, 1),
		(171, ' :thunder: ', 'Thunder', NULL, 'application/modules/Wall/externals/images/smiles/thunder.png', 1, 1),
		(172, ' :umbrella: ', 'Umbrella', NULL, 'application/modules/Wall/externals/images/smiles/umbrella.png', 1, 1),
		(173, ' :rainbow: ', 'Rainbow', NULL, 'application/modules/Wall/externals/images/smiles/rainbow.png', 1, 1),
		(174, ' :musicnote: ', 'Music Note', NULL, 'application/modules/Wall/externals/images/smiles/music-note.png', 1, 1),
		(175, ' :airplane: ', 'Airplane', NULL, 'application/modules/Wall/externals/images/smiles/airplane.png', 1, 1),
		(176, ' :car: ', 'Car', NULL, 'application/modules/Wall/externals/images/smiles/car.png', 1, 1),
		(177, ' :island: ', 'Island', NULL, 'application/modules/Wall/externals/images/smiles/island.png', 1, 1),
		(178, ' :announce: ', 'Announce', NULL, 'application/modules/Wall/externals/images/smiles/announce.png', 1, 1),
		(179, ' :brb: ', 'brb', NULL, 'application/modules/Wall/externals/images/smiles/brb.png', 1, 1),
		(180, ' :mail: ', 'Mail', NULL, 'application/modules/Wall/externals/images/smiles/mail.png', 1, 1),
		(181, ' :cell: ', 'Cell', NULL, 'application/modules/Wall/externals/images/smiles/mobile.png', 1, 1),
		(182, ' :phone: ', 'Phone', NULL, 'application/modules/Wall/externals/images/smiles/phone.png', 1, 1),
		(183, ' :camera: ', 'Camera', NULL, 'application/modules/Wall/externals/images/smiles/camera.png', 1, 1),
		(184, ' :film: ', 'Film', NULL, 'application/modules/Wall/externals/images/smiles/film.png', 1, 1),
		(185, ' :tv: ', 'TV', NULL, 'application/modules/Wall/externals/images/smiles/tv.png', 1, 1),
		(186, ' :clock: ', 'Clock', NULL, 'application/modules/Wall/externals/images/smiles/clock.png', 1, 1),
		(187, ' :lamp: ', 'Lamp', NULL, 'application/modules/Wall/externals/images/smiles/lamp.png', 1, 1),
		(188, ' :search: ', 'Search', NULL, 'application/modules/Wall/externals/images/smiles/search.png', 1, 1),
		(189, ' :coins: ', 'Coins', NULL, 'application/modules/Wall/externals/images/smiles/coins.png', 1, 1),
		(190, ' :computer: ', 'Computer', NULL, 'application/modules/Wall/externals/images/smiles/computer.png', 1, 1),
		(191, ' :console: ', 'Console', NULL, 'application/modules/Wall/externals/images/smiles/console.png', 1, 1),
		(192, ' :present: ', 'Present', NULL, 'application/modules/Wall/externals/images/smiles/present.png', 1, 1),
		(193, ' :soccer: ', 'Soccer', NULL, 'application/modules/Wall/externals/images/smiles/soccerball.png', 1, 1),
		(194, ' :clover: ', 'Clover', NULL, 'application/modules/Wall/externals/images/smiles/clover.png', 1, 1),
		(195, ' :pumpkin: ', 'Pumpkin', NULL, 'application/modules/Wall/externals/images/smiles/pumpkin.png', 1, 1),
		(196, ' :bomb: ', 'Bomb', NULL, 'application/modules/Wall/externals/images/smiles/bomb.png', 1, 1),
		(197, ' :hammer: ', 'Hammer', NULL, 'application/modules/Wall/externals/images/smiles/hammer.png', 1, 1),
		(198, ' :knife: ', 'Knife', NULL, 'application/modules/Wall/externals/images/smiles/knife.png', 1, 1),
		(199, ' :handcuffs: ', 'Handcuffs', NULL, 'application/modules/Wall/externals/images/smiles/handcuffs.png', 1, 1),
		(200, ' :pill: ', 'Pill', NULL, 'application/modules/Wall/externals/images/smiles/pill.png', 1, 1),
		(201, ' :poop: ', 'Poop', NULL, 'application/modules/Wall/externals/images/smiles/poop.png', 1, 1),
		(202, ' :cigarette: ', 'Cigarette', NULL, 'application/modules/Wall/externals/images/smiles/cigarette.png', 1, 1);
		");

		$db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('wall_index_view', 'Wall Posts', NULL, 'Posts', NULL, NULL, NULL, NULL, NULL, '[\"1\",\"2\",\"3\",\"4\",\"5\"]', 'no-subject', NULL)");

		$page_id = $db->lastInsertId();

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'main', 'NULL', '2', '[\"[]\"]', NULL)");
		$parent_content_id = $db->lastInsertId();
		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[\"[]\"]', NULL)");
		$parent_content_id_0 = $db->lastInsertId();
		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'core.content', '$parent_content_id_0', '3', '[\"[]\"]', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'right', '$parent_content_id', '5', '[\"[]\"]', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('wall_index_welcome', 'Wall Welcome', NULL, 'Welcome', NULL, NULL, NULL, NULL, NULL, NULL, 'no-subject', NULL)");

		$page_id = $db->lastInsertId();

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'main', 'NULL', '2', '[\"[]\"]', NULL)");
		$parent_content_id = $db->lastInsertId();
		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[\"[]\"]', NULL)");
		$parent_content_id_0 = $db->lastInsertId();
		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'inviter.home-inviter', '$parent_content_id_0', '5', '{\"title\":\"WALL_WELCOME_INVITER\",\"name\":\"inviter.home-inviter\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'wall.upload-photo', '$parent_content_id_0', '6', '{\"title\":\"WALL_WELCOME_UPLOAD_PHOTO\",\"name\":\"wall.upload-photo\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'wall.welcome', '$parent_content_id_0', '3', '{\"title\":\"WALL_WELCOME_WELCOME\",\"name\":\"wall.welcome\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'wall.people-know', '$parent_content_id_0', '12', '{\"title\":\"WALL_WELCOME_PEOPLE_KNOW\",\"name\":\"wall.people-know\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'credit.faq', '$parent_content_id_0', '8', '{\"title\":\"WALL_WELCOME_CREDIT_FAQ\",\"name\":\"credit.faq\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'suggest.autorecommendations', '$parent_content_id_0', '7', '{\"title\":\"WALL_WELCOME_SUGGESTION\",\"titleCount\":true,\"name\":\"suggest.autorecommendations\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'wall.most-liked', '$parent_content_id_0', '9', '{\"title\":\"WALL_WELCOME_LIKES\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'hegift.birthdays', '$parent_content_id_0', '10', '{\"title\":\"WALL_WELCOME_BIRTHDAYS\",\"name\":\"hegift.birthdays\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'wall.gift-actual', '$parent_content_id_0', '11', '{\"title\":\"WALL_WELCOME_GIFTACTUAL\"}', NULL)");

		$db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'wall.new-wall', '$parent_content_id_0', '4', '{\"title\":\"WALL_WELCOME_NEWWALL\"}', NULL)");

		// Check is right the table

		try {
		  $he_fields = Engine_Db_Table::getDefaultAdapter()->query("SHOW COLUMNS FROM engine4_wall_tags")->fetchAll();
		  $he_field_keys = array();
		  $he_valid_keys = array('tag_id', 'action_id', 'object_id', 'object_type', 'user_id', 'is_people', 'value');
		  if (!empty($he_fields)){
			foreach ($he_fields as $he_field){
			  $he_field_keys[] = $he_field['Field'];
			}
			$he_is_valid_table = true;

			foreach ($he_valid_keys as $he_key){
			  if (!in_array($he_key, $he_field_keys)){
				$he_is_valid_table = false;
			  }
			}
			if (!$he_is_valid_table){
			  Engine_Db_Table::getDefaultAdapter()->query("DROP TABLE engine4_wall_tags");
			}
		  }
		} catch (Exception $e){
		}
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
    }
    else { //$operation = upgrade|refresh
      $db = Engine_Db_Table::getDefaultAdapter();
		
	  $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }
}