<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
$db = Engine_Db_Table::getDefaultAdapter();
$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
VALUES 
("spectacular_admin_settings_images", "spectacular", "Images", "", \'{"route":"admin_default","module":"spectacular","controller":"settings","action":"images"}\', "spectacular_admin_main", "", 1, 0, 3),
("spectacular_admin_theme_customization", "spectacular", "Theme Customization", "", \'{"route":"admin_default","module":"spectacular","controller":"customization"}\', "spectacular_admin_main", "", 1, 0, 4),
("spectacular_admin_settings_footer_menu", "spectacular", "Footer Menu", "", \'{"route":"admin_default","module":"spectacular","controller":"settings","action":"footer-menu"}\', "spectacular_admin_main", "", 1, 0, 5),
("spectacular_admin_settings_banners", "spectacular", "Banners", "", \'{"route":"admin_default","module":"spectacular","controller":"settings","action":"banners"}\', "spectacular_admin_main", "", 1, 0, 6),
("spectacular_admin_layout_index", "spectacular", "Layout Settings", "", \'{"route":"admin_default","module":"spectacular","controller":"layout", "action": "index"}\', "spectacular_admin_main", "", 1, 0, 2)
');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
("spectacular_footer_first_column", "spectacular", "First Column", NULL, \'{"uri":"javascript:void(0)"}\', "spectacular_footer", NULL, "1", "1", "1"),
("spectacular_footer_first_column_1", "spectacular", "First Column - 1", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "2"), 
("spectacular_footer_first_column_2", "spectacular", "First Column - 2", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "3"), 
("spectacular_footer_first_column_3", "spectacular", "First Column - 3", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "4"), 

("spectacular_footer_second_column", "spectacular", "Second Column", NULL , \'{"uri":"javascript:void(0)"}\', "spectacular_footer", NULL , "1", "1", "10"), 
("spectacular_footer_second_column_1", "spectacular", "Second Column - 1", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "11"), 
("spectacular_footer_second_column_2", "spectacular", "Second Column - 2", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "12"), 
("spectacular_footer_second_column_3", "spectacular", "Second Column - 3", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "13"), 


("spectacular_footer_third_column", "spectacular", "Third Column", NULL , \'{"uri":"javascript:void(0)"}\', "spectacular_footer", NULL , "1", "1", "20"), 
("spectacular_footer_third_column_1", "spectacular", "Third Column - 1", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "21"), 
("spectacular_footer_third_column_2", "spectacular", "Third Column - 2", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "22"), 
("spectacular_footer_third_column_3", "spectacular", "Third Column - 3", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "23"),

("spectacular_footer_fourth_column", "spectacular", "Fourth Column", NULL , \'{"uri":"javascript:void(0)"}\', "spectacular_footer", NULL , "1", "1", "24"), 
("spectacular_footer_fourth_column_1", "spectacular", "Fourth Column - 1", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "25"), 
("spectacular_footer_fourth_column_2", "spectacular", "Fourth Column - 2", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "26"), 
("spectacular_footer_fourth_column_3", "spectacular", "Fourth Column - 3", "" , \'{"route":"default"}\', "spectacular_footer", NULL , "1", "1", "27")
;');

$db->query('INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES 
("spectacular_footer", "standard", "Responsive Spectacular Theme - Footer Menu", "1");');

Engine_Api::_()->spectacular()->setDefaultLayout($_POST);

$global_directory_name = APPLICATION_PATH . '/public/seaocore_themes';
$global_settings_file = $global_directory_name . '/spectacularThemeConstants.css';
$is_file_exist = @file_exists($global_settings_file);
if (empty($is_file_exist)) {
    if (!is_dir($global_directory_name)) {
        @mkdir($global_directory_name, 0777);
    }
    @chmod($global_directory_name, 0777);

    $fh = @fopen($global_settings_file, 'w');
    @fwrite($fh, '');
    @fclose($fh);

    @chmod($global_settings_file, 0777);
}
$tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
$tableNameContentName = $tableNameContent->info('name');

$db = Engine_Db_Table::getDefaultAdapter();
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitecontentcoverphoto')
        ->where('enabled = ?', 1);
$is_sitecontentcoverphoto_object = $select->query()->fetchObject();
if ($is_sitecontentcoverphoto_object) {

    $select = new Zend_Db_Select($db);
    $page_id = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'siteevent_index_view')
            ->query()
            ->fetchColumn();
    if ($page_id) {
        $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteGuest","updateInfoButton","inviteRsvpButton","optionsButton","venue","startDate","endDate","location","hostName", "addToMyCalendar","shareOptions"],"profile_like_button":"0","columnHeight":"400","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","contentFullWidth":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.add-to-my-calendar-siteevent" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.list-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

        $db->query("UPDATE `engine4_core_menuitems` SET  `label` =  'Save to Diary' WHERE  `engine4_core_menuitems`.`name` = 'siteevent_gutter_diary' LIMIT 1 ;");

        $select = new Zend_Db_Select($db);
        $content_id = $select
                ->from('engine4_core_content', 'content_id')
                ->where('name = ?', 'top')
                ->where('page_id = ?', $page_id)
                ->query()
                ->fetchColumn();

        if ($content_id) {
            $select = new Zend_Db_Select($db);
            $content_id = $select
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'middle')
                    ->where('parent_content_id = ?', $content_id)
                    ->where('page_id = ?', $page_id)
                    ->query()
                    ->fetchColumn();
            if ($content_id) {
                $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
            }
        }
    }


    $select = new Zend_Db_Select($db);
    $page_id = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'sitealbum_index_view')
            ->query()
            ->fetchColumn();
    if ($page_id) {
        $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"album","showContent_0":"","showContent_album":["mainPhoto","title","owner","description","totalPhotos","viewCount","likeCount","commentCount","location","CategoryLink","updateDate","optionsButton","shareOptions"],"showContent_sitebusiness_business":"","showContent_siteevent_event":"","showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitestore_store":"","showContent_sitestoreproduct_product":"","profile_like_button":"1","columnHeight":"235","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "sitealbum.profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
    }

    if (!$coreSettings->getSetting('sitepage.layoutcreate', 0)) {
        $select = new Zend_Db_Select($db);
        $page_id = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->query()
                ->fetchColumn();
        if ($page_id) {

            $top_content_id = $tableNameContent->select()
                    ->from($tableNameContentName, 'content_id')
                    ->where('page_id =?', $page_id)
                    ->where('name =?', 'top')
                    ->query()
                    ->fetchColumn();
            if (empty($top_content_id)) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $page_id,
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => ''
                ));
                $content_id = $db->lastInsertId('engine4_core_content');
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $page_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (empty($middle_content_id)) {
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'middle',
                        'page_id' => $page_id,
                        'parent_content_id' => $content_id,
                        'order' => 2,
                        'params' => ''
                    ));

                    $content_id = $db->lastInsertId('engine4_core_content');
                    if ($content_id) {
                        $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                    }
                }
            } else {
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $page_id)
                        ->where('parent_content_id =?', $top_content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (!empty($middle_content_id)) {
                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                }
            }

            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"sitepage_page","showContent_0":"","showContent_siteevent_event":"","showContent_sitepage_page":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","shareOptions"],"profile_like_button":"1","columnHeight":"400","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "sitepage.page-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
        }
    }

    if (!$coreSettings->getSetting('sitebusiness.layoutcreate', 0)) {
        $select = new Zend_Db_Select($db);
        $business_id = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitebusiness_index_view')
                ->query()
                ->fetchColumn();
        if ($business_id) {

            $top_content_id = $tableNameContent->select()
                    ->from($tableNameContentName, 'content_id')
                    ->where('page_id =?', $business_id)
                    ->where('name =?', 'top')
                    ->query()
                    ->fetchColumn();
            if (empty($top_content_id)) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $business_id,
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => ''
                ));
                $content_id = $db->lastInsertId('engine4_core_content');
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $business_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (empty($middle_content_id)) {
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'middle',
                        'page_id' => $business_id,
                        'parent_content_id' => $content_id,
                        'order' => 2,
                        'params' => ''
                    ));

                    $content_id = $db->lastInsertId('engine4_core_content');
                    if ($content_id) {
                        $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $business_id . '" LIMIT 1;');
                    }
                }
            } else {
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $business_id)
                        ->where('parent_content_id =?', $top_content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (!empty($middle_content_id)) {
                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $business_id . '" LIMIT 1;');
                }
            }

            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"sitebusiness_business","showContent_0":"","showContent_siteevent_event":"","showContent_sitebusiness_business":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","shareOptions"],"profile_like_button":"1","columnHeight":"400","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $business_id . '" LIMIT 1;');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "sitebusiness.business-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $business_id . '" LIMIT 1');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $business_id . '" LIMIT 1');
        }
    }

    if (!$coreSettings->getSetting('sitegroup.layoutcreate', 0)) {
        $select = new Zend_Db_Select($db);
        $group_id = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitegroup_index_view')
                ->query()
                ->fetchColumn();
        if ($group_id) {

            $top_content_id = $tableNameContent->select()
                    ->from($tableNameContentName, 'content_id')
                    ->where('page_id =?', $group_id)
                    ->where('name =?', 'top')
                    ->query()
                    ->fetchColumn();
            if (empty($top_content_id)) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $group_id,
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => ''
                ));
                $content_id = $db->lastInsertId('engine4_core_content');
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $group_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (empty($middle_content_id)) {
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'middle',
                        'page_id' => $group_id,
                        'parent_content_id' => $content_id,
                        'order' => 2,
                        'params' => ''
                    ));

                    $content_id = $db->lastInsertId('engine4_core_content');
                    if ($content_id) {
                        $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $group_id . '" LIMIT 1;');
                    }
                }
            } else {
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $group_id)
                        ->where('parent_content_id =?', $top_content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (!empty($middle_content_id)) {
                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $group_id . '" LIMIT 1;');
                }
            }

            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"sitegroup_group","showContent_0":"","showContent_siteevent_event":"","showContent_sitegroup_group":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","shareOptions"],"profile_like_button":"1","columnHeight":"400","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $group_id . '" LIMIT 1;');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "sitegroup.group-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $group_id . '" LIMIT 1');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $group_id . '" LIMIT 1');
        }
    }

    if (!$coreSettings->getSetting('sitestore.layoutcreate', 0)) {
        $select = new Zend_Db_Select($db);
        $store_id = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitestore_index_view')
                ->query()
                ->fetchColumn();
        if ($store_id) {

            $top_content_id = $tableNameContent->select()
                    ->from($tableNameContentName, 'content_id')
                    ->where('page_id =?', $store_id)
                    ->where('name =?', 'top')
                    ->query()
                    ->fetchColumn();
            if (empty($top_content_id)) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $store_id,
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => ''
                ));
                $content_id = $db->lastInsertId('engine4_core_content');
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $store_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (empty($middle_content_id)) {
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'middle',
                        'page_id' => $store_id,
                        'parent_content_id' => $content_id,
                        'order' => 2,
                        'params' => ''
                    ));

                    $content_id = $db->lastInsertId('engine4_core_content');
                    if ($content_id) {
                        $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $store_id . '" LIMIT 1;');
                    }
                }
            } else {
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $store_id)
                        ->where('parent_content_id =?', $top_content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (!empty($middle_content_id)) {
                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $store_id . '" LIMIT 1;');
                }
            }

            $db->query('UPDATE `engine4_core_content` SET `params` = \'{"modulename":"sitestore_store","showContent_0":"","showContent_siteevent_event":"","showContent_sitestore_store":["mainPhoto","title","followButton","likeCount","followCount","optionsButton","shareOptions"],"profile_like_button":"1","columnHeight":"400","contentFullWidth":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}\' WHERE `engine4_core_content`.`name` = "sitecontentcoverphoto.content-cover-photo" AND `engine4_core_content`.`page_id` = "' . $store_id . '" LIMIT 1;');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "sitestore.store-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $store_id . '" LIMIT 1');
            $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $store_id . '" LIMIT 1');
        }
    }
}
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'siteusercoverphoto')
        ->where('enabled = ?', 1);
$is_siteusercoverphotoobject = $select->query()->fetchObject();
if ($is_siteusercoverphotoobject) {
    $select = new Zend_Db_Select($db);
    $page_id = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'user_profile_index')
            ->query()
            ->fetchColumn();
    if ($page_id) {

        $top_content_id = $tableNameContent->select()
                ->from($tableNameContentName, 'content_id')
                ->where('page_id =?', $page_id)
                ->where('name =?', 'top')
                ->query()
                ->fetchColumn();
        if (empty($top_content_id)) {
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'parent_content_id' => null,
                'order' => 1,
                'params' => ''
            ));
            $content_id = $db->lastInsertId('engine4_core_content');
            $middle_content_id = $tableNameContent->select()
                    ->from($tableNameContentName, 'content_id')
                    ->where('page_id =?', $page_id)
                    ->where('parent_content_id =?', $content_id)
                    ->where('name =?', 'middle')
                    ->query()
                    ->fetchColumn();

            if (empty($middle_content_id)) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'middle',
                    'page_id' => $page_id,
                    'parent_content_id' => $content_id,
                    'order' => 2,
                    'params' => ''
                ));

                $content_id = $db->lastInsertId('engine4_core_content');
                if ($content_id) {
                    $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $content_id . '" WHERE `engine4_core_content`.`name` = "siteusercoverphoto.user-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
                }
            }
        } else {
            $middle_content_id = $tableNameContent->select()
                    ->from($tableNameContentName, 'content_id')
                    ->where('page_id =?', $page_id)
                    ->where('parent_content_id =?', $top_content_id)
                    ->where('name =?', 'middle')
                    ->query()
                    ->fetchColumn();

            if (!empty($middle_content_id)) {
                $db->query('UPDATE `engine4_core_content` SET `parent_content_id` =  "' . $middle_content_id . '" WHERE `engine4_core_content`.`name` = "siteusercoverphoto.user-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
            }
        }

        $db->query('UPDATE `engine4_core_content` SET `params` = \'{"title":"","titleCount":"","showContent":["mainPhoto","title","updateInfoButton","settingsButton","optionsButton","friendShipButton","composeMessageButton"],"profile_like_button":"1","columnHeight":"400","editFontColor":"0","nomobile":"0","name":"siteusercoverphoto.user-cover-photo"}\' WHERE `engine4_core_content`.`name` = "siteusercoverphoto.user-cover-photo" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1;');
        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "sitealbum.photo-strips" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
        $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "user.profile-status" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

        $db->query("UPDATE `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` = $page_id AND `engine4_core_content`.`name` = 'main' LIMIT 1 ;");
    }
}

$header_page_id = Engine_Api::_()->spectacular()->getWidgetizedPageId(array('name' => 'header'));
$main_content_id = $tableNameContent->select()
        ->from($tableNameContent->info('name'), 'content_id')
        ->where('name =?', 'main')
        ->where('page_id =?', $header_page_id)
        ->query()
        ->fetchColumn();

if (!empty($main_content_id)) {
    $content_id = $tableNameContent->select()
            ->from($tableNameContent->info('name'), 'content_id')
            ->where('name =?', 'core.html-block')
            ->where('page_id =?', $header_page_id)
            ->where('params like (?)', '%jQuery.noConflict()%')->query()
            ->fetchColumn();

    if (!$content_id) {
        $db->insert('engine4_core_content', array(
            'type' => 'widget',
            'name' => 'core.html-block',
            'page_id' => $header_page_id,
            'parent_content_id' => $main_content_id,
            'order' => 1,
            'params' => '{"title":"","data":"<script type=\"text\/javascript\"> \r\nif(typeof(window.jQuery) !=  \"undefined\") {\r\njQuery.noConflict();\r\n}\r\n<\/script>","nomobile":"0","name":"core.html-block"}'
        ));
    }
}    