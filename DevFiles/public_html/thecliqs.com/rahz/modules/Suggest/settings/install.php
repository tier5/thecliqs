<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Suggest_Installer extends Engine_Package_Installer_Module {

    public function onPreInstall() {
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

        if ($module && isset($module['installed']) && $module['installed'] && isset($module['version']) && $module['version'] == $this->_targetVersion && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
        ) {
            return;
        }

        if ($operation == 'install') {

            if ($module && $module['installed']) {
                return;
            }

            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_suggest_rejected` ( 
		  `rejected_id` int(11) unsigned NOT NULL auto_increment, 
		  `object_type` varchar(50) default NULL, 
		  `object_id` int(11) unsigned NOT NULL default '0', 
		  `user_id` int(11) unsigned NOT NULL default '0', 
		  `date` datetime NOT NULL, 
		  PRIMARY KEY  (`rejected_id`) 
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_suggest_suggests` ( 
		  `suggest_id` int(10) unsigned NOT NULL auto_increment, 
		  `from_id` int(10) unsigned NOT NULL default '0', 
		  `to_id` int(10) unsigned NOT NULL default '0', 
		  `object_type` varchar(100) default NULL, 
		  `object_id` int(10) unsigned NOT NULL default '0', 
		  `suggest_date` datetime default NULL, 
		  PRIMARY KEY  (`suggest_id`) 
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_suggest_profile_photos` ( 
			`profilephoto_id` INT(11) NOT NULL AUTO_INCREMENT, 
			`from_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`to_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			`file_id` INT(11) UNSIGNED NOT NULL DEFAULT '0', 
			PRIMARY KEY (`profilephoto_id`) 
		)ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $db->query("CREATE TABLE IF NOT EXISTS `engine4_suggest_recommendations` ( 
			`recommendation_id` INT(10) NOT NULL AUTO_INCREMENT, 
			`object_type` VARCHAR(50) NULL DEFAULT NULL, 
			`object_id` INT(11) NOT NULL DEFAULT '0', 
			`date` DATETIME NOT NULL, 
			PRIMARY KEY (`recommendation_id`) 
		)COLLATE='utf8_general_ci' ENGINE=InnoDB ROW_FORMAT=DEFAULT;");

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES 
		('core_admin_main_plugins_suggest', 'suggest', 'suggest_menu_Suggest', '', '{\"route\":\"admin_default\",\"module\":\"suggest\",\"controller\":\"global\",\"action\":\"index\"}', 'core_admin_main_plugins', '', 888),

		('suggest_admin_main_global', 'suggest', 'suggest_Global Settings', '', '{\"route\":\"admin_default\",\"module\":\"suggest\",\"controller\":\"global\",\"action\":\"index\"}', 'suggest_admin_main', '', 1), 
		('suggest_admin_main_recs', 'suggest', 'suggest_Recommendations', '', '{\"route\":\"admin_default\",\"module\":\"suggest\",\"controller\":\"recommendations\",\"action\":\"index\"}', 'suggest_admin_main', '', 2),

		('page_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'page_profile', '', 11), 
		('user_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'user_profile', '', 11), 
		('group_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'group_profile', '', 11), 
		('event_profile_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'event_profile', '', 11), 
		('classified_gutter_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'classified_gutter', '', 11), 
		('blog_gutter_suggest', 'suggest', 'Suggest To Friends', 'Suggest_Plugin_Menus', '', 'blog_gutter', '', 11);");

            $db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` ,`module` ,`body` ,`is_request` ,`handler`) VALUES 
		('suggest',  'suggest',  '{item:\$subject} has suggested to you a new {item:\$object:friend}.',  '1',  'suggest.handler.request'), 
		('suggest_suggest_profile_photo',  'suggest',  '{item:\$subject} has suggested to you a {item:\$object:profilephoto}.',  '1',  'suggest.handler.request'), 
		('suggest_video',  'video',  '{item:\$subject} has suggested to you a {item:\$object:video}.',  '1',  'suggest.handler.request'), 
		('suggest_quiz',  'quiz',  '{item:\$subject} has suggested to you a {item:\$object:quiz}.',  '1',  'suggest.handler.request'), 
		('suggest_classified',  'classified',  '{item:\$subject} has suggested to you a {item:\$object:classified}.',  '1',  'suggest.handler.request'), 
		('suggest_blog',  'blog',  '{item:\$subject} has suggested to you a {item:\$object:blog}.',  '1',  'suggest.handler.request'), 
		('suggest_poll',  'poll',  '{item:\$subject} has suggested to you a {item:\$object:poll}.',  '1',  'suggest.handler.request'), 
		('suggest_album_photo',  'album',  '{item:\$subject} has suggested to you a {item:\$object:photo}.',  '1',  'suggest.handler.request'), 
		('suggest_music_playlist',  'music',  '{item:\$subject} has suggested to you a {item:\$object:playlist}.',  '1',  'suggest.handler.request'), 
		('suggest_album',  'album',  '{item:\$subject} has suggested to you a {item:\$object:album}.',  '1',  'suggest.handler.request'), 
		('suggest_group',  'group',  '{item:\$subject} has suggested to you a {item:\$object:group}.',  '1',  'suggest.handler.request'), 
		('suggest_event',  'event',  '{item:\$subject} has suggested to you a {item:\$object:event}.',  '1',  'suggest.handler.request'), 
		('suggest_page',  'page',  '{item:\$subject} has suggested to you a {item:\$object:page}.',  '1',  'suggest.handler.request'), 
		('suggest_article',  'article',  '{item:\$subject} has suggested to you a {item:\$object:article}.',  '1',  'suggest.handler.request'), 
		('suggest_question',  'question',  '{item:\$subject} has suggested to you a {item:\$object:question}.',  '1',  'suggest.handler.request'), 
		('suggest_user',  'user',  '{item:\$subject} has suggested to you a {item:\$object:user}.',  '1',  'suggest.handler.request'), 
		('suggest_store_product',  'store',  '{item:\$subject} has suggested to you a {item:\$object:product}.',  '1',  'suggest.handler.request'), 
		('suggest_playlist', 'pagemusic', '{item:\$subject} has suggested to you a {item:\$object:playlist}', 1, 'suggest.handler.request'), 
		('suggest_pageblog', 'pageblog', '{item:\$subject} has suggested to you a {item:\$object:pageblog}', 1, 'suggest.handler.request'), 
		('suggest_pageevent', 'pageevent', '{item:\$subject} has suggested to you a {item:\$object:pageevent}', 1, 'suggest.handler.request'), 
		('suggest_pagevideo', 'pagevideo', '{item:\$subject} has suggested to you a {item:\$object:pagevideo}', 1, 'suggest.handler.request'), 
		('suggest_pagedocument', 'pagedocument', '{item:\$subject} has suggested to you a {item:\$object:pagedocument}', 1, 'suggest.handler.request'), 
		('suggest_albums', 'pagealbum', '{item:\$subject} has suggested to you a {item:\$object:pagealbums}', 1, 'suggest.handler.request'), 
		('suggest_disscussions', 'page_disscussions', '{item:\$subject} has suggested to you a {item:\$object:page_disscussions}', 1, 'suggest.handler.request'), 
		('suggest_ynmusic_album', 'ynmusic', '{item:\$subject} has suggested to you a {item:\$object:ynmusic_album}', 1, 'suggest.handler.request'), 
		('suggest_avp_video', 'avp', '{item:\$subject} has suggested to you a {item:\$object:avp_video}', 1, 'suggest.handler.request'), 
		('suggest_artarticle', 'advancedarticles', '{item:\$subject} has suggested to you a {item:\$object:artarticle}', 1, 'suggest.handler.request'), 
		('suggest_list_listing', 'list', '{item:\$subject} has suggested to you a {item:\$object:list_listing}', 1, 'suggest.handler.request'), 
		('suggest_document', 'document', '{item:\$subject} has suggested to you a {item:\$object:document}', 1, 'suggest.handler.request');");

            $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES 
		('notify_suggest', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_suggest_profile_photo', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_video', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_classified', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_quiz', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_classified', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_blog', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_poll', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_album_photo', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_music_playlist', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_album', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_group', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_event', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_article', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_question', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_store_product', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");

            $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
		('suggest.widget.item.count', '6'), 
		('suggest.event.accept', '1'), 
		('suggest.event.join', '1'), 
		('suggest.friend.add', '1'), 
		('suggest.friend.confirm', '1'), 
		('suggest.group.accept', '1'), 
		('suggest.group.join', '1'), 
		('suggest.link.album', '1'), 
		('suggest.link.photo', '1'), 
		('suggest.link.blog', '1'), 
		('suggest.link.classified', '1'), 
		('suggest.link.event', '1'), 
		('suggest.link.group', '1'), 
		('suggest.link.music.playlist', '1'), 
		('suggest.link.page', '1'), 
		('suggest.link.poll', '1'), 
		('suggest.link.quiz', '1'), 
		('suggest.link.video', '1'), 
		('suggest.link.article', '1'), 
		('suggest.link.question', '1'), 
		('suggest.link.user', '1'), 
		('suggest.link.store.product', '1'), 
		('suggest.link.pageblog', 1), 
		('suggest.link.pagediscussion_pagepost', 1), 
		('suggest.link.pagevideo', 1), 
		('suggest.link.pageevent', 1), 
		('suggest.link.pagedocument', 1), 
		('suggest.link.playlist', 1), 
		('suggest.link.page.album', 1), 
		('suggest.link.avp.video', 1), 
		('suggest.link.artarticle', 1), 
		('suggest.link.document', 1), 
		('suggest.link.ynmusic.album', 1), 
		('suggest.link.list.listing', 1), 
		('suggest.popup.create.album', '1'), 
		('suggest.popup.create.blog', '1'), 
		('suggest.popup.create.classified', '1'), 
		('suggest.popup.create.event', '1'), 
		('suggest.popup.create.group', '1'), 
		('suggest.popup.create.music.playlist', '1'), 
		('suggest.popup.create.page', '1'), 
		('suggest.popup.create.poll', '1'), 
		('suggest.popup.create.quiz', '1'), 
		('suggest.popup.create.video', '1'), 
		('suggest.popup.create.article', '1'), 
		('suggest.popup.create.question', '1'), 
		('suggest.mix.album', '1'), 
		('suggest.mix.blog', '1'), 
		('suggest.mix.classified', '1'), 
		('suggest.mix.event', '1'), 
		('suggest.mix.group', '1'), 
		('suggest.mix.music.playlist', '1'), 
		('suggest.mix.page', '1'), 
		('suggest.mix.poll', '1'), 
		('suggest.mix.quiz', '1'), 
		('suggest.mix.video', '1'), 
		('suggest.mix.article', '1'), 
		('suggest.mix.question', '1'), 
		('suggest.mix.store.product', '1'), 
		('suggest.mix.user', '1'), 
		('suggest.mix.avp.video', 1), 
		('suggest.mix.artarticle', 1), 
		('suggest.mix.list.listing', 1), 
		('suggest.mix.document', 1), 
		('suggest.mix.pagediscussion.pagepost', 1), 
		('suggest.mix.pagevideo', 1), 
		('suggest.mix.pagealbum', 1), 
		('suggest.mix.pageblog', 1), 
		('suggest.mix.pagemusic', 1), 
		('suggest.mix.pagedocument', 1), 
		('suggest.mix.list.listing', 1), 
		('suggest.mix.ynmusic.album', 1);");

            $facebook_id = $db->fetchOne("SELECT `value` FROM `engine4_core_settings` WHERE `name` = 'core.facebook.appid'");

            if ($facebook_id) {
                $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES('suggest.facebook.app.id', '" . $facebook_id . "');");
            }

            $db->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` ,`module` ,`body` ,`is_request` ,`handler`) VALUES 
		('suggest_job', 'job', '{item:\$subject} has suggested to you a {item:\$object:job}', 1, 'suggest.handler.request');");

            $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES 
		('notify_suggest_playlist', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_pageblog', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_pageevent', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_pagevideo', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_pagedocument', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_pagealbum', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_disscussions', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_ynmusic_album', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_avp_video', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_artarticle', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_list_listing', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_document', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), 
		('notify_suggest_job', 'suggest', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");

            $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES 
		('suggest.link.job', '1'), 
		('suggest.mix.job', 1);");

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } else { //$operation = upgrade|refresh
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}