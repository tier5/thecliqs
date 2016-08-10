<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2013-01-21 16:48:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_Widget_PopularAlbumsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $albumss_count = $settings->getSetting('headvancedalbum.popular.albums.count', 10);
        $popularType = $this->_getParam('popularType', 'comment');
        $albumsCount = $this->_getParam('photosCount', $albumss_count);
        if (!in_array($popularType, array('comment', 'view', 'like'))) {
            $popularType = 'comment';
        }
        $popularCol = $popularType . '_count';

        $table = Engine_Api::_()->getItemTable('album');
        $select = $table->select()
            ->where('search = ?', true);

        $ids = array();
        if ($popularType == 'like') {
            $api = Engine_Api::_()->getApi('core', 'like');
            $item_type = 'album';
            $likes = $api->getMostLikedData($item_type, $albumsCount);
            foreach ($likes as $like) {
                $ids[] = $like['resource_id'];
            }
            $select->where('album_id in (' . implode(",", $ids) . ')');
            //$select->order();
        } else {
            $select->order($popularCol . ' DESC');
        }


        // Create new array filtering out private albums
        $viewer = Engine_Api::_()->user()->getViewer();
        $album_select = $select;
        $new_select = array();
        $i = 0;
        foreach ($album_select->getTable()->fetchAll($album_select) as $album) {
            if (Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {
                $new_select[$i++] = $album;
            }
        }

        $paginator = Zend_Paginator::factory($new_select);

        if ($paginator->getTotalItemCount() <= 3) {
            return $this->setNoRender();
        }

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($albumsCount);
        $paginator->setCurrentPageNumber(1);


        $this->view->paginator = $paginator;
    }
}
