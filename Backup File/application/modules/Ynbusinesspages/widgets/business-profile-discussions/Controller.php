<?php

class Ynbusinesspages_Widget_BusinessProfileDiscussionsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('ynbusinesspages_business');
    if (!$subject -> isViewable() || !$subject -> getPackage() -> checkAvailableModule('ynbusinesspages_topic')) {
        return $this -> setNoRender();
    }

    // Get paginator
    $table = Engine_Api::_()->getItemTable('ynbusinesspages_topic');
    $select = $table->select()
      ->where('business_id = ?', $subject->getIdentity())
      ->order('sticky DESC')
      ->order('modified_date DESC');
      ;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show and not viewer
    if( $paginator->getTotalItemCount() <= 0 && !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }
	$this->getElement()->removeDecorator('Title');

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}