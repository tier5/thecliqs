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

class Headvancedalbum_Widget_BrowsePhotosController extends Engine_Content_Widget_Abstract
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

      if(Engine_Api::_()->core()->hasSubject('user')) {
          $params['owner'] = Engine_Api::_()->core()->getSubject('user');
      }

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('photos', 'headvancedalbum')->getPhotosPaginator($params);

    $paginator->setItemCountPerPage(15);
    $paginator->setCurrentPageNumber(1);

    $this->view->categories = $categories = Engine_Api::_()->getDbTable('categories', 'album')->getCategoriesAssoc($params);
  }
}