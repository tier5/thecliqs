<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: detail Auctions
 * @author     Minh Nguyen
 */
class Ynauction_Widget_DetailYnauctionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $product = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($product, $viewer, 'view')->isValid()) return;
    $this->view->product = $product;
     // album material
      $this->view->album = $album = $product->getSingletonAlbum();
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $paginator->setItemCountPerPage(100);
      
    $this->view->owner = $product->getOwner();
    $this->view->viewer = $viewer;
    $this->view->user_id = $viewer->getIdentity();  
    // Load fields view helpers
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($product);
  }
}
