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

class Headvancedalbum_Widget_PopularPhotosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $photos_count = $settings->getSetting('headvancedalbum.popular.photos.count', 10); // TODO
        $popularType = $this->_getParam('popularType', 'comment');
        $photosCount = $this->_getParam('photosCount', $photos_count);
        if (!in_array($popularType, array('comment', 'view', 'like'))) {
            $popularType = 'comment';
        }
        $popularCol = $popularType . '_count';

        // Get paginator
        $parentTable = Engine_Api::_()->getItemTable('album'); //new Headvancedalbum_Model_DbTable_Albums(); //
        $parentTableName = $parentTable->info('name');
        $table = Engine_Api::_()->getItemTable('album_photo'); //new Headvancedalbum_Model_DbTable_Photos(); //
        $tableName = $table->info('name');
        $select = $table->select()
            ->from($tableName)
            ->joinLeft($parentTableName, $parentTableName . '.album_id=' . $tableName . '.album_id', null)
            ->where($parentTableName . '.search = ?', true);


        if ($popularType == 'like') {
            $ids = array();

            $api = Engine_Api::_()->getApi('core', 'like');
            $item_type = 'album_photo';
            $likes = $api->getMostLikedData($item_type, $photosCount);
            foreach ($likes as $like) {
                $ids[] = $like['resource_id'];
            }
            $select->where($tableName . '.photo_id in (' . implode(",", $ids) . ')');
            //$select->order();
        } else {
            $select->order($popularCol . ' DESC');
        }

        // Create new array filtering out private albums
        $viewer = Engine_Api::_()->user()->getViewer();
        $photo_select = $select;
        $new_select = array();
        $i = 0;
        foreach ($photo_select->getTable()->fetchAll($photo_select) as $photo) {
            if (Engine_Api::_()->authorization()->isAllowed($photo, $viewer, 'view')) {
                $new_select[$i++] = $photo;
            }
        }

        $paginator = Zend_Paginator::factory($new_select);

        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($photosCount);
        $paginator->setCurrentPageNumber(1);

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 3) {
            return $this->setNoRender();
        }


        $this->view->paginator = $paginator;
    }
}
