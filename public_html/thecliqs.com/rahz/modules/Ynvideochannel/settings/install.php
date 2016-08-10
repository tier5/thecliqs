<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Installer extends Engine_Package_Installer_Module
{
    public function onInstall()
    {
        $this->_addUserProfileContent();
        $this->_addVideosPage();
        $this->_addVideosListingPage();
        $this->_addChannelsPage();
        $this->_addChannelsListingPage();
        $this->_addPlaylistsPage();
        $this->_addPlaylistsListingPage();
        $this->_addMyVideosPage();
        $this->_addFavoriteVideosPage();
        $this->_addMyChannelsPage();
        $this->_addMyPlaylistsPage();
        $this->_addSubscriptionsPage();
        $this->_addShareVideoPage();
        $this->_addEditVideoPage();
        $this->_addAddChannelPage();
        $this->_addFindChannelPage();
        $this->_addGetChannelPage();
        $this->_addEditChannelPage();
        $this->_addCreatePlaylistPage();
        $this->_addEditPlaylistPage();
        $this->_addVideoDetailPage();
        $this->_addChannelDetailPage();
        $this->_addPlaylistDetailPage();

        parent::onInstall();
    }

    protected function _insertWidgetToProfileContent($page_id, $name, $params, $order) {
        $db = $this -> getDb();
        $select = new Zend_Db_Select($db);
        $select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
        $container_id = $select -> query() -> fetchObject() -> content_id;

        // middle_id (will always be there)
        $select ->reset('where')-> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle');
        $middle_id = $select -> query() -> fetchObject() -> content_id;

        // tab_id (tab container) may not always be there
        $select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id);
        $tab_id = $select -> query() -> fetchObject();
        if ($tab_id && @$tab_id -> content_id) {
            $tab_id = $tab_id -> content_id;
        } else {
            $tab_id = null;
        }

        // tab on profile
        $db -> insert('engine4_core_content', array('page_id' => $page_id,
            'type' => 'widget',
            'name' => $name,
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => $order,
            'params' => $params,
        ));
    }
    protected function _addUserProfileContent()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // profile page
        $select->from('engine4_core_pages')->where('name = ?', 'user_profile_index')->limit(1);
        $page_id = $select->query()->fetchObject()->page_id;

        // ynvieochannel.profile-videos
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'ynvideochannel.profile-videos');
        $infoProfileFavoriteVideos = $select->query()->fetch();
        if (empty($infoProfileFavoriteVideos)) {
            $this->_insertWidgetToProfileContent($page_id, 'ynvideochannel.profile-videos', '{"title":"Video Channel","titleCount":true,"itemCountPerPage":6}', 99);
        }

        // ynvieochannel.profile-video-playlists
        $select ->reset('where')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'widget')
                ->where('name = ?', 'ynvideochannel.profile-video-playlists');
        $infoProfileFavoriteVideos = $select->query()->fetch();
        if (empty($infoProfileFavoriteVideos)) {
            $this->_insertWidgetToProfileContent($page_id, 'ynvideochannel.profile-video-playlists', '{"title":"Video Channel Playlists","titleCount":true,"itemCountPerPage":6}', 99);
        }
    }

    protected function _addVideosPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_index')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_index',
                'displayname' => 'YN - Video Channel - All Videos Page',
                'title' => 'All Videos',
                'description' => 'This is the home page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.featured-videos',
                'parent_content_id' => $middle_top_id,
                'order' => 2,
                'params' => '{"title":"Featured Videos","itemCountPerPage":"6"}'
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.popular-videos',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"Popular Videos","itemCountPerPage":"6"}',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"max":"6"}',
            ));
            $container_tab_id = $db->lastInsertId('engine4_core_content');

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            // widgets in the container tab
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.latest-videos',
                'parent_content_id' => $container_tab_id,
                'order' => 1,
                'params' => '{"title":"Latest","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-liked-videos',
                'parent_content_id' => $container_tab_id,
                'order' => 2,
                'params' => '{"title":"Most Liked","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-commented-videos',
                'parent_content_id' => $container_tab_id,
                'order' => 3,
                'params' => '{"title":"Most Commented","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-favorited-videos',
                'parent_content_id' => $container_tab_id,
                'order' => 4,
                'params' => '{"title":"Most Favorited","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.top-rated-videos',
                'parent_content_id' => $container_tab_id,
                'order' => 5,
                'params' => '{"title":"Top Rated","itemCountPerPage":"6"}',
            ));

            // right column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $right_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $right_id,
                'order' => 2,
                'params' => '{"type":"videos"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $right_id,
                'order' => 3,
                'params' => '{"title":"Categories","type":"videos"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.video-tags',
                'parent_content_id' => $right_id,
                'order' => 4,
                'params' => '{"title":"Tags","numberOfTags":"20"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.recommended-videos',
                'parent_content_id' => $right_id,
                'order' => 5,
                'params' => '{"title":"Recommended Videos","itemCountPerPage":"5"}',
            ));
        }
    }

    protected function _addVideosListingPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_browse-videos')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_browse-videos',
                'displayname' => 'YN - Video Channel - All Videos Listing Page',
                'title' => 'All Videos - Listing',
                'description' => 'This is videos listing page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-videos',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            // right column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $right_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $right_id,
                'order' => 2,
                'params' => '{"type":"videos"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $right_id,
                'order' => 3,
                'params' => '{"title":"Categories","type":"videos"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.video-tags',
                'parent_content_id' => $right_id,
                'order' => 4,
                'params' => '{"title":"Tags","numberOfTags":"20"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.recommended-videos',
                'parent_content_id' => $right_id,
                'order' => 5,
                'params' => '{"title":"Recommended Videos","itemCountPerPage":"5"}',
            ));
        }
    }

    protected function _addChannelsPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_channels')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_channels',
                'displayname' => 'YN - Video Channel - All Channels Page',
                'title' => 'All Channels',
                'description' => 'This is all channels page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.featured-channels',
                'parent_content_id' => $middle_top_id,
                'order' => 2,
                'params' => '{"title":"Featured Channels","itemCountPerPage":"6"}'
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"max":"6"}',
            ));
            $container_tab_id = $db->lastInsertId('engine4_core_content');

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // widgets in the container tab
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.latest-channels',
                'parent_content_id' => $container_tab_id,
                'order' => 1,
                'params' => '{"title":"Latest","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-subscribed-channels',
                'parent_content_id' => $container_tab_id,
                'order' => 2,
                'params' => '{"title":"Most Subscribed","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-liked-channels',
                'parent_content_id' => $container_tab_id,
                'order' => 3,
                'params' => '{"title":"Most Liked","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-commented-channels',
                'parent_content_id' => $container_tab_id,
                'order' => 4,
                'params' => '{"title":"Most Commented","itemCountPerPage":"6"}',
            ));

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '{"type":"channels"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"title":"Categories","type":"channels"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.channel-of-day',
                'parent_content_id' => $left_id,
                'order' => 4,
                'params' => '{"title":"Channel Of The Day"}',
            ));
        }
    }

    protected function _addChannelsListingPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_browse-channels')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_browse-channels',
                'displayname' => 'YN - Video Channel - All Channels Listing Page',
                'title' => 'All Channels',
                'description' => 'This is all channels listing page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-channels',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '{"type":"channels"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"title":"Categories","type":"channels"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.channel-of-day',
                'parent_content_id' => $left_id,
                'order' => 4,
                'params' => '{"title":"Channel Of The Day"}',
            ));
        }
    }

    protected function _addPlaylistsPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_playlists')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_playlists',
                'displayname' => 'YN - Video Channel - All Playlists Page',
                'title' => 'All Playlists',
                'description' => 'This is all playlists page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // widgets in the container tab
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.latest-playlists',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"title":"Latest Playlists","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-liked-playlists',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"title":"Most Liked Playlists","itemCountPerPage":"6"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.most-commented-playlists',
                'parent_content_id' => $middle_id,
                'order' => 4,
                'params' => '{"title":"Most Commented Playlists","itemCountPerPage":"6"}',
            ));

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.create-playlist-button',
                'parent_content_id' => $left_id,
                'order' => 1,
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '{"type":"playlists"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"title":"Categories","type":"playlists"}',
            ));
        }
    }

    protected function _addPlaylistsListingPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_browse-playlists')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_browse-playlists',
                'displayname' => 'YN - Video Channel - All Playlists Listing Page',
                'title' => 'All Playlists',
                'description' => 'This is all playlists listing page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-playlists',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.create-playlist-button',
                'parent_content_id' => $left_id,
                'order' => 1,
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '{"type":"playlists"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"title":"Categories","type":"playlists"}',
            ));
        }
    }

    protected function _addShareVideoPage()
    {
        $db = $this->getDb();

        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_index_share-video')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_index_share-video',
                'displayname' => 'YN - Video Channel - Share Video Page',
                'title' => 'Share a Video',
                'description' => 'This page allows video to be shared.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addEditVideoPage()
    {
        $db = $this->getDb();

        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_video_edit')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_video_edit',
                'displayname' => 'YN - Video Channel - Edit Video Page',
                'title' => 'Edit a Video',
                'description' => 'This page allows video to be edited.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addAddChannelPage()
    {
        $db = $this->getDb();

        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_index_add-channel')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_index_add-channel',
                'displayname' => 'YN - Video Channel - Add Channel Page',
                'title' => 'Add a Channel',
                'description' => 'This is the add a channel page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addGetChannelPage()
    {
        $db = $this->getDb();

        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_index_get-channel')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_index_get-channel',
                'displayname' => 'YN - Video Channel - Get Channel Page',
                'title' => 'Get Channel',
                'description' => 'This is the get channel page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addFindChannelPage()
    {
        $db = $this->getDb();

        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_index_find-channel')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_index_find-channel',
                'displayname' => 'YN - Video Channel - Find Channels Page',
                'title' => 'Find Channels',
                'description' => 'This is the get channel page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addEditChannelPage()
    {
        $db = $this->getDb();

        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_channel_edit')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_channel_edit',
                'displayname' => 'YN - Video Channel - Edit Channel Page',
                'title' => 'Edit a Channel',
                'description' => 'This is the edit a channel page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addMyVideosPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_manage-videos')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_manage-videos',
                'displayname' => 'YN - Video Channel - My Videos Page',
                'title' => 'My Videos',
                'description' => 'This is my videos page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // insert management menu for mobile view
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-my-videos',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"type":"videos"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 4,
                'params' => '{"title":"Categories","type":"videos"}',
            ));
        }
    }

    protected function _addFavoriteVideosPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_favorites')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_favorites',
                'displayname' => 'YN - Video Channel - Favorite Videos Page',
                'title' => 'Favorite Videos',
                'description' => 'This is favorite videos page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // insert management menu for mobile view
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-my-favorite-videos',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"type":"videos"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 4,
                'params' => '{"title":"Categories","type":"videos"}',
            ));
        }
    }

    protected function _addMyChannelsPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_manage-channels')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_manage-channels',
                'displayname' => 'YN - Video Channel - My Channels Page',
                'title' => 'My Channels',
                'description' => 'This is my channels page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // insert management menu for mobile view
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-my-channels',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"type":"channels"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 4,
                'params' => '{"title":"Categories","type":"channels"}',
            ));
        }
    }

    protected function _addSubscriptionsPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_subscriptions')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_subscriptions',
                'displayname' => 'YN - Video Channel - Subscriptions Page',
                'title' => 'Subscriptions',
                'description' => 'This is channel subscriptions page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-subscription-channels',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-creation',
                'parent_content_id' => $left_id,
                'order' => 2,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"type":"channels"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 4,
                'params' => '{"title":"Categories","type":"channels"}',
            ));
        }
    }

    protected function _addMyPlaylistsPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        // Check if it's already been placed
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_index_manage-playlists')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynvideochannel_index_manage-playlists',
                'displayname' => 'YN - Video Channel - My Playlists Page',
                'title' => 'My Playlists',
                'description' => 'This is my playlists page for the Video Channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns :middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 3,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            // insert management menu for mobile view
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-my-playlists',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"itemCountPerPage":"10"}',
            ));

            // insert right column
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu-management',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.create-playlist-button',
                'parent_content_id' => $left_id,
                'order' => 2,
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-search',
                'parent_content_id' => $left_id,
                'order' => 3,
                'params' => '{"type":"playlists"}',
            ));
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.categories',
                'parent_content_id' => $left_id,
                'order' => 4,
                'params' => '{"title":"Categories","type":"playlists"}',
            ));
        }
    }

    protected function _addCreatePlaylistPage()
    {
        $db = $this->getDb();
        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_index_create-playlist')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_index_create-playlist',
                'displayname' => 'YN - Video Channel - Create Playlist Page',
                'title' => 'Create New Playlist',
                'description' => 'This page allows playlist to be created.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addEditPlaylistPage()
    {
        $db = $this->getDb();
        // create page
        $page_id = $db->select()->from('engine4_core_pages', 'page_id')->where('name = ?', 'ynvideochannel_playlist_edit')->limit(1)->query()->fetchColumn();

        // insert if it doesn't exist yet
        if (!$page_id) {
            // Insert page
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_playlist_edit',
                'displayname' => 'YN - Video Channel - Edit Playlist Page',
                'title' => 'Edit Playlist',
                'description' => 'This page allows playlist to be edited.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();

            // Insert top
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => 1,
            ));
            $top_id = $db->lastInsertId();

            // Insert main
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();

            // Insert top-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_id,
            ));
            $top_middle_id = $db->lastInsertId();

            // Insert main-middle
            $db->insert('engine4_core_content', array('type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 2,
            ));
            $main_middle_id = $db->lastInsertId();

            // Insert menu
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));

            // Insert content
            $db->insert('engine4_core_content', array('type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
        }
    }

    protected function _addVideoDetailPage()
    {
        $db = $this->getDb();
        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_video_detail')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_video_detail',
                'displayname' => 'YN - Video Channel - Video Detail Page',
                'title' => 'Detail Video',
                'description' => 'This is the detail page for a video.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns : middle, left and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'left',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $left_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            // middle column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.comments',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '["[]"]',
            ));

            // right column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.user-other-videos',
                'parent_content_id' => $right_id,
                'order' => 1,

            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.same-channel-videos',
                'parent_content_id' => $right_id,
                'order' => 2,
                'params' => '{"title":"Videos in same Channel","itemCountPerPage":"5"}',
            ));

            // left column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.related-videos',
                'parent_content_id' => $left_id,
                'order' => 1,
                'params' => '{"title":"Related Videos","itemCountPerPage":"5","nomobile":"1","notablet":"1"}',
            ));
        }
    }

    protected function _addChannelDetailPage()
    {
        $db = $this->getDb();

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_channel_detail')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_channel_detail',
                'displayname' => 'YN - Video Channel - Channel Detail Page',
                'title' => 'Detail Channel',
                'description' => 'This is the detail page for a channel.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns : middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            // middle column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $middle_id,
                'order' => 1,
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.list-channel-videos',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"itemCountPerPage":"6"}',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.comments',
                'parent_content_id' => $middle_id,
                'order' => 3,
            ));

            // right column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.user-other-channels',
                'parent_content_id' => $right_id,
                'order' => 1,
                'params' => '{"itemCountPerPage":"5"}',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.related-channels',
                'parent_content_id' => $right_id,
                'order' => 2,
                'params' => '{"title":"Related Channels","itemCountPerPage":"5"}',
            ));
        }
    }

    protected function _addPlaylistDetailPage()
    {
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);

        // Check if it's already been placed
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_pages')->where('name = ?', 'ynvideochannel_playlist_detail')->limit(1);
        $info = $select->query()->fetch();

        if (empty($info)) {
            $db->insert('engine4_core_pages', array('name' => 'ynvideochannel_playlist_detail',
                'displayname' => 'YN - Video Channel - Playlist Detail Page',
                'title' => 'Detail Playlist',
                'description' => 'This is the detail page for a playlist.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');

            // containers
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'top',
                'parent_content_id' => null,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $middle_top_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.browse-menu',
                'parent_content_id' => $middle_top_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.detail-playlist-slideshow',
                'parent_content_id' => $middle_top_id,
                'order' => 2,
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'parent_content_id' => null,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $container_id = $db->lastInsertId('engine4_core_content');

            // insert columns : middle and right
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $container_id,
                'order' => 2,
                'params' => '["[]"]',
            ));
            $middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $container_id,
                'order' => 1,
                'params' => '["[]"]',
            ));
            $right_id = $db->lastInsertId('engine4_core_content');

            // middle column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.content',
                'parent_content_id' => $middle_id,
                'order' => 1,
                'params' => '["[]"]',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.detail-playlist-grid',
                'parent_content_id' => $middle_id,
                'order' => 2,
                'params' => '{"itemCountPerPage":"6"}',
            ));

            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.comments',
                'parent_content_id' => $middle_id,
                'order' => 3,
                'params' => '["[]"]',
            ));

            // right column content
            $db->insert('engine4_core_content', array('page_id' => $page_id,
                'type' => 'widget',
                'name' => 'ynvideochannel.user-other-playlists',
                'parent_content_id' => $right_id,
                'order' => 1,
                'params' => '',
            ));
        }
    }
}