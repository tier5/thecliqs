<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: install.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvancedalbum_Installer extends Engine_Package_Installer_Module {

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
        $module_name = "advancedalbum";
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

            // Add Field he_featured to engine4_album_albums 
            $field_found = false;
            foreach ($db->query("DESCRIBE engine4_album_albums")->fetchAll() as $field) {
                if (!empty($field) && $field['Field'] == 'he_featured') {
                    $field_found = true;
                }
            }
            if (!$field_found) {
                $db->query("ALTER TABLE `engine4_album_albums` ADD `he_featured` INT( 11 ) NOT NULL DEFAULT '0';");
            }

            // Add Field he_featured to engine4_album_photos 
            $field_found = false;
            foreach ($db->query("DESCRIBE engine4_album_photos")->fetchAll() as $field) {
                if (!empty($field) && $field['Field'] == 'he_featured') {
                    $field_found = true;
                }
            }
            if (!$field_found) {
                $db->query("ALTER TABLE `engine4_album_photos` ADD `he_featured` INT( 11 ) NOT NULL DEFAULT '0';");
            }

            // Replace to our Photos widget 
            $page = $db->query("SELECT * FROM engine4_core_pages WHERE name = 'timeline_profile_index'")->fetch();
            if ($page) {
                $db->query("UPDATE engine4_core_content SET name = 'headvancedalbum.profile-albums', params = '{\"title\":\"Photos\",\"titleCount\":true}' WHERE page_id = {$page['page_id']} AND name = 'album.profile-albums'")->fetchAll();
            }

            $query = <<<CONTENT
INSERT IGNORE INTO  `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('album_main_photos', 'headvancedalbum', 'HEADVANCEDALBUM_Browse Photos', 'Album_Plugin_Menus::canViewAlbums', '{"route":"headvancedalbum_photos_browse", "action": "index"}', 'album_main', NULL, 1, 0, 2), 
('headvancedalbum_browse', 'headvancedalbum', 'HEADVANCEDALBUM_Albums', 'Album_Plugin_Menus::canViewAlbums', '{"route":"headvancedalbum_albums_browse", "action": "browse"}', 'headvancedalbum_main', NULL, 1, 0, 1),
('headvancedalbum_photos', 'headvancedalbum', 'HEADVANCEDALBUM_Photos', 'Album_Plugin_Menus::canViewAlbums', '{"route":"headvancedalbum_photos_browse", "action": "index"}', 'headvancedalbum_main', NULL, 1, 0, 2),
('headvancedalbum_manage', 'headvancedalbum', 'HEADVANCEDALBUM_My Albums', 'Album_Plugin_Menus::canCreateAlbums', '{"route":"headvancedalbum_mine_albums", "action": "manage"}', 'headvancedalbum_main', NULL, 1, 0, 3),
('headvancedalbum_add', 'headvancedalbum', 'HEADVANCEDALBUM_Add New Photos', 'Album_Plugin_Menus::canCreateAlbums', '{"route":"headvancedalbum_add_photos", "action": "upload"}', 'headvancedalbum_main', NULL, 1, 0, 4),
('core_admin_main_plugins_headvancedalbum', 'headvancedalbum', 'HE - Advanced Photo Albums', NULL, '{"route": "admin_default", "module": "headvancedalbum", "controller": "manage"}', 'core_admin_main_plugins', NULL, 1, 0, 889),
('headvancedalbum_admin_main_manage', 'headvancedalbum', 'HEADVANCEDALBUM_Manage Albums', NULL, '{"route":"admin_default","module":"headvancedalbum","controller":"manage"}', 'headvancedalbum_admin_main', NULL, 1, 0, 1),
('headvancedalbum_admin_main_settings', 'headvancedalbum', 'HEADVANCEDALBUM_Global Settings', NULL, '{"route":"admin_default","module":"headvancedalbum","controller":"settings"}', 'headvancedalbum_admin_main', NULL, 1, 0, 2),
('headvancedalbum_admin_main_photoviewer', 'headvancedalbum', 'Photo Viewer Settings', NULL, '{"route":"admin_default","module":"photoviewer","controller":"index"}', 'headvancedalbum_admin_main', NULL, 1, 0, 3);
CONTENT;

            $db->query($query);

            $query = <<<CONTENT
INSERT IGNORE INTO  `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES 
('headvancedalbum_main', 'standard', 'HE Advanced Photo Albums Main Navigation Menu', 999);
CONTENT;

            $db->query($query);

            $query = <<<CONTENT
INSERT IGNORE INTO  `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES 
('headvancedalbum', 'Advanced Photo Albums', '', '4.2.0', 1, 'extra'); 
CONTENT;

            $db->query($query);

            $db->query("INSERT IGNORE INTO  `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('headvancedalbum_index_index', 'Advanced Album Photos Page', NULL, 'Photos', 'This page displays a list of photos', NULL, NULL, NULL, NULL, NULL, 'no-subject', NULL)");

            $page_id = $db->lastInsertId();

            if ($page_id) {
                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'top', 'NULL', '1', '[\"[]\"]', NULL)");

                $parent_content_id = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[\"[]\"]', NULL)");

                $parent_content_id_0 = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'headvancedalbum.navigation-tabs', '$parent_content_id_0', '3', '[\"[]\"]', NULL)");

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'main', 'NULL', '2', '[\"[]\"]', NULL)");

                $parent_content_id = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[\"[]\"]', NULL)");

                $parent_content_id_0 = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'core.content', '$parent_content_id_0', '7', '[\"[]\"]', NULL)");

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'headvancedalbum.featured-albums', '$parent_content_id_0', '6', '{\"title\":\"Featured Albums\"}', NULL)");
            }

            $db->query("INSERT IGNORE INTO  `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('headvancedalbum_index_browse', 'Advanced Album Browse Page', NULL, 'Albums', 'This page displays a list of albums', NULL, NULL, NULL, NULL, NULL, 'no-subject', NULL)");

            $page_id = $db->lastInsertId();

            if ($page_id) {
                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'top', 'NULL', '1', '[\"[]\"]', NULL)");

                $parent_content_id = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[\"[]\"]', NULL)");

                $parent_content_id_0 = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'headvancedalbum.navigation-tabs', '$parent_content_id_0', '3', '[\"[]\"]', NULL)");

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'main', 'NULL', '2', '[\"[]\"]', NULL)");

                $parent_content_id = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[\"[]\"]', NULL)");

                $parent_content_id_0 = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'headvancedalbum.featured-photos', '$parent_content_id_0', '6', '{\"title\":\"Featured Photos\"}', NULL)");

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'core.content', '$parent_content_id_0', '7', '[]', NULL)");
            }

            $db->query("INSERT IGNORE INTO  `engine4_core_pages` (`name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`) VALUES ('headvancedalbum_index_view', 'Advanced Album View Page', NULL, 'Album', 'This page displays a list of photos of an album ', NULL, NULL, NULL, NULL, NULL, 'no-subject', NULL)");

            $page_id = $db->lastInsertId();

            if ($page_id) {
                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'main', 'NULL', '2', '[\"[]\"]', NULL)");

                $parent_content_id = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'middle', '$parent_content_id', '6', '[\"[]\"]', NULL)");

                $parent_content_id_0 = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'core.content', '$parent_content_id_0', '3', '[\"[]\"]', NULL)");

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'core.comments', '$parent_content_id_0', '4', '{\"title\":\"Comments\"}', NULL)");

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'container', 'right', '$parent_content_id', '5', '[\"[]\"]', NULL)");

                $parent_content_id_0 = $db->lastInsertId();

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'headvancedalbum.friends-albums', '$parent_content_id_0', '6', '{\"title\":\"Friends\' Albums\"}', NULL)");

                $db->query("INSERT IGNORE INTO  `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES ('$page_id', 'widget', 'headvancedalbum.friends-photos', '$parent_content_id_0', '7', '{\"title\":\"Friends\' Photos\"}', NULL)");
            }

            $db->query("INSERT INTO `engine4_hecore_modules` (`name`, `version`, `key`, `installed`, `modified_stamp`) VALUES ('" . $package->getName() . "', '" . $package->getVersion() . "', '" . $licenseKey . "', 1, " . time() . ")");
        } else { //$operation = upgrade|refresh
            $db = Engine_Db_Table::getDefaultAdapter();

            $db->query("UPDATE `engine4_hecore_modules` SET `version` = '" . $package->getVersion() . "', modified_stamp = " . time() . " WHERE `name` = '" . $package->getName() . "';");
        }
    }

}