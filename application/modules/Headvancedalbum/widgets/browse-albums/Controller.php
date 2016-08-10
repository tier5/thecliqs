<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2013-02-18 16:48:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_Widget_BrowseAlbumsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $params['user_id'] = $user_id;
    $params['category'] = 'recent';
    $params['search'] = 1;

    $this->view->currentDate = $currentDate = date('Y-m-d h:i:s');
    $this->view->filter = $params['filter'];

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumPaginator($params);
    $paginator->setItemCountPerPage(40);
    $paginator->setCurrentPageNumber(1);

    $this->view->albums = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumsByPaginator($paginator);

    $this->view->categories = $categories = Engine_Api::_()->getDbTable('categories', 'album')->getCategoriesAssoc($params);
  }
}