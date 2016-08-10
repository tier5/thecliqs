<?php

class Page_Widget_ProfileStaffController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction()
  {
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
    else
      $page = Engine_Api::_()->core()->getSubject('page');

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$page->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $this->view->page = $page;

    $this->view->is_super_admin = (bool) $page->isOwner(Engine_Api::_()->user()->getViewer());

    /**
     *@var $team Zend_Paginator
     */
    $team = $page->getTeam();

    if( $team->getTotalItemCount() < 0 ) {
      return $this->setNoRender();
    }

    $team->setCurrentPageNumber($request->getParam('page', 1));
    $team->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));

    $this->_childCount = $team->getTotalItemCount();
    $this->view->paginator = $team;
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}