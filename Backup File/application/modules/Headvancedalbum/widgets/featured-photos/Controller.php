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

class Headvancedalbum_Widget_FeaturedPhotosController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $params['user_id'] = $user_id;
    $params['featured'] = 1;
    $params['search'] = 1;

    $this->view->currentDate = $currentDate = date('Y-m-d h:i:s');
    $this->view->filter = $params['filter'];

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $photos_count = $settings->getSetting('headvancedalbum.featured.photos.count', 10);

    $photos_tbl = Engine_Api::_()->getDbTable('photos', 'headvancedalbum');//new Headvancedalbum_Model_DbTable_Photos();



    $photos = $photos_tbl->getPhotoPaginator($params);


    if ($photos->getTotalItemCount() < 2) {
      return $this->setNoRender();
    }

    $photos->setItemCountPerPage($photos_count);
    $photos->setCurrentPageNumber(1);

    $this->view->paginator = $photos;
    $this->view->photos = $photos = Engine_Api::_()->getDbTable('photos', 'headvancedalbum')->getPhotosByPaginator($photos);


  }
}