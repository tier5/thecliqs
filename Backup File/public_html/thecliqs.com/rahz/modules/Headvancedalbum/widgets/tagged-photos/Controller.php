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

class Headvancedalbum_Widget_TaggedPhotosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $viewer = Engine_Api::_()->user()->getViewer();
        $params['order'] = 'recent';
        $params['type'] = 'tagged';

        if(Engine_Api::_()->core()->hasSubject('user')) {
            $params['owner'] = Engine_Api::_()->core()->getSubject('user');
        } else {
            $params['owner'] = $viewer;
        }

        $this->view->currentDate = $currentDate = date('Y-m-d h:i:s');
        $this->view->filter = $params['filter'];

        $photosTable = Engine_Api::_()->getDbTable('photos', 'headvancedalbum');
        $this->view->paginator = $paginator = $photosTable->getPhotosPaginator($params);

        $page_number = $params['page_number'];
        $paginator->setItemCountPerPage(40);
        $paginator->setCurrentPageNumber($page_number);
    }
}