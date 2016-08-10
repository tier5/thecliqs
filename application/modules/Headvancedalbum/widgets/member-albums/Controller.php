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

class Headvancedalbum_Widget_MemberAlbumsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $subject = null;
        $viewer = Engine_Api::_()->user()->getViewer();
        if (EnginE_Api::_()->core()->hasSubject('user')) {
            $subject = EnginE_Api::_()->core()->getSubject('user');
            $params['owner'] = $subject;
        } else {
            $params['owner'] = $viewer;
        }

        if(!$subject){
          return $this->setNoRender();
        }

        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        $paginator = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumPaginator($params);
        $paginator->setItemCountPerPage(40);
        $paginator->setCurrentPageNumber(1);
//
//        $new_paginator = array();
//        $i = 0;
//        foreach ($paginator as $album) {
//            if (Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {
//                $new_paginator[$i++] = $album;
//            }
//        }
//        $paginator = Zend_Paginator::factory($new_paginator);
//
//        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
//        $paginator->setCurrentPageNumber(1);

        $this->view->paginator = $paginator;
        $this->view->is_next = (int)(isset($paginator->getPages()->next));

        $this->view->albums = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumsByPaginator($paginator);

    }
}