<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
// 02.03.2013 - TrioxX

class Mobile_Installer extends Engine_Package_Installer_Module
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

    if ($operation == 'install') {

      if ($module && $module['installed']) {
        return;
      }

		$db = Engine_Db_Table::getDefaultAdapter();

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_mobile_content` (
		  `content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `page_id` int(11) unsigned NOT NULL,
		  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
		  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		  `parent_content_id` int(11) unsigned DEFAULT NULL,
		  `order` int(11) NOT NULL DEFAULT '1',
		  `params` text COLLATE utf8_unicode_ci,
		  `attribs` text COLLATE utf8_unicode_ci,
		  PRIMARY KEY (`content_id`),
		  KEY `page_id` (`page_id`,`order`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_mobile_menuitems` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		  `label` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
		  `plugin` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
		  `params` text COLLATE utf8_unicode_ci NOT NULL,
		  `menu` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
		  `submenu` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
		  `enabled` tinyint(1) NOT NULL DEFAULT '1',
		  `custom` tinyint(1) NOT NULL DEFAULT '0',
		  `order` smallint(6) NOT NULL DEFAULT '999',
		  PRIMARY KEY (`id`),
		  KEY `LOOKUP` (`name`,`order`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_mobile_menus` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		  `type` enum('standard','hidden','custom') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'standard',
		  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
		  `order` smallint(3) NOT NULL DEFAULT '999',
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `name` (`name`),
		  KEY `order` (`order`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_mobile_pages` (
		  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
		  `displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `description` text COLLATE utf8_unicode_ci NOT NULL,
		  `keywords` text COLLATE utf8_unicode_ci NOT NULL,
		  `custom` tinyint(1) NOT NULL DEFAULT '1',
		  `fragment` tinyint(1) NOT NULL DEFAULT '0',
		  `layout` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
		  `view_count` int(11) unsigned NOT NULL DEFAULT '0',
		  `levels` text COLLATE utf8_unicode_ci NOT NULL,
		  `module` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`page_id`),
		  UNIQUE KEY `name` (`name`),
		  UNIQUE KEY `url` (`url`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

		$db->query("CREATE TABLE IF NOT EXISTS `engine4_mobile_themes` (
		  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
		  `title` varchar(255) NOT NULL,
		  `description` text NOT NULL,
		  `active` tinyint(4) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`theme_id`),
		  UNIQUE KEY `name` (`name`),
		  KEY `active` (`active`)
		) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;");

		$db->query("INSERT IGNORE INTO `engine4_mobile_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
		('user_footer_privacy', 'core', 'Privacy', '', '{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"privacy\"}', 'user_footer', '', 1, 0, 1),
		('user_footer_terms', 'core', 'Terms of Service', '', '{\"route\":\"default\",\"core\":\"user\",\"controller\":\"help\",\"action\":\"terms\"}', 'user_footer', '', 1, 0, 2),
		('user_footer_contact', 'core', 'Contact', '', '{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"contact\"}', 'user_footer', '', 1, 0, 3),
		('user_home_updates', 'user', 'View Recent Updates', '', '{\"route\":\"recent_activity\",\"icon\":\"application/modules/User/externals/images/links/updates.png\"}', 'user_home', '', 1, 0, 1),
		('user_home_view', 'user', 'View My Profile', 'User_Plugin_Menus', '{\"route\":\"user_profile_self\",\"icon\":\"application/modules/User/externals/images/links/profile.png\"}', 'user_home', '', 1, 0, 2),
		('user_home_friends', 'user', 'Browse Members', '', '{\"route\":\"user_general\",\"controller\":\"index\",\"action\":\"browse\",\"icon\":\"application/modules/User/externals/images/links/search.png\"}', 'user_home', '', 1, 0, 4),
		('user_home_invite', 'invite', 'Invite Your Friends', 'Invite_Plugin_Menus::canInvite', '{\"route\":\"default\",\"module\":\"invite\",\"icon\":\"application/modules/Invite/externals/images/invite.png\"}', 'user_home', '', 1, 0, 5),
		('user_profile_friend', 'user', 'Friends', 'User_Plugin_Menus', '', 'user_profile', '', 1, 0, 2),
		('user_profile_message', 'messages', 'Send Message', 'Messages_Plugin_Menus', '', 'user_profile', '', 1, 0, 3),
		('core_footer_privacy', 'core', 'Privacy', '', '{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"privacy\"}', 'core_footer', '', 1, 0, 1),
		('core_footer_contact', 'core', 'Contact', '', '{\"route\":\"default\",\"module\":\"core\",\"controller\":\"help\",\"action\":\"contact\"}', 'core_footer', '', 1, 0, 3),
		('album_main_browse', 'album', 'Everyone''s Albums', 'Album_Plugin_Menus::canViewAlbums', '{\"route\":\"album_general\",\"action\":\"browse\"}', 'album_main', '', 1, 0, 1),
		('album_main_manage', 'album', 'My Albums', 'Album_Plugin_Menus::canCreateAlbums', '{\"route\":\"album_general\",\"action\":\"manage\"}', 'album_main', '', 1, 0, 2),
		('blog_main_browse', 'blog', 'Browse Entries', 'Blog_Plugin_Menus::canViewBlogs', '{\"route\":\"blog_general\"}', 'blog_main', '', 1, 0, 1),
		('blog_main_manage', 'blog', 'My Entries', 'Blog_Plugin_Menus::canCreateBlogs', '{\"route\":\"blog_general\",\"action\":\"manage\"}', 'blog_main', '', 1, 0, 2),
		('blog_gutter_list', 'blog', 'View All Entries', 'Blog_Plugin_Menus', '{\"route\":\"blog_view\",\"class\":\"buttonlink icon_blog_viewall\"}', 'blog_gutter', '', 1, 0, 1),
		('blog_gutter_delete', 'blog', 'Delete This Entry', 'Blog_Plugin_Menus', '{\"route\":\"blog_specific\",\"action\":\"delete\",\"class\":\"buttonlink icon_blog_delete\"}', 'blog_gutter', '', 1, 0, 4),
		('core_mini_auth', 'user', 'Auth', 'User_Plugin_Menus', '', 'core_footer', '', 1, 0, 998),
		('event_main_upcoming', 'event', 'Upcoming Events', '', '{\"route\":\"event_upcoming\"}', 'event_main', '', 1, 0, 1),
		('event_main_past', 'event', 'Past Events', '', '{\"route\":\"event_past\"}', 'event_main', '', 1, 0, 2),
		('event_main_manage', 'event', 'My Events', '', '{\"route\":\"event_general\",\"action\":\"manage\"}', 'event_main', '', 1, 0, 3),
		('core_main_home', 'core', 'Home', 'User_Plugin_Menus', '', 'core_main', '', 1, 0, 1),
		('core_mini_profile', 'user', 'My Profile', 'User_Plugin_Menus', '', 'core_main', '', 1, 0, 2),
		('core_mini_messages', 'messages', 'Messages', 'Messages_Plugin_Menus', '', 'core_main', '', 1, 0, 3),
		('activity_requests', 'activity', 'My Requests', '', '{\"route\":\"default\",\"action\":\"requests\", \"module\":\"activity\",\"controller\":\"notifications\"}', 'activity_main', '', 1, 0, 3),
		('activity_notifications', 'activity', 'My Notifications', '', '{\"route\":\"recent_activity\"}', 'activity_main', '', 1, 0, 2),
		('classified_main_browse', 'classified', 'Browse Listings', 'Classified_Plugin_Menus::canViewClassifieds', '{\"route\":\"classified_general\"}', 'classified_main', '', 1, 0, 1),
		('classified_main_manage', 'classified', 'My Listings', 'Classified_Plugin_Menus::canCreateClassifieds', '{\"route\":\"classified_general\",\"action\":\"manage\"}', 'classified_main', '', 1, 0, 2),
		('group_profile_member', 'group', 'Member', 'Group_Plugin_Menus', '', 'group_profile', '', 1, 0, 3),
		('group_profile_share', 'group', 'Share', 'Group_Plugin_Menus', '', 'group_profile', '', 1, 0, 5),
		('group_main_browse', 'group', 'Browse Groups', '', '{\"route\":\"group_general\",\"action\":\"browse\"}', 'group_main', '', 1, 0, 1),
		('group_main_manage', 'group', 'My Groups', 'Group_Plugin_Menus', '{\"route\":\"group_general\",\"action\":\"manage\"}', 'group_main', '', 1, 0, 2),
		('event_profile_member', 'event', 'Member', 'Event_Plugin_Menus', '', 'event_profile', '', 1, 0, 3),
		('event_profile_share', 'event', 'Share', 'Event_Plugin_Menus', '', 'event_profile', '', 1, 0, 5),
		('page_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'page_profile', '', 1, 0, 11),
		('user_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'user_profile', '', 1, 0, 11),
		('group_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'group_profile', '', 1, 0, 11),
		('event_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'event_profile', '', 1, 0, 11),
		('classified_gutter_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'classified_gutter', '', 1, 0, 11),
		('blog_gutter_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'blog_gutter', '', 1, 0, 11),
		('page_profile_share', 'page', 'Share Page', 'Page_Plugin_Menus', '', 'page_profile', '', 1, 0, 5),
		('pageevent_past', 'pageevent', 'PAGEEVENT_PAST', 'Pageevent_Plugin_Menus', '', 'pageevent', '', 1, 0, 2),
		('pageevent_user', 'pageevent', 'PAGEEVENT_USER', 'Pageevent_Plugin_Menus', '', 'pageevent', '', 1, 0, 3),
		('pagealbum_all', 'pagealbum', 'All', 'Pagealbum_Plugin_Menus', '', 'pagealbum', '', 1, 0, 1),
		('pagealbum_mine', 'pagealbum', 'Mine', 'Pagealbum_Plugin_Menus', '', 'pagealbum', '', 1, 0, 2),
		('pageblog_all', 'pageblog', 'All', 'Pageblog_Plugin_Menus', '', 'pageblog', '', 1, 0, 1),
		('pageblog_mine', 'pageblog', 'Mine', 'Pageblog_Plugin_Menus', '', 'pageblog', '', 1, 0, 2),
		('core_sitemap_home', 'core', 'Home', '', '{\"route\":\"default\"}', 'core_sitemap', '', 1, 0, 1),
		('core_sitemap_user', 'user', 'Members', '', '{\"route\":\"user_general\",\"action\":\"browse\"}', 'core_sitemap', '', 1, 0, 2),
		('core_sitemap_event', 'event', 'Events', '', '{\"route\":\"event_general\"}', 'core_sitemap', '', 1, 0, 3),
		('core_sitemap_group', 'group', 'Groups', '', '{\"route\":\"group_general\"}', 'core_sitemap', '', 1, 0, 4),
		('core_sitemap_page', 'page', 'Pages', '', '{\"route\":\"page_browse\"}', 'core_sitemap', '', 1, 0, 5),
		('core_sitemap_album', 'album', 'Albums', '', '{\"route\":\"album_general\",\"action\":\"browse\"}', 'core_sitemap', '', 1, 0, 6),
		('core_sitemap_article', 'article', 'Articles', '', '{\"route\":\"article_browse\"}', 'core_sitemap', '', 1, 0, 7),
		('core_sitemap_blog', 'blog', 'Blogs', '', '{\"route\":\"blog_general\"}', 'core_sitemap', '', 1, 0, 8),
		('core_sitemap_classified', 'classified', 'Classifieds', '', '{\"route\":\"classified_general\"}', 'core_sitemap', '', 1, 0, 9),
		('core_sitemap_question', 'question', 'Questions & Answers', '', '{\"route\":\"default\",\"module\":\"question\"}', 'core_sitemap', '', 1, 0, 10);");

		$db->query("INSERT IGNORE INTO `engine4_mobile_menus` (`name`, `type`, `title`, `order`) VALUES
		('core_main', 'standard', 'Main Navigation Menu', 1),
		('core_mini', 'standard', 'Mini Navigation Menu', 2),
		('core_footer', 'standard', 'Footer Menu', 3),
		('core_sitemap', 'standard', 'Sitemap', 4),
		('user_home', 'standard', 'Member Home Quick Links Menu', 999),
		('user_profile', 'standard', 'Member Profile Options Menu', 999),
		('album_main', 'standard', 'Album Main Navigation Menu', 999),
		('blog_main', 'standard', 'Blog Main Navigation Menu', 999),
		('event_main', 'standard', 'Event Main Navigation Menu', 999),
		('event_profile', 'standard', 'Event Profile Options Menu', 999),
		('music_main', 'standard', 'Music Main Navigation Menu', 999),
		('poll_main', 'standard', 'Poll Main Navigation Menu', 999),
		('video_main', 'standard', 'Video Main Navigation Menu', 999),
		('group_main', 'standard', 'Group Main Navigation Menu', 999),
		('group_profile', 'standard', 'Group Profile Options Menu', 999),
		('classified_main', 'standard', 'Classified Main Navigation Menu', 999),
		('activity_main', 'standard', 'Activity Main Navigation Menu', 999);");

		$db->query("INSERT IGNORE INTO `engine4_mobile_pages` (`page_id`, `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `view_count`, `levels`, `module`) VALUES
		(1, 'header', 'Site Header', NULL, '', '', '', 0, 1, '', 0, '0', 'core'),
		(2, 'footer', 'Site Footer', NULL, '', '', '', 0, 1, '', 0, '0', 'core'),
		(3, 'core_index_index', 'Home Page', NULL, 'Home Page', 'This is the home page.', '', 0, 0, '', 0, '0', 'core'),
		(4, 'user_index_home', 'Member Home Page', NULL, 'Member Home Page', 'This is the home page for members.', '', 0, 0, '', 0, '0', 'core'),
		(5, 'user_profile_index', 'Member Profile', NULL, 'Member Profile', 'This is a member''s profile.', '', 0, 0, '', 0, '0', 'core'),
		(6, 'event_profile_index', 'Event Profile', NULL, 'Event Profile', 'This is the profile for an event.', '', 0, 0, '', 0, '0', 'event'),
		(7, 'mobile_index_index', 'Dashboard', NULL, 'Dashboard', 'This is the dashboard', '', 0, 0, 'default', 0, '0', 'core'),
		(8, 'group_profile_index', 'Group Profile', NULL, 'Group Profile', 'This is the profile for an group.', '', 0, 0, '', 0, '', 'group'),
		(9, 'page_index_view', 'Page Profile', NULL, 'Page Profile', 'This is the profile for an profile.', '', 0, 0, '', 0, '', 'page');");

		$db->query("
		INSERT IGNORE INTO `engine4_mobile_themes` (`name`, `title`, `description`, `active`) VALUES
		('default', 'Default Theme', '', 1),
		('midnight', 'Midnight', '', 0),
		('bamboo', 'Bamboo Theme', '', 0),
		('snowbot', 'Snowbot Theme', '', 0);");

		$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
		('mobile.show.rate-browse', '1'),
		('mobile.show.rate-widget', '1');");

		$db->query("
		INSERT INTO `engine4_mobile_content` (`content_id`, `page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
		(100, 1, 'container', 'main', NULL, 2, '[\"\"]', NULL),
		(200, 2, 'container', 'main', NULL, 2, '[\"\"]', NULL),
		(210, 2, 'widget', 'mobile.menu-footer', 200, 2, '{\"title\":\"\",\"name\":\"mobile.menu-footer\"}', NULL),
		(300, 3, 'container', 'main', NULL, 2, '[\"\"]', NULL),
		(312, 3, 'container', 'middle', 300, 6, '[\"\"]', NULL),
		(400, 4, 'container', 'main', NULL, 2, '[\"\"]', NULL),
		(412, 4, 'container', 'middle', 400, 6, '[\"\"]', NULL),
		(440, 4, 'widget', 'mobile.list-announcements', 412, 3, '{\"title\":\"Announcements\"}', NULL),
		(441, 4, 'widget', 'mobile.activity-feed', 412, 4, '{\"title\":\"\",\"limit\":\"15\",\"name\":\"mobile.feed\"}', NULL),
		(500, 5, 'container', 'main', NULL, 2, '[\"\"]', NULL),
		(511, 5, 'container', 'middle', 500, 6, '[\"\"]', NULL),
		(549, 6, 'container', 'main', NULL, 2, '[\"\"]', NULL),
		(550, 6, 'container', 'middle', 549, 6, '[\"\"]', NULL),
		(552, 6, 'widget', 'mobile.container-tabs', 550, 6, '{\"max\":\"6\"}', NULL),
		(556, 6, 'widget', 'mobile.event-profile-info', 552, 8, '{\"title\":\"Event Details\"}', NULL),
		(559, 6, 'widget', 'mobile.event-profile-members', 552, 9, '{\"title\":\"Guests\",\"titleCount\":\"true\"}', NULL),
		(560, 6, 'widget', 'mobile.event-profile-photos', 552, 10, '{\"title\":\"Photos\",\"titleCount\":\"true\"}', NULL),
		(641, 1, 'widget', 'mobile.menu-main', 100, 3, '{\"count\":\"8\",\"title\":\"\",\"name\":\"mobile.menu-main\"}', NULL),
		(659, 5, 'widget', 'mobile.activity-feed', 663, 6, '{\"title\":\"What''s New\",\"limit\":\"10\",\"name\":\"mobile.activity-feed\"}', NULL),
		(663, 5, 'widget', 'mobile.container-tabs', 511, 5, '{\"max\":\"6\"}', NULL),
		(664, 5, 'widget', 'mobile.album-profile-albums', 663, 12, '{\"title\":\"Albums\",\"titleCount\":\"true\"}', NULL),
		(666, 1, 'widget', 'mobile.main-header', 100, 2, NULL, NULL),
		(670, 7, 'container', 'main', NULL, 2, NULL, NULL),
		(671, 7, 'container', 'middle', 670, 6, NULL, NULL),
		(673, 7, 'widget', 'mobile.site-map', 671, 3, '{\"type\":\"list\",\"title\":\"\",\"name\":\"mobile.site-map\"}', NULL),
		(680, 5, 'widget', 'mobile.blog-profile-blogs', 663, 14, '{\"title\":\"Blogs\",\"titleCount\":\"true\"}', NULL),
		(681, 5, 'widget', 'mobile.event-profile-events', 663, 9, '{\"title\":\"Events\",\"titleCount\":\"true\"}', NULL),
		(684, 6, 'widget', 'mobile.activity-feed', 552, 7, '{\"title\":\"What''s New\",\"limit\":\"15\"}', NULL),
		(686, 8, 'container', 'main', NULL, 2, NULL, NULL),
		(689, 8, 'container', 'middle', 686, 6, '[\"\"]', NULL),
		(691, 8, 'widget', 'mobile.container-tabs', 689, 5, '{\"max\":\"6\"}', NULL),
		(702, 8, 'widget', 'mobile.activity-feed', 691, 6, '{\"title\":\"What''s New\",\"limit\":\"15\"}', NULL),
		(706, 8, 'widget', 'mobile.group-profile-info', 691, 7, '{\"title\":\"Info\",\"name\":\"mobile.group-profile-info\"}', NULL),
		(707, 8, 'widget', 'mobile.group-profile-members', 691, 8, '{\"title\":\"Members\",\"name\":\"mobile.group-profile-members\",\"titleCount\":\"true\",\"itemCountPerPage\":\"10\"}', NULL),
		(708, 8, 'widget', 'mobile.group-profile-photos', 691, 9, '{\"title\":\"Photos\",\"itemCountPerPage\":\"10\",\"titleCount\":\"true\"}', NULL),
		(709, 8, 'widget', 'mobile.group-profile-events', 691, 10, '{\"title\":\"Events\",\"titleCount\":\"true\",\"itemCountPerPage\":\"10\"}', NULL),
		(710, 5, 'widget', 'mobile.classified-profile-classifieds', 663, 15, '{\"title\":\"Classifieds\",\"titleCount\":\"true\"}', NULL),
		(720, 5, 'widget', 'mobile.group-profile-groups', 663, 10, '{\"title\":\"Groups\",\"titleCount\":\"true\"}', NULL),
		(724, 3, 'widget', 'mobile.user-login-or-signup', 312, 3, NULL, NULL),
		(730, 5, 'widget', 'mobile.user-profile-friends', 663, 8, '{\"title\":\"Friends\",\"titleCount\":\"true\"}', NULL),
		(735, 2, 'widget', 'mobile.mode-switcher', 200, 3, '{\"standard\":\"Standard Site\",\"mobile\":\"Mobile Site\"}', NULL),
		(736, 4, 'widget', 'mobile.autorecommendations', 412, 5, '{\"title\":\"Recommendations\"}', NULL),
		(738, 5, 'widget', 'mobile.like-box', 663, 18, '{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}', NULL),
		(739, 5, 'widget', 'mobile.like-profile-likes', 663, 17, '{\"title\":\"like_Likes\",\"titleCount\":\"true\"}', NULL),
		(741, 5, 'widget', 'mobile.user-profile-widgets', 511, 3, '{\"left\":[\"mobile.user-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.user-profile-options\"],\"title\":\"\",\"name\":\"mobile.user-profile-widgets\"}', NULL),
		(745, 6, 'widget', 'mobile.event-profile-rsvp', 550, 4, NULL, NULL),
		(748, 6, 'widget', 'mobile.like-box', 552, 11, '{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}', NULL),
		(750, 8, 'widget', 'mobile.like-box', 691, 11, '{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}', NULL),
		(758, 9, 'container', 'main', NULL, 2, NULL, NULL),
		(759, 9, 'container', 'middle', 758, 6, NULL, NULL),
		(782, 9, 'widget', 'mobile.container-tabs', 759, 6, '{\"max\":\"6\"}', NULL),
		(783, 9, 'widget', 'mobile.page-feed', 782, 7, '{\"title\":\"Updates\",\"titleCount\":\"false\"}', NULL),
		(788, 9, 'widget', 'mobile.page-profile-fields', 782, 8, '{\"title\":\"Info\",\"titleCount\":\"true\"}', NULL),
		(791, 9, 'widget', 'mobile.page-profile-note', 759, 5, '{\"title\":\"Page Note\",\"titleCount\":\"false\"}', NULL),
		(794, 9, 'widget', 'mobile.page-profile-admins', 782, 9, '{\"title\":\"Team\",\"titleCount\":\"false\"}', NULL),
		(797, 9, 'widget', 'mobile.rate-widget', 759, 4, '{\"title\":\"Rate This\",\"titleCount\":\"true\"}', NULL),
		(805, 9, 'widget', 'mobile.page-profile-blog', 782, 11, '{\"title\":\"Blogs\",\"titleCount\":\"true\"}', NULL),
		(806, 9, 'widget', 'mobile.page-profile-discussion', 782, 12, '{\"title\":\"Discussions\",\"titleCount\":\"true\"}', NULL),
		(807, 9, 'widget', 'mobile.page-profile-event', 782, 13, '{\"title\":\"Events\",\"titleCount\":\"true\"}', NULL),
		(808, 9, 'widget', 'mobile.page-review', 782, 14, '{\"title\":\"Reviews\",\"titleCount\":\"true\"}', NULL),
		(811, 9, 'widget', 'mobile.page-profile-album', 782, 10, '{\"title\":\"Albums\",\"titleCount\":\"true\",\"url_params\":{\"route\":\"default\",\"module\":\"pagealbum\",\"controller\":\"index\",\"action\":\"index\"}}', NULL),
		(813, 9, 'widget', 'mobile.page-profile-widgets', 759, 3, '{\"left\":[\"mobile.page-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.page-profile-options\"],\"title\":\"\",\"name\":\"mobile.page-profile-widgets\"}', NULL),
		(814, 9, 'widget', 'mobile.like-box', 782, 15, '{\"title\":\"like_Like Club\",\"titleCount\":\"true\"}', NULL),
		(817, 5, 'widget', 'mobile.question-profile-questions', 663, 16, '{\"title\":\"Q&A\",\"titleCount\":\"false\"}', NULL),
		(818, 5, 'widget', 'mobile.article-profile-articles', 663, 13, '{\"title\":\"Articles\",\"titleCount\":\"true\"}', NULL),
		(819, 5, 'widget', 'mobile.page-profile-pages', 663, 11, '{\"title\":\"Pages\",\"titleCount\":\"true\"}', NULL),
		(820, 5, 'widget', 'mobile.rate-widget', 511, 4, '{\"title\":\"Rate This\",\"titleCount\":\"true\"}', NULL),
		(823, 5, 'widget', 'mobile.user-profile-info', 663, 7, '{\"title\":\"Profile Info\",\"titleCount\":\"true\"}', NULL),
		(825, 8, 'widget', 'mobile.rate-widget', 689, 4, '{\"title\":\"Rate This\",\"titleCount\":\"true\"}', NULL),
		(828, 6, 'widget', 'mobile.event-profile-widgets', 550, 3, '{\"left\":[\"mobile.event-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.event-profile-options\"]}', NULL),
		(829, 8, 'widget', 'mobile.group-profile-widgets', 689, 3, '{\"left\":[\"mobile.group-profile-photo\"],\"right\":[\"mobile.like-status\",\"mobile.group-profile-options\"]}', NULL),
		(830, 6, 'widget', 'mobile.rate-widget', 550, 5, '{\"title\":\"Rate This\",\"titleCount\":\"true\"}', NULL);");

		$db->query("INSERT IGNORE INTO `engine4_core_content` ( `page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
		(2, 'widget', 'mobile.mode-switcher', 200, 999, '{\"standard\":\"Standard Site\",\"mobile\":\"Mobile Site\"}', NULL);");

		$db->query("DELETE FROM `engine4_core_menuitems` WHERE `module`= 'mobile';");

		$db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
		('core_admin_main_plugins_mobile', 'mobile', 'HE - Mobile', NULL, '{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"content\",\"action\":\"index\"}', 'core_admin_main_plugins', '', 1, 0, 888),
		('mobile_admin_main_content', 'mobile', 'Layout Editor', '', '{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"content\",\"action\":\"index\"}', 'mobile_admin_main', '', 1, 0, 1),
		('mobile_admin_main_themes', 'mobile', 'Theme Editor', NULL, '{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"themes\",\"action\":\"index\"}', 'mobile_admin_main', NULL, 1, 0, 2),
		('mobile_admin_main_menus', 'mobile', 'MOBILE_MENU_EDITOR', NULL, '{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"menus\",\"action\":\"index\"}', 'mobile_admin_main', NULL, 1, 0, 3),
		('mobile_admin_main_plugin_settings', 'mobile', 'MOBILE_PLUGIN_SETTINGS', 'Mobile_Plugin_Menus', '{\"route\":\"admin_default\",\"module\":\"mobile\",\"controller\":\"plugin-settings\",\"action\":\"index\"}', 'mobile_admin_main', NULL, 1, 0, 4);");
		
		$db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
    }
    else { //$operation = upgrade|refresh
		$db = Engine_Db_Table::getDefaultAdapter();
		
		$db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
    }
  }
}