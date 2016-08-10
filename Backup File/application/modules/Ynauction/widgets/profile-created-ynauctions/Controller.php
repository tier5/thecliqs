<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: latest auctions
 * @author     Minh Nguyen
 */
class Ynauction_Widget_ProfileCreatedYnauctionsController extends Engine_Content_Widget_Abstract
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
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Get paginator
    $values['orderby'] = 'creation_date';
    $now = date('Y-m-d H:i:s'); 
    $values['where'] = "display_home = 1 AND approved = '1' AND start_time <= '$now' AND user_id = ".Engine_Api::_()->core()->getSubject()->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->ynauction()->getProductsPaginator($values);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); 
  }
  public function getChildCount()
  {
    return $this->_childCount;
  }
}
?>