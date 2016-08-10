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

class Headvancedalbum_Widget_FeaturedAlbumsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    $viewer = Engine_Api::_()->user()->getViewer();

    $params['featured'] = 1;
    $params['search'] = 1;

    $this->view->currentDate = $currentDate = date('Y-m-d h:i:s');
    $this->view->filter = $params['filter'];

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->albums_count = $albums_count = $settings->getSetting('headvancedalbum.featured.albums.count', 6);

    $albums_tbl = new Headvancedalbum_Model_DbTable_Albums();
    $albums = $albums_tbl->getAlbumPaginator($params);

    if ($albums->getTotalItemCount() <= 3) {
      return $this->setNoRender();
    }

    $albums->setItemCountPerPage($albums_count);
    $albums->setCurrentPageNumber(1);

    $this->view->paginator = $albums;
    $this->view->albums = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumsByPaginator($albums);

  }
}
