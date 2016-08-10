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

class Page_Widget_ProfileAdminsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $page_id = $request->getParam('page_id');

    if( !Engine_Api::_()->core()->hasSubject() ) {
      if( !$page_id )
        return $this->setNoRender();
      else {
        $page = Engine_Api::_()->getItem('page', $page_id);
        Engine_Api::_()->core()->setSubject($page);
      }
    }


    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('page');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Prepare data
    $this->view->viewer = $viewer;
    $this->view->page = $page = Engine_Api::_()->core()->getSubject();

    /**
     * @var $paginator Zend_Paginator
     */
    $paginator = $page->getTeam();
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));


    // Do not render if nothing to show and no search
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    $currentPage = $request->getParam('page');
    if( !$currentPage ) {
      $currentPage = 1;
      $this->view->firstRequest = true;
    }

    $paginator->setCurrentPageNumber($currentPage);


    $this->view->admins = $paginator;

    $total = $paginator->getTotalItemCount();
    $perPage = $paginator->getItemCountPerPage();
    $pages =  ceil($total / $perPage);

    $this->view->totalPages = $pages;
    $this->view->currentPage = $currentPage;
    $this->view->perPage = $perPage;
  }
}