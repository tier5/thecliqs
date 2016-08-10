<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_FavoritePagesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() != 'page') {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $this->view->showManage = false;
    if ($viewer->getIdentity() && $subject->isAdmin($viewer)) {
      $this->view->showManage = true;
    }

    $this->view->paginator = $paginator = Engine_Api::_()->page()->getFavoritePages($subject);
    $paginator->setItemCountPerPage(6);
    
    if (!$paginator->getTotalItemCount()) {
      return $this->setNoRender();
    }

    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }
}