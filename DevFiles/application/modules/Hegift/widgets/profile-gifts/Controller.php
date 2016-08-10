<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 05.04.12 11:54 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Widget_ProfileGiftsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Recipients
     * @var $subject User_Model_User
     */

    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      if (!($user = Engine_Api::_()->getItem('user', $this->_getParam('user_id', 0)))) {
        return $this->setNoRender();
      }
      Engine_Api::_()->core()->setSubject($user);
    }

    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Member type
    $subject = Engine_Api::_()->core()->getSubject();
    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');

    $ipp = $this->_getParam('itemCountPerPage', 20);
    $page = $this->_getParam('page', 1);
    $this->view->paginator = $paginator = $table->getPaginator(array('user_id' => $subject->getIdentity(), 'action_name' => 'received', 'page' => $page, 'ipp' => $ipp));
    $this->view->storage = Engine_Api::_()->storage();

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

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
