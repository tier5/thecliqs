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
class Spectacular_Api_Core extends Core_Api_Abstract {

    /**
     * This function return the complete path of image, from the photo id.
     *
     * @param id: The photo id.
     * @param type: The type of photo required.
     * @return Image path.
     */
    public function displayPhoto($id, $type = 'thumb.profile') {
        if (empty($id)) {
            return null;
        }
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

    /**
     * Plugin which return the error, if Siteadmin not using correct version for the plugin.
     *
     */
    public function isModulesSupport() {
        $isSpectacularActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('spectacular.isActivate', 0);
        if (empty($isSpectacularActivate))
            return array();

        $modArray = array(
            'siteevent' => '4.8.8p3',
            'siteeventticket' => '4.8.8p3',
            'sitecontentcoverphoto' => '4.8.8p5',
            'siteusercoverphoto' => '4.8.8p4',
            'sitereview' => '4.8.8p1',
            'sitereviewlistingtype' => '4.8.8p1',
            'sitealbum' => '4.8.8p1',
            'sitemenu' => '4.8.8p3'
        );
        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $isModEnabled = Engine_Api::_()->hasModuleBootstrap($key);
            if (!empty($isModEnabled)) {
                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
                $isModSupport = strcasecmp($getModVersion->version, $value);
                if ($isModSupport < 0) {
                    $finalModules[] = $getModVersion->title;
                }
            }
        }
        return $finalModules;
    }

    public function checkNavigationWidgetExists() {
        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'header')
                ->limit(1)
                ->query()
                ->fetchColumn();
        $content_id = '';
        if (!empty($page_id)) {
            $content_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'spectacular.navigation')
                    ->where('page_id = ?', $page_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
        }

        return $content_id;
    }

    /**
     * Get Widgetized PageId
     * @param $params
     */
    public function getWidgetizedPageId($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $page_id = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'page_id')
                ->where('name =?', $params['name'])
                ->query()
                ->fetchColumn();
        return $page_id;
    }

    /**
     * Check Widget Exist
     * @param $params
     */
    public function checkWidgetExist($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $content_id = $tableNameContent->select()
                ->from($tableNameContent->info('name'), 'content_id')
                ->where('page_id =?', $this->getWidgetizedPageId(array('name' => 'core_index_index')))
                ->where('name =?', $params['name'])
                ->query()
                ->fetchColumn();
        return $content_id;
    }

    /**
     * Get Widgetized Page Layout Value
     * @param $params
     */
    public function getWidgetizedPageLayoutValue($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $select = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'layout');

        if (isset($params['name'])) {
            $select->where('name =?', $params['name']);
        }
        if (isset($params['page_id'])) {
            $select->where('page_id =?', $params['page_id']);
        }
        $layout = $select->query()
                ->fetchColumn();
        return $layout;
    }

    /**
     * Get language array
     *
     * @param string $page_url
     * @return array $localeMultiOptions
     */
    public function getLanguageArray() {

        //PREPARE LANGUAGE LIST
        $languageList = Zend_Registry::get('Zend_Translate')->getList();

        //PREPARE DEFAULT LANGUAGE
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }
        //INIT DEFAULT LOCAL
        $localeObject = Zend_Registry::get('Locale');
        $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        $localeMultiOptions = array();
        foreach ($languageList as $key) {
            $languageName = null;
            if (!empty($languages[$key])) {
                $languageName = $languages[$key];
            } else {
                $tmpLocale = new Zend_Locale($key);
                $region = $tmpLocale->getRegion();
                $language = $tmpLocale->getLanguage();
                if (!empty($languages[$language]) && !empty($territories[$region])) {
                    $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }

            if ($languageName) {
                $localeMultiOptions[$key] = $languageName;
            } else {
                $localeMultiOptions[$key] = Zend_Registry::get('Zend_Translate')->_('Unknown');
            }
        }
        $localeMultiOptions = array_merge(array(
            $defaultLanguage => $defaultLanguage
                ), $localeMultiOptions);
        return $localeMultiOptions;
    }

    public function getContentPageId($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $page_id = $tableNameContent->select()
                ->from($tableNameContent->info('name'), 'page_id')
                ->where('content_id =?', $params['content_id'])
                ->query()
                ->fetchColumn();
        return $page_id;
    }

    /**
     * Get Widgetized PageId
     * @param $params
     */
    public function getBackupHomePageId($params = array()) {
        //GET CORE CONTENT TABLE
        $tableNamePages = Engine_Api::_()->getDbtable('pages', 'core');
        $page_id = $tableNamePages->select()
                ->from($tableNamePages->info('name'), 'page_id')
                ->where('url =?', 'home')
                ->query()
                ->fetchColumn();
        return $page_id;
    }

    public function getBackupOfHomePage() {

        $page_id = $this->getWidgetizedPageId(array('name' => 'core_index_index'));
        $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableNameContentName = $tableNameContent->info('name');
        $db = Engine_Db_Table::getDefaultAdapter();

        //CHECK PAGE EXIST OR NOT
        $home_backup_page_id = $this->getBackupHomePageId();

        //CREATE PAGE
        if (empty($home_backup_page_id)) {
            $db->query("INSERT IGNORE INTO `engine4_core_pages` ( `name`, `displayname`, `url`, `title`, `description`, `keywords`, `custom`, `fragment`, `layout`, `levels`, `provides`, `view_count`, `search`) VALUES (NULL, 'Landing Page - Backup of Landing Page on Installation of Spectacular Theme', 'home', 'Backup of Landing Page on Installation of Spectacular Theme Plugin', '', '', '0', '0', '', NULL, NULL, '0', '0');");
        }

        //GET EXISTING PAGE ID
        $home_backup_page_id = $this->getBackupHomePageId();

        $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $home_backup_page_id");
        //GET MAIN CONTAINER WORK

        $select = $tableNameContent->select()
                ->from($tableNameContentName, '*')
                ->where('page_id =?', $page_id)
                ->where('name =?', 'main')
                ->where('type =?', 'container');

        $mainRow = $tableNameContent->fetchRow($select);

        if (!empty($mainRow)) {

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $home_backup_page_id,
                'parent_content_id' => null,
                'order' => $mainRow->order,
                'params' => $mainRow->params ? json_encode($mainRow->params) : ''
            ));
            $content_id = $db->lastInsertId('engine4_core_content');

            $results = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('name in (?)', array('left', 'middle', 'right'))
                    ->where('type =?', 'container')
                    ->where('parent_content_id =?', $mainRow->content_id)
                    ->query()
                    ->fetchAll();

            foreach ($results as $values) {
                $db->insert('engine4_core_content', array(
                    'type' => $values['type'],
                    'name' => $values['name'],
                    'page_id' => $home_backup_page_id,
                    'parent_content_id' => $content_id,
                    'order' => $values['order'],
                    'params' => $values['params']
                ));
            }

            //LEFT CONTAINER WIDGETS
            $select = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('parent_content_id =?', $mainRow->content_id)
                    ->where('name =?', 'left')
                    ->where('type =?', 'container');

            $leftRow = $tableNameContent->fetchRow($select);

            if (!empty($leftRow)) {
                $results = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $page_id)
                        ->where('parent_content_id =?', $leftRow->content_id)
                        ->where('type =?', 'widget')
                        ->query()
                        ->fetchAll();

                $select = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $home_backup_page_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'left')
                        ->where('type =?', 'container');

                $row = $tableNameContent->fetchRow($select);

                foreach ($results as $values) {
                    $db->insert('engine4_core_content', array(
                        'type' => $values['type'],
                        'name' => $values['name'],
                        'page_id' => $home_backup_page_id,
                        'parent_content_id' => $row->content_id,
                        'order' => $values['order'],
                        'params' => $values['params']
                    ));
                }
            }
            //END LEFT CONTAINER WIDGET
            //MIDDLE CONTAINER WIDGETS
            $select = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('parent_content_id =?', $mainRow->content_id)
                    ->where('name =?', 'middle')
                    ->where('type =?', 'container');

            $middleRow = $tableNameContent->fetchRow($select);

            if (!empty($middleRow)) {
                $results = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $page_id)
                        ->where('parent_content_id =?', $middleRow->content_id)
                        ->where('type =?', 'widget')
                        ->query()
                        ->fetchAll();

                $select = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $home_backup_page_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->where('type =?', 'container');

                $row = $tableNameContent->fetchRow($select);

                foreach ($results as $values) {
                    $db->insert('engine4_core_content', array(
                        'type' => $values['type'],
                        'name' => $values['name'],
                        'page_id' => $home_backup_page_id,
                        'parent_content_id' => $row->content_id,
                        'order' => $values['order'],
                        'params' => $values['params']
                    ));
                }
            }
            //END MIDDLE CONTAINER WIDGET
            //RIGHT CONTAINER WIDGETS
            $select = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('parent_content_id =?', $mainRow->content_id)
                    ->where('name =?', 'right')
                    ->where('type =?', 'container');

            $rightRow = $tableNameContent->fetchRow($select);

            if (!empty($rightRow)) {
                $results = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $page_id)
                        ->where('parent_content_id =?', $rightRow->content_id)
                        ->where('type =?', 'widget')
                        ->query()
                        ->fetchAll();

                $select = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $home_backup_page_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'right')
                        ->where('type =?', 'container');

                $row = $tableNameContent->fetchRow($select);

                foreach ($results as $values) {
                    $db->insert('engine4_core_content', array(
                        'type' => $values['type'],
                        'name' => $values['name'],
                        'page_id' => $home_backup_page_id,
                        'parent_content_id' => $row->content_id,
                        'order' => $values['order'],
                        'params' => $values['params']
                    ));
                }
            }
            //END RIGHT CONTAINER WIDGET
        }

        //TOP CONTAINER
        $select = $tableNameContent->select()
                ->from($tableNameContentName, '*')
                ->where('page_id =?', $page_id)
                ->where('name =?', 'top')
                ->where('type =?', 'container');

        $topRow = $tableNameContent->fetchRow($select);

        if (!empty($topRow)) {

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $home_backup_page_id,
                'parent_content_id' => null,
                'order' => $topRow->order,
                //'params' => json_encode($topRow->params)
                'params' => $topRow->params ? json_encode($topRow->params) : ''
            ));
            $content_id = $db->lastInsertId('engine4_core_content');

            $results = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('name in (?)', array('left', 'middle', 'right'))
                    ->where('type =?', 'container')
                    ->where('parent_content_id =?', $topRow->content_id)
                    ->query()
                    ->fetchAll();

            foreach ($results as $values) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => $values['name'],
                    'page_id' => $home_backup_page_id,
                    'parent_content_id' => $content_id,
                    'order' => $values['order'],
                    'params' => $values['params']
                ));
            }

            //MIDDLE CONTAINER WIDGETS
            $select = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('parent_content_id =?', $topRow->content_id)
                    ->where('name =?', 'middle')
                    ->where('type =?', 'container');

            $topMiddleRow = $tableNameContent->fetchRow($select);

            if (!empty($topMiddleRow)) {
                $results = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $page_id)
                        ->where('parent_content_id =?', $topMiddleRow->content_id)
                        ->where('type =?', 'widget')
                        ->query()
                        ->fetchAll();

                $select = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $home_backup_page_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->where('type =?', 'container');

                $row = $tableNameContent->fetchRow($select);

                foreach ($results as $values) {
                    $db->insert('engine4_core_content', array(
                        'type' => $values['type'],
                        'name' => $values['name'],
                        'page_id' => $home_backup_page_id,
                        'parent_content_id' => $row->content_id,
                        'order' => $values['order'],
                        'params' => $values['params']
                    ));
                }
            }
            //END MIDDLE CONTAINER WIDGET
        }


        //GET BOTTOM CONTAINER
        $select = $tableNameContent->select()
                ->from($tableNameContentName, '*')
                ->where('page_id =?', $page_id)
                ->where('name =?', 'bottom')
                ->where('type =?', 'container');

        $bottomRow = $tableNameContent->fetchRow($select);

        if (!empty($bottomRow)) {

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'bottom',
                'page_id' => $home_backup_page_id,
                'parent_content_id' => null,
                'order' => $bottomRow->order,
                'params' => $bottomRow->params ? json_encode($bottomRow->params) : ''
            ));
            $content_id = $db->lastInsertId('engine4_core_content');

            $results = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('name in (?)', array('left', 'middle', 'right'))
                    ->where('type =?', 'container')
                    ->where('parent_content_id =?', $bottomRow->content_id)
                    ->query()
                    ->fetchAll();

            foreach ($results as $values) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => $values['name'],
                    'page_id' => $home_backup_page_id,
                    'parent_content_id' => $content_id,
                    'order' => $values['order'],
                    'params' => $values['params']
                ));
            }

            //MIDDLE CONTAINER WIDGETS
            $select = $tableNameContent->select()
                    ->from($tableNameContentName, '*')
                    ->where('page_id =?', $page_id)
                    ->where('parent_content_id =?', $bottomRow->content_id)
                    ->where('name =?', 'middle')
                    ->where('type =?', 'container');

            $bottomMiddleRow = $tableNameContent->fetchRow($select);

            if (!empty($bottomMiddleRow)) {
                $results = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $page_id)
                        ->where('parent_content_id =?', $bottomMiddleRow->content_id)
                        ->where('type =?', 'widget')
                        ->query()
                        ->fetchAll();

                $select = $tableNameContent->select()
                        ->from($tableNameContentName, '*')
                        ->where('page_id =?', $home_backup_page_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->where('type =?', 'container');

                $row = $tableNameContent->fetchRow($select);

                foreach ($results as $values) {
                    $db->insert('engine4_core_content', array(
                        'type' => $values['type'],
                        'name' => $values['name'],
                        'page_id' => $home_backup_page_id,
                        'parent_content_id' => $row->content_id,
                        'order' => $values['order'],
                        'params' => $values['params']
                    ));
                }
            }
            //END MIDDLE CONTAINER WIDGET
        }
    }

    public function setDefaultLayout($obj) {

        Engine_Api::_()->spectacular()->getBackupOfHomePage();

        $db = Engine_Db_Table::getDefaultAdapter();
        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'core_index_index')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!empty($page_id) && !empty($obj) && !empty($obj['spectacular_landing_page_layout'])) {
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");
            $db->query("UPDATE `engine4_core_pages` SET  `layout` =  'default-simple' WHERE  `engine4_core_pages`.`page_id` = $page_id LIMIT 1 ;");

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 6,
            ));
            $main_middle_id = $db->lastInsertId();

            $isSitehomepagevideoModEnabled = Engine_Api::_()->hasModuleBootstrap('sitehomepagevideo');
            if ($isSitehomepagevideoModEnabled) {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'sitehomepagevideo.videos',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'params' => '{"showLogo":"1","logo":"","sitehomepagevideoSignupLoginLink":"1","sitehomepagevideoBrowseMenus":"1","max":"8","sitehomepagevideoFirstImprotantLink":"1","sitehomepagevideoFirstTitle":"Important Title & Link","sitehomepagevideoFirstUrl":"#","sitehomepagevideoHtmlTitle":"BRING PEOPLE TOGETHER","sitehomepagevideoHtmlDescription":"Create an event. Sell tickets online.","sitehomepagevideoHowItWorks":"1","sitehomepagevideoSignupLoginButton":"1","sitehomepagevideoSearchBox":"1","showNextLink":"0","title":"","nomobile":"0","name":"sitehomepagevideo.videos"}',
                    'order' => 3,
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'spectacular.images',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'params' => '{"showImages":"1","selectedImages":"","width":"","height":"700","speed":"5000","order":"2","showLogo":"1","logo":"","spectacularSignupLoginLink":"1","spectacularBrowseMenus":"1","max":"8","spectacularFirstImprotantLink":"1","spectacularFirstTitle":"Important Title & Link","spectacularFirstUrl":"#","spectacularHtmlTitle":"BRING PEOPLE TOGETHER","spectacularHtmlDescription":"Create an event. Sell tickets online.","spectacularHowItWorks":"1","spectacularSignupLoginButton":"0","spectacularSearchBox":"2","title":"","nomobile":"0","name":"spectacular.images"}',
                    'order' => 3,
                ));
            }

            if (Engine_Api::_()->hasModuleBootstrap('siteevent')) {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'siteevent.recently-popular-random-siteevent',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'params' => '{"title":"Popular Events","titleCount":"","layouts_views":["gridZZZview"],"ajaxTabs":["upcoming"],"showContent":["price","location"],"upcoming_order":"1","reviews_order":"2","popular_order":"3","featured_order":"4","sponosred_order":"5","joined_order":"6","columnWidth":"260","titleLink":"","eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"0","hidden_subcategory_id":"0","hidden_subsubcategory_id":"0","eventInfo":["endDate","location"],"showEventType":"all","defaultOrder":"gridZZZview","columnHeight":"325","month_order":"7","week_order":"8","weekend_order":"9","today_order":"10","titlePosition":"1","showViewMore":"0","limit":"8","truncationLocation":"50","truncationList":"600","truncationGrid":"90","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","shareOptions":"1","loaded_by_ajax":"1","nomobile":"0","name":"siteevent.recently-popular-random-siteevent"}',
                    'order' => 4,
                ));

                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'siteevent.categories-grid-view',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'params' => '{"title":"","titleCount":true,"showSubCategoriesCount":"5","showCount":"1","columnWidth":"234","columnHeight":"216","categoryCount":"8","nomobile":"0","name":"siteevent.categories-grid-view"}',
                    'order' => 5,
                ));
            }

            if (Engine_Api::_()->hasModuleBootstrap('sitegroup')) {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'sitemember.recent-popular-random-members',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'params' => '{"title":"Most Viewed Members","titleCount":true,"viewType":"gridview","viewtype":"vertical","columnWidth":"157","fea_spo":"","has_photo":"1","titlePosition":"0","viewtitletype":"horizontal","columnHeight":"157","orderby":"view_count","interval":"overall","links":"","memberInfo":"","customParams":"5","custom_field_title":"0","custom_field_heading":"0","itemCount":"7","truncation":"16","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"sitemember.recent-popular-random-members"}',
                    'order' => 6,
                ));
            }
        }
        $isSitemenuModEnabled = Engine_Api::_()->hasModuleBootstrap('sitemenu');
        $isAdvSearchModEnabled = Engine_Api::_()->hasModuleBootstrap('siteadvsearch');

        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'footer')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!empty($page_id) && !empty($obj) && !empty($obj['spectacular_landing_page_layout'])) {
            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = $page_id");

            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $main_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'spectacular.homepage-footertext',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 3,
            ));

            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'spectacular.menu-footer',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 4,
            ));

            if ($isSitemenuModEnabled) {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'sitemenu.menu-footer',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_id,
                    'params' => '{"sitemenu_is_language":"1","sitemenu_show_in_footer":"2","sitemenu_footer_search_width":"150","sitemenu_social_links":["facebooklink","twitterlink","pininterestlink","youtubelink","linkedinlink"],"facebook":null,"facebook_url":"http:\/\/www.facebook.com\/","facebook_default_icon":"application\/modules\/Sitemenu\/externals\/images\/facebook.png","facebook_hover_icon":"application\/modules\/Sitemenu\/externals\/images\/overfacebook.png","facebook_title":"Like us on Facebook","twitter":null,"twitter_url":"https:\/\/www.twitter.com\/","twitter_default_icon":"application\/modules\/Sitemenu\/externals\/images\/twitter.png","twitter_hover_icon":"application\/modules\/Sitemenu\/externals\/images\/overtwitter.png","twitter_title":"Follow us on Twitter","pinterest":null,"pinterest_url":"https:\/\/www.pinterest.com\/","pinterest_default_icon":"application\/modules\/Sitemenu\/externals\/images\/pinterest.png","pinterest_hover_icon":"application\/modules\/Sitemenu\/externals\/images\/overpinterest.png","pinterest_title":"Pinterest","youtube":null,"youtube_url":"http:\/\/www.youtube.com\/","youtube_default_icon":"application\/modules\/Sitemenu\/externals\/images\/youtube.png","youtube_hover_icon":"application\/modules\/Sitemenu\/externals\/images\/overyoutube.png","youtube_title":"YouTube","linkedin":null,"linkedin_url":"https:\/\/www.linkedin.com\/","linkedin_default_icon":"application\/modules\/Sitemenu\/externals\/images\/linkedin.png","linkedin_hover_icon":"application\/modules\/Sitemenu\/externals\/images\/overlinkedin.png","linkedin_title":"LinkedIn","title":"","nomobile":"0","name":"sitemenu.menu-footer"}',
                    'order' => 5,
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'core.menu-footer',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_id,
                    'order' => 5,
                ));
            }
        }

        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'header')
                ->limit(1)
                ->query()
                ->fetchColumn();
        if (!empty($page_id) && !empty($obj) && !empty($obj['spectacular_landing_page_layout'])) {

            if ($isAdvSearchModEnabled) {

                $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'spectacular.search-box' AND `engine4_core_content`.`page_id` ='$page_id'");
                $content_id = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'siteadvsearch.search-box')
                        ->where('page_id = ?', $page_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                if (!$content_id) {

                    $main_middle_id = $db->select()
                            ->from('engine4_core_content', 'content_id')
                            ->where('name = ?', 'main')
                            ->where('page_id = ?', $page_id)
                            ->limit(1)
                            ->query()
                            ->fetchColumn();

                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'siteadvsearch.search-box',
                        'page_id' => $page_id,
                        'parent_content_id' => $main_middle_id,
                        'order' => 3,
                        'params' => '{"title":"","titleCount":true,"advsearch_search_box_width":"275","nomobile":"0","name":"siteadvsearch.search-box"}'
                    ));
                }
            }

            $content_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'seaocore.browse-menu-main')
                    ->where('page_id = ?', $page_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$content_id) {
                $main_middle_id = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'main')
                        ->where('page_id = ?', $page_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'seaocore.browse-menu-main',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'order' => 4,
                ));
            }
            $content_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'core.menu-logo')
                    ->where('page_id = ?', $page_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            if ($content_id) {
                $db->query("UPDATE  `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` ='$page_id' AND `name` =  'core.menu-logo' LIMIT 1 ;");
            } else {
                $main_middle_id = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'main')
                        ->where('page_id = ?', $page_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'core.menu-logo',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'order' => 2,
                ));
            }

            $content_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'zephyrtheme.header-menu')
                    ->where('page_id = ?', $page_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            if ($content_id) {
                $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'zephyrtheme.header-menu' AND `engine4_core_content`.`page_id` ='$page_id'");
                $content_id = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'core.menu-mini')
                        ->where('page_id = ?', $page_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                if (!$content_id) {
                    $main_middle_id = $db->select()
                            ->from('engine4_core_content', 'content_id')
                            ->where('name = ?', 'main')
                            ->where('page_id = ?', $page_id)
                            ->limit(1)
                            ->query()
                            ->fetchColumn();
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'core.menu-mini',
                        'page_id' => $page_id,
                        'parent_content_id' => $main_middle_id,
                        'order' => 7,
                    ));
                }
            }

            $db->query("UPDATE  `engine4_core_content` SET `order` =  '7'  WHERE  `engine4_core_content`.`page_id` ='$page_id' AND `name` =  'core.menu-mini' LIMIT 1 ;");
            if ($isAdvSearchModEnabled) {
                $db->query('UPDATE  `engine4_core_content` SET `order` =  "7", `name` = "siteadvsearch.menu-mini", `params`= \'{"advsearch_search_width":"275","title":"","nomobile":"0","name":"siteadvsearch.menu-mini"}\' WHERE  `engine4_core_content`.`page_id` =' . $page_id . ' AND `name` =  "core.menu-mini" LIMIT 1 ;');
            }

            if (!empty($isSitemenuModEnabled)) {
                $db->query('UPDATE  `engine4_core_content` SET `order` =  "7", `name` = "sitemenu.menu-mini", `params`= \'{"sitemenu_on_logged_out":"1","sitemenu_show_icon":"1","no_of_updates":"10","sitemenu_show_in_mini_options":"0","search_position":"1","changeMyLocation":"0","showLocationBasedContent":"0","sitemenu_mini_search_width":"275","sitemenu_enable_login_lightbox":"1","sitemenu_enable_signup_lightbox":"1","title":"","nomobile":"0","name":"sitemenu.menu-mini"}\' WHERE  `engine4_core_content`.`page_id` =' . $page_id . ' AND (`name` =  "core.menu-mini" OR `name` =  "siteadvsearch.menu-mini") LIMIT 1 ;');
                $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'sitemenu.menu-main' AND `engine4_core_content`.`page_id` ='$page_id'");
            }

            if (!$isAdvSearchModEnabled) {
                $content_id = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'spectacular.search-box')
                        ->where('page_id = ?', $page_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

                if (!$content_id) {
                    $main_middle_id = $db->select()
                            ->from('engine4_core_content', 'content_id')
                            ->where('name = ?', 'main')
                            ->where('page_id = ?', $page_id)
                            ->limit(1)
                            ->query()
                            ->fetchColumn();
                    $db->insert('engine4_core_content', array(
                        'type' => 'widget',
                        'name' => 'spectacular.search-box',
                        'page_id' => $page_id,
                        'parent_content_id' => $main_middle_id,
                        'order' => 3,
                        'params' => '{"title":"","titleCount":true}'
                    ));
                    $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'siteadvsearch.search-box' AND `engine4_core_content`.`page_id` ='$page_id'");
                }
            }

            $db->query("DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = 'core.menu-main' AND `engine4_core_content`.`page_id` ='$page_id'");


            $content_id = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('name = ?', 'spectacular.navigation')
                    ->where('page_id = ?', $page_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
            if (!$content_id) {
                $main_middle_id = $db->select()
                        ->from('engine4_core_content', 'content_id')
                        ->where('name = ?', 'main')
                        ->where('page_id = ?', $page_id)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                $db->insert('engine4_core_content', array(
                    'type' => 'widget',
                    'name' => 'spectacular.navigation',
                    'page_id' => $page_id,
                    'parent_content_id' => $main_middle_id,
                    'order' => 9,
                ));
            }

            $member_home_page_id = Engine_Api::_()->spectacular()->getWidgetizedPageId(array('name' => 'user_index_home'));
            $tableNameContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableNameContentName = $tableNameContent->info('name');
            $top_content_id = $tableNameContent->select()
                    ->from($tableNameContentName, 'content_id')
                    ->where('page_id =?', $member_home_page_id)
                    ->where('name =?', 'top')
                    ->query()
                    ->fetchColumn();
            if (empty($top_content_id)) {
                $db->insert('engine4_core_content', array(
                    'type' => 'container',
                    'name' => 'top',
                    'page_id' => $member_home_page_id,
                    'parent_content_id' => null,
                    'order' => 1,
                    'params' => ''
                ));
                $content_id = $db->lastInsertId('engine4_core_content');
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $member_home_page_id)
                        ->where('parent_content_id =?', $content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (empty($middle_content_id)) {
                    $db->insert('engine4_core_content', array(
                        'type' => 'container',
                        'name' => 'middle',
                        'page_id' => $member_home_page_id,
                        'parent_content_id' => $content_id,
                        'order' => 2,
                        'params' => ''
                    ));

                    $content_id = $db->lastInsertId('engine4_core_content');

                    $middle_banner_id = $tableNameContent->select()
                            ->from($tableNameContentName, 'content_id')
                            ->where('page_id =?', $member_home_page_id)
                            ->where('parent_content_id =?', $content_id)
                            ->where('name =?', 'spectacular.banner-images')
                            ->query()
                            ->fetchColumn();
                    if (!$middle_banner_id) {
                        $db->insert('engine4_core_content', array(
                            'type' => 'widget',
                            'name' => 'spectacular.banner-images',
                            'page_id' => $member_home_page_id,
                            'parent_content_id' => $content_id,
                            'order' => 1,
                            'params' => '{"showBanners":"1","selectedBanners":"","width":"","height":"200","speed":"5000","order":"2","spectacularHtmlTitle":"Events & Groups that you\'d love","spectacularHtmlDescription":"Discover new events in your town, interact with other party-goers and share the fun!","title":"","nomobile":"0","name":"spectacular.banner-images"}'
                        ));
                    }
                }
                $db->query("UPDATE `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` = $member_home_page_id AND `engine4_core_content`.`name` = 'main' LIMIT 1 ;");
            } else {
                $middle_content_id = $tableNameContent->select()
                        ->from($tableNameContentName, 'content_id')
                        ->where('page_id =?', $member_home_page_id)
                        ->where('parent_content_id =?', $top_content_id)
                        ->where('name =?', 'middle')
                        ->query()
                        ->fetchColumn();

                if (!empty($middle_content_id)) {

                    $middle_banner_id = $tableNameContent->select()
                            ->from($tableNameContentName, 'content_id')
                            ->where('page_id =?', $member_home_page_id)
                            ->where('parent_content_id =?', $middle_content_id)
                            ->where('name =?', 'spectacular.banner-images')
                            ->query()
                            ->fetchColumn();

                    if (!$middle_banner_id) {
                        $db->insert('engine4_core_content', array(
                            'type' => 'widget',
                            'name' => 'spectacular.banner-images',
                            'page_id' => $member_home_page_id,
                            'parent_content_id' => $middle_content_id,
                            'order' => 1,
                            'params' => '{"showBanners":"1","selectedBanners":"","width":"","height":"200","speed":"5000","order":"2","spectacularHtmlTitle":"Events & Groups that you\'d love","spectacularHtmlDescription":"Discover new events in your town, interact with other party-goers and share the fun!","title":"","nomobile":"0","name":"spectacular.banner-images"}'
                        ));
                    }
                    $db->query("UPDATE `engine4_core_content` SET  `order` =  '2' WHERE  `engine4_core_content`.`page_id` = $member_home_page_id AND `engine4_core_content`.`name` = 'main' LIMIT 1;");
                }
            }
        }
    }

}
